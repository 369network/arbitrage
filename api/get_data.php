<?php
/**
 * 369Network API - Get Dashboard Data
 */

// Check authentication
require_once 'auth.php';
checkAPIAuth();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include config
require_once '../includes/config.php';

// Sample data (replace with database queries in production)
$data = [
    'clients' => [
        [
            'id' => 'USM001',
            'name' => 'Usmanbhai',
            'email' => 'usman@example.com',
            'status' => 'active',
            'totalRevenue' => 390059,
            'totalExpense' => 220900,
            'profit' => 169159,
            'revenueShare' => 50,
            'domains' => ['viralpvb.com', 'gahdi.com', 'azeembux.online', 'spookymilklife.org']
        ],
        [
            'id' => 'DIV001',
            'name' => 'Diversity Media',
            'email' => 'diversity@example.com',
            'status' => 'active',
            'totalRevenue' => 245000,
            'totalExpense' => 125000,
            'profit' => 120000,
            'revenueShare' => 50,
            'domains' => ['newsportal.com', 'viraltrends.net']
        ]
    ],
    'domains' => [],
    'transactions' => [],
    'summary' => [
        'totalRevenue' => 390059,
        'totalExpense' => 220900,
        'totalProfit' => 169159,
        'activeClients' => 2,
        'activeDomains' => 24
    ],
    'monthlyData' => [
        [
            'month' => 'September 2025',
            'revenue' => 121350,
            'expense' => 65000,
            'profit' => 56350,
            'networkShare' => 32500,
            'clientShare' => 32500
        ],
        [
            'month' => 'October 2025',
            'revenue' => 102602,
            'expense' => 58200,
            'profit' => 44402,
            'networkShare' => 29100,
            'clientShare' => 29250
        ],
        [
            'month' => 'November 2025',
            'revenue' => 67707,
            'expense' => 42200,
            'profit' => 25507,
            'networkShare' => 21100,
            'clientShare' => 21100
        ],
        [
            'month' => 'December 2025',
            'revenue' => 98400,
            'expense' => 55500,
            'profit' => 42900,
            'networkShare' => 30000,
            'clientShare' => 28000
        ]
    ]
];

echo json_encode($data);
