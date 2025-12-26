<?php
/**
 * 369Network API - Domains Management
 * Includes AdSense/AdX payment tracking with status colors
 */

// Check authentication
require_once 'auth.php';
checkAPIAuth();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getDomains();
        break;
    case 'POST':
        addDomain();
        break;
    case 'PUT':
        updateDomain();
        break;
    case 'DELETE':
        deleteDomain();
        break;
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}

/**
 * Domain Status Colors:
 * - active (green): Domain running, payments received normally
 * - pending (yellow): Waiting for payment (before 21st)
 * - hold (orange): Payment on hold by Google
 * - deducted (purple): Payment received with deductions
 * - disabled (red): Account disabled, no payment expected
 * - paused (gray): Domain temporarily paused
 */

function getDomains() {
    $clientId = $_GET['client_id'] ?? null;
    $month = $_GET['month'] ?? date('n');
    $year = $_GET['year'] ?? date('Y');
    
    try {
        $db = getDB();
        
        $query = "
            SELECT 
                d.*,
                c.client_code,
                u.name as client_name,
                COALESCE(ap.payment_status, 'pending') as adsense_status,
                ap.gross_amount,
                ap.deduction_amount,
                ap.net_amount,
                ap.expected_payment_date,
                ap.actual_payment_date,
                ap.deduction_reason,
                SUM(t.revenue) as total_revenue,
                SUM(t.expense) as total_expense,
                SUM(t.profit) as total_profit
            FROM domains d
            LEFT JOIN clients c ON d.client_id = c.id
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN adsense_payments ap ON d.id = ap.domain_id 
                AND ap.earning_month = ? AND ap.earning_year = ?
            LEFT JOIN transactions t ON d.id = t.domain_id
            WHERE 1=1
        ";
        
        $params = [$month, $year];
        
        if ($clientId) {
            $query .= " AND c.client_code = ?";
            $params[] = $clientId;
        }
        
        $query .= " GROUP BY d.id ORDER BY d.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $domains = $stmt->fetchAll();
        
        // Add status color mapping
        foreach ($domains as &$domain) {
            $domain['status_color'] = getStatusColor($domain['adsense_status']);
            $domain['status_label'] = getStatusLabel($domain['adsense_status']);
            
            // Calculate effective profit based on payment status
            $domain['effective_profit'] = calculateEffectiveProfit($domain);
        }
        
        jsonResponse(['success' => true, 'domains' => $domains]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function addDomain() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['domain']) || !isset($input['clientId'])) {
        jsonResponse(['error' => 'Domain name and client ID are required'], 400);
    }
    
    try {
        $db = getDB();
        
        // Get client internal ID
        $clientStmt = $db->prepare("SELECT id FROM clients WHERE client_code = ?");
        $clientStmt->execute([$input['clientId']]);
        $client = $clientStmt->fetch();
        
        if (!$client) {
            jsonResponse(['error' => 'Client not found'], 404);
        }
        
        $stmt = $db->prepare("
            INSERT INTO domains (client_id, domain_name, traffic_source, monetization, target_country, campaign_info, status)
            VALUES (?, ?, ?, ?, ?, ?, 'active')
        ");
        
        $stmt->execute([
            $client['id'],
            sanitize($input['domain']),
            $input['source'] ?? 'facebook',
            $input['monetization'] ?? 'adsense',
            $input['country'] ?? null,
            $input['campaign'] ?? null
        ]);
        
        $domainId = $db->lastInsertId();
        
        jsonResponse([
            'success' => true,
            'message' => 'Domain added successfully',
            'domain' => [
                'id' => $domainId,
                'name' => $input['domain'],
                'status' => 'active',
                'status_color' => '#10b981'
            ]
        ]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function updateDomain() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        jsonResponse(['error' => 'Domain ID is required'], 400);
    }
    
    try {
        $db = getDB();
        
        // Update domain
        $stmt = $db->prepare("
            UPDATE domains SET
                domain_name = COALESCE(?, domain_name),
                traffic_source = COALESCE(?, traffic_source),
                monetization = COALESCE(?, monetization),
                target_country = COALESCE(?, target_country),
                status = COALESCE(?, status),
                payment_status = COALESCE(?, payment_status),
                deduction_amount = COALESCE(?, deduction_amount),
                deduction_reason = COALESCE(?, deduction_reason),
                notes = COALESCE(?, notes),
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        
        $stmt->execute([
            $input['domain'] ?? null,
            $input['source'] ?? null,
            $input['monetization'] ?? null,
            $input['country'] ?? null,
            $input['status'] ?? null,
            $input['paymentStatus'] ?? null,
            $input['deductionAmount'] ?? null,
            $input['deductionReason'] ?? null,
            $input['notes'] ?? null,
            $input['id']
        ]);
        
        jsonResponse(['success' => true, 'message' => 'Domain updated successfully']);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function deleteDomain() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        jsonResponse(['error' => 'Domain ID is required'], 400);
    }
    
    try {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM domains WHERE id = ?");
        $stmt->execute([$input['id']]);
        
        jsonResponse(['success' => true, 'message' => 'Domain deleted successfully']);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

/**
 * Get status color based on payment status
 */
function getStatusColor($status) {
    $colors = [
        'active' => '#10b981',      // Green - All good
        'received' => '#10b981',    // Green - Payment received
        'pending' => '#f59e0b',     // Yellow - Waiting for payment
        'hold' => '#f97316',        // Orange - Payment on hold
        'deducted' => '#8b5cf6',    // Purple - Deductions applied
        'account_disabled' => '#ef4444', // Red - Account disabled
        'not_received' => '#ef4444', // Red - Payment not received
        'paused' => '#6b7280',      // Gray - Paused
        'inactive' => '#6b7280'     // Gray - Inactive
    ];
    
    return $colors[$status] ?? '#6b7280';
}

/**
 * Get status label
 */
function getStatusLabel($status) {
    $labels = [
        'active' => 'Active',
        'received' => 'Paid',
        'pending' => 'Pending (21st)',
        'hold' => 'On Hold',
        'deducted' => 'Deducted',
        'account_disabled' => 'Disabled',
        'not_received' => 'Not Received',
        'paused' => 'Paused',
        'inactive' => 'Inactive'
    ];
    
    return $labels[$status] ?? 'Unknown';
}

/**
 * Calculate effective profit based on payment status
 * Only count profit if payment is actually received
 */
function calculateEffectiveProfit($domain) {
    $status = $domain['adsense_status'];
    $profit = $domain['total_profit'] ?? 0;
    $deduction = $domain['deduction_amount'] ?? 0;
    
    switch ($status) {
        case 'received':
        case 'active':
            // Full profit
            return $profit;
            
        case 'deducted':
            // Profit minus deductions
            return $profit - $deduction;
            
        case 'hold':
        case 'pending':
            // Potential profit (not confirmed yet)
            return 0; // Or return $profit with a "pending" flag
            
        case 'account_disabled':
        case 'not_received':
            // No profit - payment won't come
            return 0;
            
        default:
            return $profit;
    }
}
