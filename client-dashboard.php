<?php
// Check authentication - Client only
require_once 'auth/check.php';
checkClient();

// Get current user info
$user = getCurrentUser();
$clientId = getClientId();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - 369Network</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Client-specific overrides */
        .sidebar { background: linear-gradient(180deg, #1a1a25 0%, #12121a 100%); }
        .logo-icon { background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 50%, #c084fc 100%); }
        .logo-text h1 { background: linear-gradient(135deg, #8b5cf6 0%, #c084fc 100%); -webkit-background-clip: text; background-clip: text; }
        
        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.15) 0%, rgba(168, 85, 247, 0.1) 100%);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: var(--radius-xl);
            padding: 32px;
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.2) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .welcome-content h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        
        .welcome-content p {
            color: var(--text-secondary);
            margin-bottom: 16px;
        }
        
        .welcome-stats {
            display: flex;
            gap: 32px;
        }
        
        .welcome-stat {
            text-align: center;
        }
        
        .welcome-stat-value {
            font-size: 32px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
        }
        
        .welcome-stat-value.positive { color: var(--accent-success); }
        
        .welcome-stat-label {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Statement Card */
        .statement-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .statement-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-subtle);
        }

        .statement-period {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .statement-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .statement-row:last-child {
            border-bottom: none;
        }

        .statement-row.total {
            background: rgba(99, 102, 241, 0.05);
            font-weight: 600;
        }

        .statement-label {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .statement-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profit-breakdown {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-top: 24px;
        }

        .breakdown-card {
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
            padding: 20px;
            text-align: center;
        }

        .breakdown-value {
            font-size: 28px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            margin-bottom: 8px;
        }

        .breakdown-label {
            font-size: 14px;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Client Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">369</div>
                    <div class="logo-text">
                        <h1>369Network</h1>
                        <span>Client Portal</span>
                    </div>
                </div>
            </div>

            <nav class="nav-menu">
                <div class="nav-section">
                    <div class="nav-section-title">Overview</div>
                    <a href="#" class="nav-item active" data-page="overview">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="9"/>
                            <rect x="14" y="3" width="7" height="5"/>
                            <rect x="14" y="12" width="7" height="9"/>
                            <rect x="3" y="16" width="7" height="5"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="#" class="nav-item" data-page="myDomains">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                        </svg>
                        My Domains
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Financial</div>
                    <a href="#" class="nav-item" data-page="earnings">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                        Earnings
                    </a>
                    <a href="#" class="nav-item" data-page="statements">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                        Statements
                    </a>
                    <a href="#" class="nav-item" data-page="payouts">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        Payouts
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Account</div>
                    <a href="#" class="nav-item" data-page="profile">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Profile
                    </a>
                    <a href="#" class="nav-item" data-page="support">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        Support
                    </a>
                    <a href="auth/logout.php" class="nav-item" style="color: #ef4444;">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Logout
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                    <div class="user-info">
                        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        <span>Client #<?php echo htmlspecialchars($clientId); ?></span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Overview Page -->
            <div id="overview" class="page-content active">
                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-content">
                        <h1>Welcome back, <?php echo htmlspecialchars($user['name']); ?>! 👋</h1>
                        <p>Here's your partnership performance with 369Network</p>
                        <span class="status-badge active"><span class="status-dot"></span>Active Partner</span>
                    </div>
                    <div class="welcome-stats">
                        <div class="welcome-stat">
                            <div class="welcome-stat-value positive">$1,69,159</div>
                            <div class="welcome-stat-label">Total Earnings</div>
                        </div>
                        <div class="welcome-stat">
                            <div class="welcome-stat-value">50%</div>
                            <div class="welcome-stat-label">Revenue Share</div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon revenue">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="23,6 13.5,15.5 8.5,10.5 1,18"/>
                                </svg>
                            </div>
                            <div class="stat-trend up">+45%</div>
                        </div>
                        <div class="stat-value">$98,400</div>
                        <div class="stat-label">December Revenue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon profit">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"/>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                            </div>
                            <div class="stat-trend up">+68%</div>
                        </div>
                        <div class="stat-value">$42,900</div>
                        <div class="stat-label">December Profit</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23"/>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value">$21,450</div>
                        <div class="stat-label">Your Share (50%)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon clients">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value">8</div>
                        <div class="stat-label">Active Domains</div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="charts-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Earnings Overview</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="clientRevenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Monthly Breakdown</h3>
                        </div>
                        <div class="card-body">
                            <div class="statement-row">
                                <div class="statement-label">
                                    <span>September 2025</span>
                                </div>
                                <span class="money positive">$32,500</span>
                            </div>
                            <div class="statement-row">
                                <div class="statement-label">
                                    <span>October 2025</span>
                                </div>
                                <span class="money positive">$29,250</span>
                            </div>
                            <div class="statement-row">
                                <div class="statement-label">
                                    <span>November 2025</span>
                                </div>
                                <span class="money positive">$21,100</span>
                            </div>
                            <div class="statement-row">
                                <div class="statement-label">
                                    <span>December 2025</span>
                                </div>
                                <span class="money positive">$28,000</span>
                            </div>
                            <div class="statement-row total">
                                <div class="statement-label">
                                    <span>Total Paid</span>
                                </div>
                                <span class="money positive">$1,10,850</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Domains Performance -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Your Domains Performance</h3>
                        <button class="btn btn-sm btn-secondary">View All</button>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Domain</th>
                                        <th>Source</th>
                                        <th>Revenue</th>
                                        <th>Expense</th>
                                        <th>Your Share</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="domain-cell">
                                                <div class="domain-favicon">M</div>
                                                <div>
                                                    <div class="domain-name">mahatmapost.com</div>
                                                    <div class="domain-source">Adsense</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="source-tag adsense">AdSense</span></td>
                                        <td class="money positive">$35,700</td>
                                        <td class="money negative">$19,100</td>
                                        <td class="money positive">$8,300</td>
                                        <td><span class="status-badge active"><span class="status-dot"></span>Active</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="domain-cell">
                                                <div class="domain-favicon">A</div>
                                                <div>
                                                    <div class="domain-name">azeembux.online</div>
                                                    <div class="domain-source">Adsense</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="source-tag adsense">AdSense</span></td>
                                        <td class="money positive">$30,700</td>
                                        <td class="money negative">$16,900</td>
                                        <td class="money positive">$6,900</td>
                                        <td><span class="status-badge active"><span class="status-dot"></span>Active</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="domain-cell">
                                                <div class="domain-favicon">S</div>
                                                <div>
                                                    <div class="domain-name">spookymilklife.org</div>
                                                    <div class="domain-source">Facebook Ads</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="source-tag facebook">Facebook</span></td>
                                        <td class="money positive">$27,447</td>
                                        <td class="money negative">$15,500</td>
                                        <td class="money positive">$5,973</td>
                                        <td><span class="status-badge active"><span class="status-dot"></span>Active</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="domain-cell">
                                                <div class="domain-favicon">S</div>
                                                <div>
                                                    <div class="domain-name">Spoofy-2</div>
                                                    <div class="domain-source">Adsense</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="source-tag adsense">AdSense</span></td>
                                        <td class="money positive">$16,000</td>
                                        <td class="money negative">$9,200</td>
                                        <td class="money positive">$3,400</td>
                                        <td><span class="status-badge active"><span class="status-dot"></span>Active</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Domains Page -->
            <div id="myDomains" class="page-content">
                <header class="page-header">
                    <div class="page-title">
                        <h2>My Domains</h2>
                        <p>All domains in your partnership with 369Network</p>
                    </div>
                </header>
                <div class="card">
                    <div class="card-body" style="padding: 0;">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Domain</th>
                                        <th>Traffic Source</th>
                                        <th>Monetization</th>
                                        <th>Total Revenue</th>
                                        <th>Total Expense</th>
                                        <th>Net Profit</th>
                                        <th>Your Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><div class="domain-cell"><div class="domain-favicon">V</div><div><div class="domain-name">viralpvb.com</div></div></div></td>
                                        <td><span class="source-tag facebook">Facebook</span></td>
                                        <td><span class="source-tag adx">AdX</span></td>
                                        <td class="money positive">$8,370</td>
                                        <td class="money negative">$4,750</td>
                                        <td class="money positive">$3,620</td>
                                        <td class="money positive">$1,810</td>
                                    </tr>
                                    <tr>
                                        <td><div class="domain-cell"><div class="domain-favicon">G</div><div><div class="domain-name">gahdi.com</div></div></div></td>
                                        <td><span class="source-tag facebook">Facebook</span></td>
                                        <td><span class="source-tag adsense">AdSense</span></td>
                                        <td class="money positive">$17,689</td>
                                        <td class="money negative">$11,825</td>
                                        <td class="money positive">$5,864</td>
                                        <td class="money positive">$2,932</td>
                                    </tr>
                                    <tr>
                                        <td><div class="domain-cell"><div class="domain-favicon">A</div><div><div class="domain-name">azeembux.online</div></div></div></td>
                                        <td><span class="source-tag facebook">Facebook</span></td>
                                        <td><span class="source-tag adsense">AdSense</span></td>
                                        <td class="money positive">$61,358</td>
                                        <td class="money negative">$33,500</td>
                                        <td class="money positive">$27,858</td>
                                        <td class="money positive">$13,929</td>
                                    </tr>
                                    <tr>
                                        <td><div class="domain-cell"><div class="domain-favicon">S</div><div><div class="domain-name">spookymilklife.org</div></div></div></td>
                                        <td><span class="source-tag facebook">Facebook</span></td>
                                        <td><span class="source-tag adx">AdX</span></td>
                                        <td class="money positive">$76,447</td>
                                        <td class="money negative">$44,000</td>
                                        <td class="money positive">$32,447</td>
                                        <td class="money positive">$16,223</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statements Page -->
            <div id="statements" class="page-content">
                <header class="page-header">
                    <div class="page-title">
                        <h2>Monthly Statements</h2>
                        <p>Detailed breakdown of your earnings and shares</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-secondary" data-export="pdf">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Download PDF
                        </button>
                    </div>
                </header>

                <div class="statement-card" style="margin-bottom: 24px;">
                    <div class="statement-header">
                        <h3>December 2025 Statement</h3>
                        <span class="status-badge active"><span class="status-dot"></span>Current</span>
                    </div>
                    <div class="statement-row">
                        <div class="statement-label">
                            <div class="statement-icon" style="background: rgba(16, 185, 129, 0.15); color: var(--accent-success);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23,6 13.5,15.5 8.5,10.5 1,18"/></svg>
                            </div>
                            <span>Total Revenue (All Domains)</span>
                        </div>
                        <span class="money positive">$98,400</span>
                    </div>
                    <div class="statement-row">
                        <div class="statement-label">
                            <div class="statement-icon" style="background: rgba(239, 68, 68, 0.15); color: var(--accent-danger);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23,18 13.5,8.5 8.5,13.5 1,6"/></svg>
                            </div>
                            <span>Total Ad Spend (FB, Google, etc.)</span>
                        </div>
                        <span class="money negative">-$55,500</span>
                    </div>
                    <div class="statement-row">
                        <div class="statement-label">
                            <div class="statement-icon" style="background: rgba(99, 102, 241, 0.15); color: var(--accent-primary);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            </div>
                            <span>Net Profit</span>
                        </div>
                        <span class="money positive">$42,900</span>
                    </div>
                    <div class="statement-row total">
                        <div class="statement-label">
                            <span style="font-weight: 600;">Your Share (50%)</span>
                        </div>
                        <span class="money positive" style="font-size: 20px;">$21,450</span>
                    </div>
                    <div class="profit-breakdown">
                        <div class="breakdown-card">
                            <div class="breakdown-value" style="color: var(--accent-primary);">$21,450</div>
                            <div class="breakdown-label">369Network Share</div>
                        </div>
                        <div class="breakdown-card">
                            <div class="breakdown-value positive">$21,450</div>
                            <div class="breakdown-label">Your Share</div>
                        </div>
                    </div>
                </div>

                <div class="statement-card">
                    <div class="statement-header">
                        <h3>Historical Statements</h3>
                    </div>
                    <div class="statement-row">
                        <div class="statement-label"><span>September 2025</span></div>
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <span class="money positive">$32,500</span>
                            <span class="status-badge active"><span class="status-dot"></span>Paid</span>
                        </div>
                    </div>
                    <div class="statement-row">
                        <div class="statement-label"><span>October 2025</span></div>
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <span class="money positive">$29,250</span>
                            <span class="status-badge active"><span class="status-dot"></span>Paid</span>
                        </div>
                    </div>
                    <div class="statement-row">
                        <div class="statement-label"><span>November 2025</span></div>
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <span class="money positive">$21,100</span>
                            <span class="status-badge active"><span class="status-dot"></span>Paid</span>
                        </div>
                    </div>
                    <div class="statement-row">
                        <div class="statement-label"><span>December 2025</span></div>
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <span class="money positive">$28,000</span>
                            <span class="status-badge pending"><span class="status-dot"></span>Pending</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payouts Page -->
            <div id="payouts" class="page-content">
                <header class="page-header">
                    <div class="page-title">
                        <h2>Payout History</h2>
                        <p>Track all payments received from 369Network</p>
                    </div>
                </header>
                <div class="card">
                    <div class="card-body" style="padding: 0;">
                        <div id="clientPayoutsHistory">
                            <div class="payment-item">
                                <div class="payment-info">
                                    <div class="payment-icon incoming">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                                    </div>
                                    <div class="payment-details">
                                        <h4>September 2025 Share</h4>
                                        <span>27 Sep 2025 • Bank Transfer</span>
                                    </div>
                                </div>
                                <div class="payment-amount positive">+$32,500</div>
                            </div>
                            <div class="payment-item">
                                <div class="payment-info">
                                    <div class="payment-icon incoming">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                                    </div>
                                    <div class="payment-details">
                                        <h4>October 2025 Share</h4>
                                        <span>31 Oct 2025 • Bank Transfer</span>
                                    </div>
                                </div>
                                <div class="payment-amount positive">+$29,250</div>
                            </div>
                            <div class="payment-item">
                                <div class="payment-info">
                                    <div class="payment-icon incoming">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                                    </div>
                                    <div class="payment-details">
                                        <h4>November 2025 Share</h4>
                                        <span>30 Nov 2025 • Bank Transfer</span>
                                    </div>
                                </div>
                                <div class="payment-amount positive">+$21,100</div>
                            </div>
                            <div class="payment-item">
                                <div class="payment-info">
                                    <div class="payment-icon incoming">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                                    </div>
                                    <div class="payment-details">
                                        <h4>December 2025 Share</h4>
                                        <span>26 Dec 2025 • Bank Transfer</span>
                                    </div>
                                </div>
                                <div class="payment-amount positive">+$28,000</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other pages -->
            <div id="earnings" class="page-content">
                <header class="page-header"><div class="page-title"><h2>Earnings Analytics</h2></div></header>
            </div>
            <div id="profile" class="page-content">
                <header class="page-header"><div class="page-title"><h2>Profile Settings</h2></div></header>
            </div>
            <div id="support" class="page-content">
                <header class="page-header"><div class="page-title"><h2>Support</h2></div></header>
            </div>
        </main>
    </div>

    <div id="toastContainer" class="toast-container"></div>

    <script src="js/app.js"></script>
    <script>
        // Client-specific chart
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('clientRevenueChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [
                            {
                                label: 'Your Earnings',
                                data: [32500, 29250, 21100, 28000],
                                backgroundColor: '#8b5cf6',
                                borderRadius: 8,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (context) => '$' + context.raw.toLocaleString()
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#64748b' }
                            },
                            y: {
                                grid: { color: 'rgba(255,255,255,0.05)' },
                                ticks: {
                                    color: '#64748b',
                                    callback: (value) => '$' + (value / 1000) + 'K'
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
