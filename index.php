<?php
// Check authentication - Admin only
require_once 'auth/check.php';
checkAdmin();

// Get current user info
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>369Network - Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <!-- All Data Page -->
            <div id="all" class="page-content">
                <header class="page-header">
                    <div class="page-title">
                        <h2>Complete Overview</h2>
                        <p>All data across all sections - Revenue, Expenses, Profit, Clients, Domains & Analytics</p>
                    </div>
                    <div class="header-actions">
                        <div class="month-selector">
                            <button class="month-btn prev"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15,18 9,12 15,6"/></svg></button>
                            <div class="month-display">December 2025</div>
                            <button class="month-btn next"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"/></svg></button>
                        </div>
                        <button class="btn btn-secondary" data-export="csv">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Export All
                        </button>
                    </div>
                </header>

                <!-- All Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon revenue"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23,6 13.5,15.5 8.5,10.5 1,18"/><polyline points="17,6 23,6 23,12"/></svg></div>
                            <div class="stat-trend up"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18,15 12,9 6,15"/></svg>+12.5%</div>
                        </div>
                        <div class="stat-value" data-stat="revenue">$3,90,059</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon expense"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23,18 13.5,8.5 8.5,13.5 1,6"/><polyline points="17,18 23,18 23,12"/></svg></div>
                            <div class="stat-trend down"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6,9 12,15 18,9"/></svg>-5.2%</div>
                        </div>
                        <div class="stat-value" data-stat="expense">$2,20,900</div>
                        <div class="stat-label">Total Expenses</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon profit"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                            <div class="stat-trend up"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18,15 12,9 6,15"/></svg>+23.1%</div>
                        </div>
                        <div class="stat-value" data-stat="profit">$1,69,159</div>
                        <div class="stat-label">Net Profit</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon clients"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                            <div class="stat-trend up"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18,15 12,9 6,15"/></svg>+2</div>
                        </div>
                        <div class="stat-value" data-stat="clients">2</div>
                        <div class="stat-label">Active Clients</div>
                    </div>
                </div>

                <!-- Revenue Breakdown -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><svg class="card-title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>Revenue Breakdown</h3>
                    </div>
                    <div class="card-body">
                        <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
                            <div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(52,168,83,0.15);color:#34a853;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg></div></div><div class="stat-value">$1,45,000</div><div class="stat-label">AdSense Revenue</div></div>
                            <div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(66,133,244,0.15);color:#4285f4;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg></div></div><div class="stat-value">$1,85,000</div><div class="stat-label">AdX Revenue</div></div>
                            <div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(139,92,246,0.15);color:#8b5cf6;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg></div></div><div class="stat-value">$60,059</div><div class="stat-label">Other Revenue</div></div>
                        </div>
                    </div>
                </div>

                <!-- Expense Breakdown -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><svg class="card-title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>Expense Breakdown</h3>
                    </div>
                    <div class="card-body">
                        <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
                            <div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(24,119,242,0.15);color:#1877f2;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></div></div><div class="stat-value">$1,40,000</div><div class="stat-label">Facebook Ads</div></div>
                            <div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(234,67,53,0.15);color:#ea4335;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg></div></div><div class="stat-value">$45,000</div><div class="stat-label">Google Ads</div></div>
                            <div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(139,92,246,0.15);color:#8b5cf6;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/></svg></div></div><div class="stat-value">$35,900</div><div class="stat-label">Native & Other</div></div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="charts-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><svg class="card-title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>Revenue & Expenses Trend</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container"><canvas id="revenueChart"></canvas></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><svg class="card-title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>Traffic Sources</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container"><canvas id="sourceChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <!-- All Domains & Clients -->
                <div class="grid-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><svg class="card-title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/></svg>All Domains</h3>
                        </div>
                        <div class="card-body" style="padding:0;">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead><tr><th>Domain</th><th>Source</th><th>Revenue</th><th>Expense</th><th>Profit</th><th>Status</th></tr></thead>
                                    <tbody id="allDomainsTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><svg class="card-title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>All Clients</h3>
                        </div>
                        <div class="card-body">
                            <div id="clientsGrid" class="clients-grid"></div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><svg class="card-title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>Complete Payment History</h3>
                    </div>
                    <div class="card-body">
                        <div id="paymentsHistory"></div>
                    </div>
                </div>
            </div>

            <div id="dashboard" class="page-content active">
                <header class="page-header">
                    <div class="page-title">
                        <h2>Dashboard Overview</h2>
                        <p>Welcome back! Here's your business performance for December 2025 <span style="color:#64748b;font-size:12px;margin-left:10px;">(Click on any stat card to filter)</span></p>
                    </div>
                    <div class="header-actions">
                        <div class="month-selector">
                            <button class="month-btn prev"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15,18 9,12 15,6"/></svg></button>
                            <div class="month-display">December 2025</div>
                            <button class="month-btn next"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9,18 15,12 9,6"/></svg></button>
                        </div>
                        <button class="btn btn-secondary" data-export="csv">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Export
                        </button>
                        <button class="btn btn-primary" data-modal="addDomainModal">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Add Domain
                        </button>
                    </div>
                </header>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon revenue"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23,6 13.5,15.5 8.5,10.5 1,18"/><polyline points="17,6 23,6 23,12"/></svg></div>
                            <div class="stat-trend up"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18,15 12,9 6,15"/></svg>+12.5%</div>
                        </div>
                        <div class="stat-value" data-stat="revenue">$3,90,059</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon expense"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23,18 13.5,8.5 8.5,13.5 1,6"/><polyline points="17,18 23,18 23,12"/></svg></div>
                            <div class="stat-trend down"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6,9 12,15 18,9"/></svg>-5.2%</div>
                        </div>
                        <div class="stat-value" data-stat="expense">$2,20,900</div>
                        <div class="stat-label">Total Expenses</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon profit"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                            <div class="stat-trend up"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18,15 12,9 6,15"/></svg>+23.1%</div>
                        </div>
                        <div class="stat-value" data-stat="profit">$1,69,159</div>
                        <div class="stat-label">Net Profit</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon clients"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                            <div class="stat-trend up"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18,15 12,9 6,15"/></svg>+2</div>
                        </div>
                        <div class="stat-value" data-stat="clients">2</div>
                        <div class="stat-label">Active Clients</div>
                    </div>
                </div>

                <div class="charts-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><svg class="card-title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>Revenue & Expenses Trend</h3>
                            <div class="tabs" style="background:transparent;padding:0;margin:0;">
                                <button class="tab active" style="padding:8px 12px;flex:0;">Monthly</button>
                                <button class="tab" style="padding:8px 12px;flex:0;">Weekly</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container"><canvas id="revenueChart"></canvas></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><svg class="card-title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>Traffic Sources</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container"><canvas id="sourceChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><svg class="card-title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/></svg>Top Performing Domains</h3>
                            <button class="btn btn-sm btn-secondary">View All</button>
                        </div>
                        <div class="card-body" style="padding:0;">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead><tr><th>Domain</th><th>Revenue</th><th>Profit</th><th>Status</th></tr></thead>
                                    <tbody>
                                        <tr><td><div class="domain-cell"><div class="domain-favicon">M</div><div><div class="domain-name">mahatmapost.com</div><div class="domain-source">Adsense</div></div></div></td><td class="money positive">$35,700</td><td class="money positive">$16,600</td><td><span class="status-badge active"><span class="status-dot"></span>Active</span></td></tr>
                                        <tr><td><div class="domain-cell"><div class="domain-favicon">A</div><div><div class="domain-name">azeembux.online</div><div class="domain-source">Adsense</div></div></div></td><td class="money positive">$30,700</td><td class="money positive">$13,800</td><td><span class="status-badge active"><span class="status-dot"></span>Active</span></td></tr>
                                        <tr><td><div class="domain-cell"><div class="domain-favicon">S</div><div><div class="domain-name">spookymilklife.org</div><div class="domain-source">Facebook</div></div></div></td><td class="money positive">$27,447</td><td class="money positive">$11,947</td><td><span class="status-badge active"><span class="status-dot"></span>Active</span></td></tr>
                                        <tr><td><div class="domain-cell"><div class="domain-favicon">N</div><div><div class="domain-name">Newsclicks24</div><div class="domain-source">369-Adsense</div></div></div></td><td class="money positive">$17,000</td><td class="money positive">$8,000</td><td><span class="status-badge active"><span class="status-dot"></span>Active</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><svg class="card-title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>Recent Payments</h3>
                            <button class="btn btn-sm btn-secondary">View All</button>
                        </div>
                        <div class="card-body" style="padding:0;">
                            <div id="paymentsHistory"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clients Page -->
            <div id="clients" class="page-content">
                <header class="page-header">
                    <div class="page-title"><h2>Client Management</h2><p>Manage your arbitration clients and their domains</p></div>
                    <div class="header-actions">
                        <input type="text" id="searchInput" class="form-input" placeholder="Search clients..." style="width:250px;">
                        <button class="btn btn-primary" data-modal="addClientModal"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Add Client</button>
                    </div>
                </header>
                <div id="clientsGrid" class="clients-grid"></div>
            </div>

            <!-- Domains Page -->
            <div id="domains" class="page-content">
                <header class="page-header">
                    <div class="page-title"><h2>Domain Management</h2><p>Track all your domains and their performance</p></div>
                    <div class="header-actions">
                        <select class="form-select" style="width:180px;"><option>All Sources</option><option>Facebook</option><option>Google Ads</option><option>TikTok</option><option>Native</option><option>AdSense</option></select>
                        <button class="btn btn-primary" data-modal="addDomainModal"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Add Domain</button>
                    </div>
                </header>
                <div class="card">
                    <div class="card-body" style="padding:0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead><tr><th>Domain</th><th>Source</th><th>Revenue</th><th>Expense</th><th>Profit</th><th>Status</th><th>Action</th></tr></thead>
                                <tbody id="domainsTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Page -->
            <div id="revenue" class="page-content">
                <header class="page-header"><div class="page-title"><h2>Revenue Analytics</h2><p>Detailed breakdown of your revenue streams</p></div></header>
                <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
                    <div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(52,168,83,0.15);color:#34a853;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg></div></div><div class="stat-value">$1,45,000</div><div class="stat-label">AdSense Revenue</div></div>
                    <div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(66,133,244,0.15);color:#4285f4;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg></div></div><div class="stat-value">$1,85,000</div><div class="stat-label">AdX Revenue</div></div>
                    <div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(139,92,246,0.15);color:#8b5cf6;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg></div></div><div class="stat-value">$60,059</div><div class="stat-label">Other Revenue</div></div>
                </div>
                <div class="card"><div class="card-header"><h3 class="card-title">Revenue Share Distribution (50/50)</h3></div><div class="card-body"><div class="chart-container"><canvas id="profitChart"></canvas></div></div></div>
            </div>

            <!-- Payments Page -->
            <div id="payments" class="page-content">
                <header class="page-header"><div class="page-title"><h2>Payment History</h2><p>Track all payments between 369Network and clients</p></div><div class="header-actions"><button class="btn btn-primary" data-modal="addPaymentModal"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Record Payment</button></div></header>
                <div class="grid-2">
                    <div class="card"><div class="card-header"><h3 class="card-title">369Network Payments</h3></div><div class="card-body"><div class="stat-value positive">$1,12,700</div><div class="stat-label">Total Paid (Sep-Dec 2025)</div><div class="mt-4"><div class="payment-item"><div class="payment-info"><div class="payment-icon incoming"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg></div><div class="payment-details"><h4>September Share</h4><span>27 Sep 2025</span></div></div><div class="payment-amount positive">$32,500</div></div><div class="payment-item"><div class="payment-info"><div class="payment-icon incoming"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg></div><div class="payment-details"><h4>October Share</h4><span>31 Oct 2025</span></div></div><div class="payment-amount positive">$29,100</div></div><div class="payment-item"><div class="payment-info"><div class="payment-icon incoming"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg></div><div class="payment-details"><h4>November Share</h4><span>30 Nov 2025</span></div></div><div class="payment-amount positive">$21,100</div></div><div class="payment-item"><div class="payment-info"><div class="payment-icon incoming"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg></div><div class="payment-details"><h4>December Share</h4><span>26 Dec 2025</span></div></div><div class="payment-amount positive">$30,000</div></div></div></div></div>
                    <div class="card"><div class="card-header"><h3 class="card-title">Client Payments (Usmanbhai)</h3></div><div class="card-body"><div class="stat-value positive">$1,10,850</div><div class="stat-label">Total Paid (Sep-Dec 2025)</div><div class="mt-4"><div class="payment-item"><div class="payment-info"><div class="payment-icon outgoing"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M19 12l-7 7-7-7"/></svg></div><div class="payment-details"><h4>September Share</h4><span>27 Sep 2025</span></div></div><div class="payment-amount negative">$32,500</div></div><div class="payment-item"><div class="payment-info"><div class="payment-icon outgoing"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M19 12l-7 7-7-7"/></svg></div><div class="payment-details"><h4>October Share</h4><span>31 Oct 2025</span></div></div><div class="payment-amount negative">$29,250</div></div><div class="payment-item"><div class="payment-info"><div class="payment-icon outgoing"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M19 12l-7 7-7-7"/></svg></div><div class="payment-details"><h4>November Share</h4><span>30 Nov 2025</span></div></div><div class="payment-amount negative">$21,100</div></div><div class="payment-item"><div class="payment-info"><div class="payment-icon outgoing"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M19 12l-7 7-7-7"/></svg></div><div class="payment-details"><h4>December Share</h4><span>26 Dec 2025</span></div></div><div class="payment-amount negative">$28,000</div></div></div></div></div>
                </div>
            </div>

            <!-- Expenses & Sources & Settings pages omitted for brevity but follow same pattern -->
            <div id="expenses" class="page-content"><header class="page-header"><div class="page-title"><h2>Expense Tracking</h2><p>Monitor your traffic acquisition costs</p></div></header><div class="stats-grid" style="grid-template-columns:repeat(3,1fr);"><div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(24,119,242,0.15);color:#1877f2;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></div></div><div class="stat-value">$1,40,000</div><div class="stat-label">Facebook Ads</div></div><div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(234,67,53,0.15);color:#ea4335;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg></div></div><div class="stat-value">$45,000</div><div class="stat-label">Google Ads</div></div><div class="stat-card"><div class="stat-header"><div class="stat-icon" style="background:rgba(139,92,246,0.15);color:#8b5cf6;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/></svg></div></div><div class="stat-value">$35,900</div><div class="stat-label">Native & Other</div></div></div></div>
            <div id="sources" class="page-content"><header class="page-header"><div class="page-title"><h2>Traffic Analytics</h2><p>Analyze traffic sources and their performance</p></div></header></div>
            <div id="settings" class="page-content"><header class="page-header"><div class="page-title"><h2>Settings</h2><p>Configure your dashboard preferences</p></div></header></div>
        </main>
    </div>

    <?php include 'includes/modals.php'; ?>
    <div id="toastContainer" class="toast-container"></div>
    <script src="js/app.js"></script>
</body>
</html>
