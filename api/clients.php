<?php
/**
 * 369Network API - Clients Management
 */

// Check authentication - Admin only
require_once 'auth.php';
checkAPIAdmin();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getClients();
        break;
    case 'POST':
        addClient();
        break;
    case 'PUT':
        updateClient();
        break;
    case 'DELETE':
        deleteClient();
        break;
    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}

function getClients() {
    try {
        $db = getDB();
        $stmt = $db->query("
            SELECT c.*, u.name, u.email, u.phone, u.status, u.revenue_share
            FROM clients c
            LEFT JOIN users u ON c.user_id = u.id
            ORDER BY c.created_at DESC
        ");
        $clients = $stmt->fetchAll();
        
        // Get domain count for each client
        foreach ($clients as &$client) {
            $domainStmt = $db->prepare("SELECT COUNT(*) FROM domains WHERE client_id = ?");
            $domainStmt->execute([$client['id']]);
            $client['domain_count'] = $domainStmt->fetchColumn();
        }
        
        jsonResponse(['success' => true, 'clients' => $clients]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function addClient() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['name']) || empty($input['name'])) {
        jsonResponse(['error' => 'Client name is required'], 400);
    }
    
    try {
        $db = getDB();
        $db->beginTransaction();
        
        // Create user account
        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $input['name']));
        $email = $input['email'] ?? $username . '@369network.client';
        $password = password_hash('changeme123', PASSWORD_DEFAULT);
        $revenueShare = $input['revenueShare'] ?? 50;
        
        $userStmt = $db->prepare("
            INSERT INTO users (username, email, password, role, name, phone, revenue_share, status)
            VALUES (?, ?, ?, 'client', ?, ?, ?, ?)
        ");
        $userStmt->execute([
            $username,
            $email,
            $password,
            sanitize($input['name']),
            $input['phone'] ?? null,
            $revenueShare,
            $input['status'] ?? 'active'
        ]);
        
        $userId = $db->lastInsertId();
        
        // Generate client code
        $clientCode = strtoupper(substr($input['name'], 0, 3)) . str_pad($userId, 3, '0', STR_PAD_LEFT);
        
        // Create client record
        $clientStmt = $db->prepare("
            INSERT INTO clients (user_id, client_code, company_name, notes)
            VALUES (?, ?, ?, ?)
        ");
        $clientStmt->execute([
            $userId,
            $clientCode,
            $input['company'] ?? null,
            $input['notes'] ?? null
        ]);
        
        $clientId = $db->lastInsertId();
        
        $db->commit();
        
        jsonResponse([
            'success' => true,
            'message' => 'Client added successfully',
            'client' => [
                'id' => $clientCode,
                'name' => $input['name'],
                'email' => $email,
                'status' => $input['status'] ?? 'active',
                'revenueShare' => $revenueShare,
                'totalRevenue' => 0,
                'totalExpense' => 0,
                'profit' => 0,
                'domains' => []
            ]
        ]);
    } catch (Exception $e) {
        if (isset($db)) $db->rollBack();
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function updateClient() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        jsonResponse(['error' => 'Client ID is required'], 400);
    }
    
    try {
        $db = getDB();
        
        $stmt = $db->prepare("
            UPDATE clients c
            JOIN users u ON c.user_id = u.id
            SET u.name = COALESCE(?, u.name),
                u.email = COALESCE(?, u.email),
                u.phone = COALESCE(?, u.phone),
                u.revenue_share = COALESCE(?, u.revenue_share),
                u.status = COALESCE(?, u.status),
                c.notes = COALESCE(?, c.notes)
            WHERE c.client_code = ?
        ");
        
        $stmt->execute([
            $input['name'] ?? null,
            $input['email'] ?? null,
            $input['phone'] ?? null,
            $input['revenueShare'] ?? null,
            $input['status'] ?? null,
            $input['notes'] ?? null,
            $input['id']
        ]);
        
        jsonResponse(['success' => true, 'message' => 'Client updated successfully']);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function deleteClient() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        jsonResponse(['error' => 'Client ID is required'], 400);
    }
    
    try {
        $db = getDB();
        
        $stmt = $db->prepare("DELETE FROM clients WHERE client_code = ?");
        $stmt->execute([$input['id']]);
        
        jsonResponse(['success' => true, 'message' => 'Client deleted successfully']);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}
