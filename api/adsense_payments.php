<?php
/**
 * 369Network API - AdSense/AdX Payment Tracking
 * Handles payment status updates with 21st date cycle
 */

// Check authentication
require_once 'auth.php';
checkAPIAuth();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

switch ($method) {
    case 'GET':
        if ($action === 'summary') {
            getPaymentSummary();
        } else {
            getPayments();
        }
        break;
    case 'POST':
        recordPayment();
        break;
    case 'PUT':
        updatePaymentStatus();
        break;
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}

/**
 * Get all AdSense payments with status
 */
function getPayments() {
    $month = $_GET['month'] ?? date('n');
    $year = $_GET['year'] ?? date('Y');
    $clientId = $_GET['client_id'] ?? null;
    
    try {
        $db = getDB();
        
        $query = "
            SELECT 
                ap.*,
                d.domain_name,
                d.monetization,
                c.client_code,
                u.name as client_name
            FROM adsense_payments ap
            JOIN domains d ON ap.domain_id = d.id
            JOIN clients c ON ap.client_id = c.id
            JOIN users u ON c.user_id = u.id
            WHERE ap.earning_month = ? AND ap.earning_year = ?
        ";
        
        $params = [$month, $year];
        
        if ($clientId) {
            $query .= " AND c.client_code = ?";
            $params[] = $clientId;
        }
        
        $query .= " ORDER BY d.domain_name";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $payments = $stmt->fetchAll();
        
        // Add computed fields
        foreach ($payments as &$payment) {
            $payment['status_color'] = getPaymentStatusColor($payment['payment_status']);
            $payment['expected_date'] = getExpectedPaymentDate($payment['earning_month'], $payment['earning_year']);
            $payment['is_overdue'] = isPaymentOverdue($payment);
        }
        
        jsonResponse(['success' => true, 'payments' => $payments]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

/**
 * Get payment summary with totals
 */
function getPaymentSummary() {
    $month = $_GET['month'] ?? date('n');
    $year = $_GET['year'] ?? date('Y');
    
    try {
        $db = getDB();
        
        $stmt = $db->prepare("
            SELECT 
                payment_status,
                COUNT(*) as count,
                SUM(gross_amount) as gross_total,
                SUM(deduction_amount) as deduction_total,
                SUM(gross_amount - deduction_amount) as net_total
            FROM adsense_payments
            WHERE earning_month = ? AND earning_year = ?
            GROUP BY payment_status
        ");
        
        $stmt->execute([$month, $year]);
        $summary = $stmt->fetchAll();
        
        // Calculate overall totals
        $totals = [
            'total_expected' => 0,
            'total_received' => 0,
            'total_pending' => 0,
            'total_hold' => 0,
            'total_lost' => 0,
            'total_deductions' => 0
        ];
        
        foreach ($summary as $row) {
            $totals['total_expected'] += $row['gross_total'];
            $totals['total_deductions'] += $row['deduction_total'];
            
            switch ($row['payment_status']) {
                case 'received':
                    $totals['total_received'] += $row['net_total'];
                    break;
                case 'pending':
                    $totals['total_pending'] += $row['net_total'];
                    break;
                case 'hold':
                    $totals['total_hold'] += $row['gross_total'];
                    break;
                case 'account_disabled':
                case 'not_received':
                    $totals['total_lost'] += $row['gross_total'];
                    break;
            }
        }
        
        jsonResponse([
            'success' => true,
            'summary' => $summary,
            'totals' => $totals,
            'month' => $month,
            'year' => $year
        ]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

/**
 * Record new AdSense payment entry
 */
function recordPayment() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $required = ['domain_id', 'earning_month', 'earning_year', 'gross_amount'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            jsonResponse(['error' => "$field is required"], 400);
        }
    }
    
    try {
        $db = getDB();
        
        // Get client_id from domain
        $domainStmt = $db->prepare("SELECT client_id FROM domains WHERE id = ?");
        $domainStmt->execute([$input['domain_id']]);
        $domain = $domainStmt->fetch();
        
        if (!$domain) {
            jsonResponse(['error' => 'Domain not found'], 404);
        }
        
        // Calculate expected payment date (21st of next month)
        $expectedDate = getExpectedPaymentDate($input['earning_month'], $input['earning_year']);
        
        $stmt = $db->prepare("
            INSERT INTO adsense_payments 
            (domain_id, client_id, earning_month, earning_year, gross_amount, 
             deduction_amount, expected_payment_date, payment_status, deduction_reason, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                gross_amount = VALUES(gross_amount),
                deduction_amount = VALUES(deduction_amount),
                payment_status = VALUES(payment_status),
                deduction_reason = VALUES(deduction_reason),
                notes = VALUES(notes),
                updated_at = CURRENT_TIMESTAMP
        ");
        
        $stmt->execute([
            $input['domain_id'],
            $domain['client_id'],
            $input['earning_month'],
            $input['earning_year'],
            $input['gross_amount'],
            $input['deduction_amount'] ?? 0,
            $expectedDate,
            $input['status'] ?? 'pending',
            $input['deduction_reason'] ?? null,
            $input['notes'] ?? null
        ]);
        
        jsonResponse(['success' => true, 'message' => 'Payment recorded successfully']);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

/**
 * Update payment status (received, hold, disabled, etc.)
 */
function updatePaymentStatus() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || !isset($input['status'])) {
        jsonResponse(['error' => 'Payment ID and status are required'], 400);
    }
    
    $validStatuses = ['pending', 'received', 'hold', 'deducted', 'account_disabled', 'not_received'];
    if (!in_array($input['status'], $validStatuses)) {
        jsonResponse(['error' => 'Invalid status'], 400);
    }
    
    try {
        $db = getDB();
        
        $stmt = $db->prepare("
            UPDATE adsense_payments SET
                payment_status = ?,
                actual_payment_date = ?,
                deduction_amount = COALESCE(?, deduction_amount),
                deduction_reason = COALESCE(?, deduction_reason),
                notes = COALESCE(?, notes),
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        
        $actualDate = null;
        if ($input['status'] === 'received' || $input['status'] === 'deducted') {
            $actualDate = $input['actual_date'] ?? date('Y-m-d');
        }
        
        $stmt->execute([
            $input['status'],
            $actualDate,
            $input['deduction_amount'] ?? null,
            $input['deduction_reason'] ?? null,
            $input['notes'] ?? null,
            $input['id']
        ]);
        
        // Recalculate client and monthly totals
        recalculateTotals($input['id']);
        
        jsonResponse(['success' => true, 'message' => 'Payment status updated']);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

/**
 * Helper: Get expected payment date (21st of next month)
 */
function getExpectedPaymentDate($earningMonth, $earningYear) {
    $nextMonth = $earningMonth + 1;
    $nextYear = $earningYear;
    
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }
    
    return sprintf('%04d-%02d-21', $nextYear, $nextMonth);
}

/**
 * Helper: Check if payment is overdue
 */
function isPaymentOverdue($payment) {
    if ($payment['payment_status'] !== 'pending') {
        return false;
    }
    
    $expectedDate = $payment['expected_payment_date'] ?? $payment['expected_date'];
    return strtotime($expectedDate) < strtotime('today');
}

/**
 * Helper: Get status color
 */
function getPaymentStatusColor($status) {
    $colors = [
        'received' => '#10b981',    // Green
        'pending' => '#f59e0b',     // Yellow  
        'hold' => '#f97316',        // Orange
        'deducted' => '#8b5cf6',    // Purple
        'account_disabled' => '#ef4444', // Red
        'not_received' => '#ef4444'  // Red
    ];
    
    return $colors[$status] ?? '#6b7280';
}

/**
 * Recalculate totals after status change
 */
function recalculateTotals($paymentId) {
    try {
        $db = getDB();
        
        // Get payment details
        $stmt = $db->prepare("
            SELECT client_id, earning_month, earning_year 
            FROM adsense_payments WHERE id = ?
        ");
        $stmt->execute([$paymentId]);
        $payment = $stmt->fetch();
        
        if (!$payment) return;
        
        // Recalculate monthly summary
        $summaryStmt = $db->prepare("
            INSERT INTO monthly_summary (client_id, month, year, total_revenue, total_expense, total_profit, network_share, client_share)
            SELECT 
                client_id,
                earning_month,
                earning_year,
                SUM(CASE WHEN payment_status IN ('received', 'deducted') THEN gross_amount - deduction_amount ELSE 0 END),
                0,
                SUM(CASE WHEN payment_status IN ('received', 'deducted') THEN gross_amount - deduction_amount ELSE 0 END),
                SUM(CASE WHEN payment_status IN ('received', 'deducted') THEN (gross_amount - deduction_amount) * 0.5 ELSE 0 END),
                SUM(CASE WHEN payment_status IN ('received', 'deducted') THEN (gross_amount - deduction_amount) * 0.5 ELSE 0 END)
            FROM adsense_payments
            WHERE client_id = ? AND earning_month = ? AND earning_year = ?
            GROUP BY client_id, earning_month, earning_year
            ON DUPLICATE KEY UPDATE
                total_revenue = VALUES(total_revenue),
                total_profit = VALUES(total_profit),
                network_share = VALUES(network_share),
                client_share = VALUES(client_share)
        ");
        
        $summaryStmt->execute([
            $payment['client_id'],
            $payment['earning_month'],
            $payment['earning_year']
        ]);
    } catch (Exception $e) {
        // Log error but don't fail the main operation
        error_log("Error recalculating totals: " . $e->getMessage());
    }
}
