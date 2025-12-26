// 369Network Dashboard - Main JavaScript
class Dashboard {
    constructor() {
        this.currentMonth = new Date().getMonth();
        this.currentYear = 2025;
        this.clients = [];
        this.domains = [];
        this.transactions = [];
        this.charts = {};
        this.activeFilter = null; // Track active section filter
        
        this.init();
    }

    init() {
        this.loadData();
        this.initNavigation();
        this.initModals();
        this.initCharts();
        this.initAnimations();
        this.initEventListeners();
    }

    // Data Management
    async loadData() {
        try {
            const response = await fetch('api/get_data.php');
            const data = await response.json();
            
            this.clients = data.clients || [];
            this.domains = data.domains || [];
            this.transactions = data.transactions || [];
            
            this.updateDashboard();
        } catch (error) {
            console.error('Error loading data:', error);
            this.loadSampleData();
        }
    }

    loadSampleData() {
        // Sample data based on the Excel structure
        this.clients = [
            {
                id: 'USM001',
                name: 'Usmanbhai',
                email: 'usman@example.com',
                status: 'active',
                totalRevenue: 390059,
                totalExpense: 220900,
                profit: 169159,
                revenueShare: 50,
                domains: ['viralpvb.com', 'gahdi.com', 'azeembux.online', 'spookymilklife.org']
            },
            {
                id: 'DIV001',
                name: 'Diversity Media',
                email: 'diversity@example.com',
                status: 'active',
                totalRevenue: 245000,
                totalExpense: 125000,
                profit: 120000,
                revenueShare: 50,
                domains: ['newsportal.com', 'viraltrends.net']
            }
        ];

        this.monthlyData = {
            'September 2025': {
                revenue: 121350,
                expense: 65000,
                profit: 56350,
                domains: [
                    { name: 'viralpvb.com', country: 'Spain-1pay-525', revenue: 8370, expense: 4750, source: 'facebook' },
                    { name: 'izsviral.com', country: 'Spain-1pay-525', revenue: 6900, expense: 3975, source: 'facebook' },
                    { name: 'toppistm.com', country: 'Spain-1pay-525', revenue: 6070, expense: 3600, source: 'facebook' },
                    { name: '369healtway.store', country: 'India-Pin-180', revenue: 9700, expense: 5030, source: 'facebook' },
                    { name: 'IPLbaba', country: 'Search', revenue: 11000, expense: 3535, source: 'search' },
                    { name: 'aikgq.shop', country: 'india-pin-verify-170', revenue: 9270, expense: 4805, source: 'facebook' },
                    { name: 'Newsclicks24', country: '369-Adsense', revenue: 17000, expense: 9000, source: 'adsense' },
                    { name: 'Aryahindi', country: '369-adsense', revenue: 11600, expense: 6000, source: 'adsense' }
                ],
                payments: {
                    network: 32500,
                    client: 32500
                }
            },
            'October 2025': {
                revenue: 102602,
                expense: 58200,
                profit: 44402,
                domains: [
                    { name: 'gahdi.com', country: 'Pay-rcv-usmn', revenue: 9919, expense: 7500, source: 'facebook' },
                    { name: 'azeembux.online', country: 'Pay-rcv-usmn', revenue: 20296, expense: 11000, source: 'facebook' },
                    { name: 'spookymilklife.org', country: 'Pay-rcv-usmn', revenue: 18661, expense: 9800, source: 'facebook' },
                    { name: 'spookymilklife.org-2', country: 'Pay-rcv-usmn', revenue: 19189, expense: 10000, source: 'facebook' },
                    { name: 'spookymilklife.org-3', country: 'Pay-rcv-usmn', revenue: 27447, expense: 15500, source: 'facebook' },
                    { name: 'goodloanmitra.site', country: 'Vivek-adsense', revenue: 5500, expense: 3000, source: 'adsense' }
                ],
                payments: {
                    network: 29100,
                    client: 29250
                }
            },
            'November 2025': {
                revenue: 67707,
                expense: 42200,
                profit: 25507,
                domains: [
                    { name: 'culturacoffee', country: 'MI', revenue: 4600, expense: 3600, source: 'native' },
                    { name: 'doramasvip.cam', country: 'Adsense', revenue: 13866, expense: 8100, source: 'adsense' },
                    { name: 'spookymilklife', country: 'MI', revenue: 7900, expense: 6400, source: 'native' },
                    { name: 'eyulo.com', country: 'search', revenue: 9191, expense: 6200, source: 'search' },
                    { name: 'mahatmapost.com', country: 'Adsense-QA1', revenue: 8534, expense: 4500, source: 'adsense' },
                    { name: 'azeembux.online', country: 'Adsense', revenue: 10362, expense: 5600, source: 'adsense' }
                ],
                payments: {
                    network: 21100,
                    client: 21100
                }
            },
            'December 2025': {
                revenue: 98400,
                expense: 55500,
                profit: 42900,
                domains: [
                    { name: 'spookymilklife', country: 'MI', revenue: 3250, expense: 2300, source: 'native' },
                    { name: 'azeembux.online', country: 'Adsense', revenue: 30700, expense: 16900, source: 'adsense' },
                    { name: 'inshotapps.com', country: 'MI', revenue: 4520, expense: 3200, source: 'native' },
                    { name: 'mahatmapost.com-2', country: 'MA+Adsense', revenue: 35700, expense: 19100, source: 'adsense' },
                    { name: 'Spoofy-2', country: 'Adsense', revenue: 16000, expense: 9200, source: 'adsense' },
                    { name: 'doramasvip.cam', country: 'Adsense', revenue: 2600, expense: 1400, source: 'adsense' }
                ],
                payments: {
                    network: 30000,
                    client: 28000
                }
            }
        };

        this.updateDashboard();
    }

    // Navigation
    initNavigation() {
        const navItems = document.querySelectorAll('.nav-item');
        const pages = document.querySelectorAll('.page-content');

        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const page = item.dataset.page;

                navItems.forEach(nav => nav.classList.remove('active'));
                item.classList.add('active');

                pages.forEach(p => {
                    p.classList.remove('active');
                    if (p.id === page) {
                        p.classList.add('active');
                        this.animatePageTransition(p);
                    }
                });

                // Update URL hash
                window.location.hash = page;
            });
        });

        // Handle initial page load from hash
        const hash = window.location.hash.slice(1);
        if (hash) {
            const targetNav = document.querySelector(`[data-page="${hash}"]`);
            if (targetNav) targetNav.click();
        }
    }

    // Modals
    initModals() {
        const modalTriggers = document.querySelectorAll('[data-modal]');
        const modals = document.querySelectorAll('.modal-overlay');
        const closeButtons = document.querySelectorAll('.modal-close');

        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                const modalId = trigger.dataset.modal;
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            });
        });

        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.modal-overlay');
                modal.classList.remove('active');
                document.body.style.overflow = '';
            });
        });

        modals.forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    }

    // Charts Initialization
    initCharts() {
        this.initRevenueChart();
        this.initSourceChart();
        this.initProfitChart();
    }

    initRevenueChart() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        const months = ['Sep', 'Oct', 'Nov', 'Dec'];
        const revenue = [121350, 102602, 67707, 98400];
        const expense = [65000, 58200, 42200, 55500];
        const profit = revenue.map((r, i) => r - expense[i]);

        this.charts.revenue = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Revenue',
                        data: revenue,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    },
                    {
                        label: 'Expense',
                        data: expense,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    },
                    {
                        label: 'Profit',
                        data: profit,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#94a3b8',
                            usePointStyle: true,
                            padding: 20,
                            font: { family: 'Outfit', size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1a1a25',
                        titleColor: '#f8fafc',
                        bodyColor: '#94a3b8',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: (context) => `${context.dataset.label}: $${context.raw.toLocaleString()}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#64748b', font: { family: 'Outfit' } }
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'JetBrains Mono' },
                            callback: (value) => '$' + (value / 1000) + 'K'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    initSourceChart() {
        const ctx = document.getElementById('sourceChart');
        if (!ctx) return;

        this.charts.source = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Facebook Ads', 'Google Ads', 'AdSense', 'Native', 'Search'],
                datasets: [{
                    data: [45, 15, 25, 10, 5],
                    backgroundColor: [
                        '#1877f2',
                        '#ea4335',
                        '#34a853',
                        '#8b5cf6',
                        '#f59e0b'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            color: '#94a3b8',
                            usePointStyle: true,
                            padding: 16,
                            font: { family: 'Outfit', size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1a1a25',
                        titleColor: '#f8fafc',
                        bodyColor: '#94a3b8',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: (context) => `${context.label}: ${context.raw}%`
                        }
                    }
                }
            }
        });
    }

    initProfitChart() {
        const ctx = document.getElementById('profitChart');
        if (!ctx) return;

        const months = ['Sep', 'Oct', 'Nov', 'Dec'];
        const networkShare = [32500, 29100, 21100, 30000];
        const clientShare = [32500, 29250, 21100, 28000];

        this.charts.profit = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: '369Network Share',
                        data: networkShare,
                        backgroundColor: '#6366f1',
                        borderRadius: 8,
                        borderSkipped: false
                    },
                    {
                        label: 'Client Share',
                        data: clientShare,
                        backgroundColor: '#8b5cf6',
                        borderRadius: 8,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#94a3b8',
                            usePointStyle: true,
                            padding: 20,
                            font: { family: 'Outfit', size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1a1a25',
                        titleColor: '#f8fafc',
                        bodyColor: '#94a3b8',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: (context) => `${context.dataset.label}: $${context.raw.toLocaleString()}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { family: 'Outfit' } }
                    },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'JetBrains Mono' },
                            callback: (value) => '$' + (value / 1000) + 'K'
                        }
                    }
                }
            }
        });
    }

    // Animations
    initAnimations() {
        // Intersection Observer for scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        // Number counter animation
        this.animateNumbers();
    }

    animateNumbers() {
        const counters = document.querySelectorAll('.stat-value[data-value]');
        counters.forEach(counter => {
            const target = parseInt(counter.dataset.value);
            const duration = 2000;
            const start = 0;
            const startTime = performance.now();

            const animate = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easing = 1 - Math.pow(1 - progress, 3); // Ease out cubic
                const current = Math.floor(start + (target - start) * easing);
                
                counter.textContent = '$' + current.toLocaleString();
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            };

            requestAnimationFrame(animate);
        });
    }

    animatePageTransition(page) {
        const cards = page.querySelectorAll('.card, .stat-card, .client-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    // Event Listeners
    initEventListeners() {
        // Logout button
        const logoutBtn = document.querySelector('.logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                await this.handleLogout();
            });
        }

        // Month navigation
        document.querySelectorAll('.month-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.classList.contains('prev')) {
                    this.currentMonth--;
                    if (this.currentMonth < 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    }
                } else {
                    this.currentMonth++;
                    if (this.currentMonth > 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    }
                }
                this.updateMonthDisplay();
                this.updateDashboardForMonth();
            });
        });

        // Form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(form);
            });
        });

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value);
            });
        }

        // Tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const tabGroup = tab.closest('.tabs');
                const tabContent = tab.closest('.card').querySelectorAll('.tab-content');
                
                tabGroup.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const tabIndex = Array.from(tabGroup.children).indexOf(tab);
                tabContent.forEach((content, index) => {
                    content.style.display = index === tabIndex ? 'block' : 'none';
                });
            });
        });

        // Export functionality
        document.querySelectorAll('[data-export]').forEach(btn => {
            btn.addEventListener('click', () => {
                const format = btn.dataset.export;
                this.exportData(format);
            });
        });
        
        // Section filtering - click on stat cards to filter
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('click', () => {
                const statValue = card.querySelector('[data-stat]');
                if (statValue) {
                    const filterType = statValue.dataset.stat;
                    this.toggleSectionFilter(filterType, card);
                }
            });
        });
    }

    // Dashboard Updates
    updateDashboard() {
        this.updateStats();
        this.updateDomainsTable();
        this.updateClientsGrid();
        this.updatePaymentsHistory();
        this.updateAllPage();
    }

    updateStats() {
        // Calculate live totals from monthly data
        const totals = {
            revenue: 0,
            expense: 0,
            profit: 0,
            clients: this.clients.length
        };
        
        // Sum up all months
        Object.values(this.monthlyData).forEach(month => {
            totals.revenue += month.revenue;
            totals.expense += month.expense;
            totals.profit += month.profit;
        });

        // Update stat cards
        const statElements = {
            totalRevenue: document.querySelector('[data-stat="revenue"]'),
            totalExpense: document.querySelector('[data-stat="expense"]'),
            totalProfit: document.querySelector('[data-stat="profit"]'),
            totalClients: document.querySelector('[data-stat="clients"]')
        };

        if (statElements.totalRevenue) {
            statElements.totalRevenue.textContent = '$' + totals.revenue.toLocaleString();
        }
        if (statElements.totalExpense) {
            statElements.totalExpense.textContent = '$' + totals.expense.toLocaleString();
        }
        if (statElements.totalProfit) {
            statElements.totalProfit.textContent = '$' + totals.profit.toLocaleString();
        }
        if (statElements.totalClients) {
            statElements.totalClients.textContent = totals.clients;
        }
    }

    updateDomainsTable() {
        const tableBody = document.getElementById('domainsTableBody');
        if (!tableBody) return;

        const monthKey = this.getMonthKey();
        const currentMonthData = this.monthlyData[monthKey];
        
        if (!currentMonthData || !currentMonthData.domains) {
            tableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:#64748b;">No data available for this month</td></tr>';
            return;
        }

        tableBody.innerHTML = currentMonthData.domains.map(domain => `
            <tr>
                <td>
                    <div class="domain-cell">
                        <div class="domain-favicon">${domain.name.charAt(0).toUpperCase()}</div>
                        <div>
                            <div class="domain-name">${domain.name}</div>
                            <div class="domain-source">${domain.country}</div>
                        </div>
                    </div>
                </td>
                <td><span class="source-tag ${domain.source}">${domain.source}</span></td>
                <td class="money positive">$${domain.revenue.toLocaleString()}</td>
                <td class="money negative">$${domain.expense.toLocaleString()}</td>
                <td class="money positive">$${(domain.revenue - domain.expense).toLocaleString()}</td>
                <td><span class="status-badge active"><span class="status-dot"></span>Active</span></td>
                <td>
                    <button class="btn btn-sm btn-secondary">View</button>
                </td>
            </tr>
        `).join('');
    }

    updateClientsGrid() {
        const grid = document.getElementById('clientsGrid');
        if (!grid) return;

        grid.innerHTML = this.clients.map(client => `
            <div class="client-card" data-client-id="${client.id}">
                <div class="client-header">
                    <div class="client-avatar">${client.name.charAt(0)}</div>
                    <div>
                        <div class="client-name">${client.name}</div>
                        <div class="client-id">#${client.id}</div>
                    </div>
                    <span class="status-badge ${client.status}">
                        <span class="status-dot"></span>${client.status}
                    </span>
                </div>
                <div class="client-stats">
                    <div class="client-stat">
                        <div class="client-stat-value positive">$${client.totalRevenue.toLocaleString()}</div>
                        <div class="client-stat-label">Total Revenue</div>
                    </div>
                    <div class="client-stat">
                        <div class="client-stat-value">$${client.profit.toLocaleString()}</div>
                        <div class="client-stat-label">Net Profit</div>
                    </div>
                </div>
                <div class="client-domains">
                    ${client.domains.slice(0, 3).map(d => `<span class="domain-tag">${d}</span>`).join('')}
                    ${client.domains.length > 3 ? `<span class="domain-tag">+${client.domains.length - 3} more</span>` : ''}
                </div>
            </div>
        `).join('');

        // Add click handlers
        grid.querySelectorAll('.client-card').forEach(card => {
            card.addEventListener('click', () => {
                const clientId = card.dataset.clientId;
                this.showClientDetails(clientId);
            });
        });
    }

    updatePaymentsHistory() {
        const container = document.getElementById('paymentsHistory');
        if (!container) return;

        const payments = [
            { type: 'incoming', name: '369Network Payment', date: '26 Dec 2025', amount: 30000 },
            { type: 'outgoing', name: 'Client Payout - Usmanbhai', date: '25 Dec 2025', amount: 28000 },
            { type: 'incoming', name: 'AdSense Revenue', date: '20 Dec 2025', amount: 35700 },
            { type: 'outgoing', name: 'Facebook Ads', date: '18 Dec 2025', amount: 16900 },
            { type: 'incoming', name: 'AdX Revenue', date: '15 Dec 2025', amount: 16000 }
        ];

        container.innerHTML = payments.map(payment => `
            <div class="payment-item">
                <div class="payment-info">
                    <div class="payment-icon ${payment.type}">
                        ${payment.type === 'incoming' ? 
                            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>' :
                            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>'
                        }
                    </div>
                    <div class="payment-details">
                        <h4>${payment.name}</h4>
                        <span>${payment.date}</span>
                    </div>
                </div>
                <div class="payment-amount ${payment.type === 'incoming' ? 'positive' : 'negative'}">
                    ${payment.type === 'incoming' ? '+' : '-'}$${payment.amount.toLocaleString()}
                </div>
            </div>
        `).join('');
    }

    updateMonthDisplay() {
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];
        
        const display = document.querySelector('.month-display');
        if (display) {
            display.textContent = `${monthNames[this.currentMonth]} ${this.currentYear}`;
        }
        
        // Update page header subtitle
        const pageHeader = document.querySelector('.page-header p');
        if (pageHeader) {
            pageHeader.textContent = `Welcome back! Here's your business performance for ${monthNames[this.currentMonth]} ${this.currentYear}`;
        }
    }

    updateDashboardForMonth() {
        const monthKey = this.getMonthKey();
        const monthData = this.monthlyData[monthKey];
        
        if (!monthData) {
            console.warn('No data for selected month');
            return;
        }
        
        // Update stats cards with current month data
        const statElements = {
            revenue: document.querySelector('[data-stat="revenue"]'),
            expense: document.querySelector('[data-stat="expense"]'),
            profit: document.querySelector('[data-stat="profit"]')
        };
        
        if (statElements.revenue) {
            statElements.revenue.textContent = '$' + monthData.revenue.toLocaleString();
        }
        if (statElements.expense) {
            statElements.expense.textContent = '$' + monthData.expense.toLocaleString();
        }
        if (statElements.profit) {
            statElements.profit.textContent = '$' + monthData.profit.toLocaleString();
        }
        
        // Update domains table
        this.updateDomainsTable();
        
        // Update charts with current month data
        this.updateChartsForMonth(monthKey);
    }
    
    getMonthKey() {
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];
        return `${monthNames[this.currentMonth]} ${this.currentYear}`;
    }
    
    updateChartsForMonth(monthKey) {
        // Get all available months up to current selection
        const availableMonths = Object.keys(this.monthlyData);
        const currentIndex = availableMonths.indexOf(monthKey);
        
        if (currentIndex === -1) return;
        
        // Get data for chart (show last 4 months including current)
        const startIndex = Math.max(0, currentIndex - 3);
        const monthsToShow = availableMonths.slice(startIndex, currentIndex + 1);
        
        const labels = monthsToShow.map(m => m.split(' ')[0].substring(0, 3));
        const revenue = monthsToShow.map(m => this.monthlyData[m].revenue);
        const expense = monthsToShow.map(m => this.monthlyData[m].expense);
        const profit = revenue.map((r, i) => r - expense[i]);
        
        // Update revenue chart
        if (this.charts.revenue) {
            this.charts.revenue.data.labels = labels;
            this.charts.revenue.data.datasets[0].data = revenue;
            this.charts.revenue.data.datasets[1].data = expense;
            this.charts.revenue.data.datasets[2].data = profit;
            this.charts.revenue.update();
        }
        
        // Update profit chart
        if (this.charts.profit) {
            const networkShare = monthsToShow.map(m => this.monthlyData[m].networkShare);
            const clientShare = monthsToShow.map(m => this.monthlyData[m].clientShare);
            
            this.charts.profit.data.labels = labels;
            this.charts.profit.data.datasets[0].data = networkShare;
            this.charts.profit.data.datasets[1].data = clientShare;
            this.charts.profit.update();
        }
    }

    // Form Handling
    handleFormSubmit(form) {
        const formId = form.id;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        switch (formId) {
            case 'addClientForm':
                this.addClient(data);
                break;
            case 'addDomainForm':
                this.addDomain(data);
                break;
            case 'addTransactionForm':
                this.addTransaction(data);
                break;
            default:
                console.log('Form submitted:', data);
        }

        // Close modal if in one
        const modal = form.closest('.modal-overlay');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        this.showToast('Success', 'Data saved successfully!', 'success');
    }

    // CRUD Operations
    async addClient(data) {
        try {
            const response = await fetch('api/clients.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            
            if (result.success) {
                this.clients.push(result.client);
                this.updateClientsGrid();
                this.showToast('Client Added', `${data.name} has been added successfully.`, 'success');
            }
        } catch (error) {
            console.error('Error adding client:', error);
            // Add locally for demo
            this.clients.push({
                id: 'CLT' + Date.now(),
                ...data,
                status: 'active',
                totalRevenue: 0,
                totalExpense: 0,
                profit: 0,
                domains: []
            });
            this.updateClientsGrid();
            this.showToast('Client Added', `${data.name} has been added successfully.`, 'success');
        }
    }

    async addDomain(data) {
        this.showToast('Domain Added', `${data.domain} has been added successfully.`, 'success');
        this.updateDomainsTable();
    }

    async addTransaction(data) {
        this.showToast('Transaction Recorded', 'Payment has been recorded successfully.', 'success');
        this.updatePaymentsHistory();
    }

    // Search
    handleSearch(query) {
        const cards = document.querySelectorAll('.client-card, .data-table tbody tr');
        const lowerQuery = query.toLowerCase();

        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(lowerQuery) ? '' : 'none';
        });
    }

    // Client Details
    showClientDetails(clientId) {
        const client = this.clients.find(c => c.id === clientId);
        if (!client) return;

        // Could open a modal or navigate to client page
        console.log('Show client:', client);
    }

    // Section Filtering
    toggleSectionFilter(filterType, card) {
        // Remove active class from all stat cards
        document.querySelectorAll('.stat-card').forEach(c => {
            c.classList.remove('stat-card-active');
            c.style.cursor = 'pointer';
        });
        
        // If clicking the same filter, deactivate it
        if (this.activeFilter === filterType) {
            this.activeFilter = null;
            this.showAllSections();
            return;
        }
        
        // Set new active filter
        this.activeFilter = filterType;
        card.classList.add('stat-card-active');
        
        // Filter the display based on section
        this.filterDashboardBySection(filterType);
        
        // Show toast notification
        const filterNames = {
            revenue: 'Revenue',
            expense: 'Expenses',
            profit: 'Profit',
            clients: 'Clients'
        };
        this.showToast('Filter Applied', `Showing only ${filterNames[filterType]} data`, 'info');
    }
    
    filterDashboardBySection(filterType) {
        // Hide all charts initially
        const chartsGrid = document.querySelector('.charts-grid');
        const grid2 = document.querySelector('.grid-2');
        
        if (chartsGrid) chartsGrid.style.display = 'none';
        if (grid2) grid2.style.display = 'none';
        
        // Update chart based on filter
        switch(filterType) {
            case 'revenue':
                this.showRevenueOnlyChart();
                if (chartsGrid) chartsGrid.style.display = 'grid';
                break;
            case 'expense':
                this.showExpenseOnlyChart();
                if (chartsGrid) chartsGrid.style.display = 'grid';
                break;
            case 'profit':
                this.showProfitOnlyChart();
                if (chartsGrid) chartsGrid.style.display = 'grid';
                break;
            case 'clients':
                // Show clients grid
                if (grid2) grid2.style.display = 'grid';
                break;
        }
    }
    
    showRevenueOnlyChart() {
        if (!this.charts.revenue) return;
        
        // Show only revenue dataset
        this.charts.revenue.data.datasets.forEach((dataset, index) => {
            dataset.hidden = index !== 0; // Show only first dataset (Revenue)
        });
        this.charts.revenue.update();
    }
    
    showExpenseOnlyChart() {
        if (!this.charts.revenue) return;
        
        // Show only expense dataset
        this.charts.revenue.data.datasets.forEach((dataset, index) => {
            dataset.hidden = index !== 1; // Show only second dataset (Expense)
        });
        this.charts.revenue.update();
    }
    
    showProfitOnlyChart() {
        if (!this.charts.revenue) return;
        
        // Show only profit dataset
        this.charts.revenue.data.datasets.forEach((dataset, index) => {
            dataset.hidden = index !== 2; // Show only third dataset (Profit)
        });
        this.charts.revenue.update();
    }
    
    showAllSections() {
        // Show all charts
        const chartsGrid = document.querySelector('.charts-grid');
        const grid2 = document.querySelector('.grid-2');
        
        if (chartsGrid) chartsGrid.style.display = 'grid';
        if (grid2) grid2.style.display = 'grid';
        
        // Show all datasets in revenue chart
        if (this.charts.revenue) {
            this.charts.revenue.data.datasets.forEach(dataset => {
                dataset.hidden = false;
            });
            this.charts.revenue.update();
        }
    }
    
    // Update All Page with comprehensive data
    updateAllPage() {
        this.updateAllDomainsTable();
    }
    
    updateAllDomainsTable() {
        const tableBody = document.getElementById('allDomainsTableBody');
        if (!tableBody) return;
        
        // Collect all domains from all months
        const allDomains = [];
        Object.entries(this.monthlyData).forEach(([month, data]) => {
            if (data.domains) {
                data.domains.forEach(domain => {
                    // Check if domain already exists
                    const existingDomain = allDomains.find(d => d.name === domain.name);
                    if (existingDomain) {
                        // Aggregate data
                        existingDomain.revenue += domain.revenue;
                        existingDomain.expense += domain.expense;
                    } else {
                        // Add new domain
                        allDomains.push({
                            name: domain.name,
                            country: domain.country,
                            source: domain.source,
                            revenue: domain.revenue,
                            expense: domain.expense
                        });
                    }
                });
            }
        });
        
        // Sort by revenue descending
        allDomains.sort((a, b) => b.revenue - a.revenue);
        
        tableBody.innerHTML = allDomains.map(domain => `
            <tr>
                <td>
                    <div class="domain-cell">
                        <div class="domain-favicon">${domain.name.charAt(0).toUpperCase()}</div>
                        <div>
                            <div class="domain-name">${domain.name}</div>
                            <div class="domain-source">${domain.country}</div>
                        </div>
                    </div>
                </td>
                <td><span class="source-tag ${domain.source}">${domain.source}</span></td>
                <td class="money positive">$${domain.revenue.toLocaleString()}</td>
                <td class="money negative">$${domain.expense.toLocaleString()}</td>
                <td class="money positive">$${(domain.revenue - domain.expense).toLocaleString()}</td>
                <td><span class="status-badge active"><span class="status-dot"></span>Active</span></td>
            </tr>
        `).join('');
    }
    
    // Export
    exportData(format) {
        const data = {
            clients: this.clients,
            monthlyData: this.monthlyData
        };

        switch (format) {
            case 'csv':
                this.exportCSV(data);
                break;
            case 'pdf':
                this.exportPDF(data);
                break;
            case 'excel':
                this.exportExcel(data);
                break;
        }
    }

    exportCSV(data) {
        let csv = 'Domain,Country,Revenue,Expense,Profit,Source\n';
        
        Object.values(this.monthlyData).forEach(month => {
            month.domains.forEach(d => {
                csv += `${d.name},${d.country},${d.revenue},${d.expense},${d.revenue - d.expense},${d.source}\n`;
            });
        });

        this.downloadFile(csv, 'dashboard-export.csv', 'text/csv');
        this.showToast('Export Complete', 'CSV file has been downloaded.', 'success');
    }

    exportPDF(data) {
        this.showToast('Export Started', 'Generating PDF report...', 'info');
        // Would integrate with a PDF library like jsPDF
    }

    exportExcel(data) {
        this.showToast('Export Started', 'Generating Excel file...', 'info');
        // Would integrate with a library like SheetJS
    }

    downloadFile(content, filename, type) {
        const blob = new Blob([content], { type });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    // Toast Notifications
    showToast(title, message, type = 'info') {
        const container = document.getElementById('toastContainer') || this.createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                ${this.getToastIcon(type)}
            </div>
            <div class="toast-content">
                <h4>${title}</h4>
                <p>${message}</p>
            </div>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }

    getToastIcon(type) {
        const icons = {
            success: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>',
            error: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            warning: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            info: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
        };
        return icons[type] || icons.info;
    }

    async handleLogout() {
        try {
            const session = localStorage.getItem('session');
            if (session) {
                const sessionData = JSON.parse(session);
                
                // Call logout API
                await fetch('/api/auth-logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${sessionData.access_token}`
                    }
                });
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            // Clear local storage
            localStorage.removeItem('session');
            localStorage.removeItem('user');
            
            // Redirect to login
            window.location.href = '/login.html';
        }
    }
}

// Utility Functions
function formatCurrency(amount) {
    return '$' + amount.toLocaleString('en-IN');
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize Dashboard
document.addEventListener('DOMContentLoaded', () => {
    window.dashboard = new Dashboard();
});

// Mobile sidebar toggle
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('open');
}

// Print functionality
function printReport() {
    window.print();
}
