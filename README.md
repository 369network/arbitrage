# 369Network - Arbitrage Management Dashboard

A comprehensive web-based dashboard for managing arbitrage business operations, tracking revenue, expenses, clients, and domains.

## 🚀 Features

### Core Functionality
- **Complete Overview ("All" Page)** - See all data across all sections in one comprehensive view
- **Dashboard** - Real-time business performance metrics
- **Domain Management** - Track all domains with revenue and expense data
- **Client Management** - Manage client relationships and their domains
- **Financial Tracking** - Monitor revenue, expenses, and profit margins
- **Payment History** - Complete transaction records
- **Analytics** - Traffic source analysis and performance metrics

### Advanced Features
- **Month-wise Navigation** - Browse data by specific months
- **Live Data Updates** - Dynamic calculations from monthly data
- **Section Filtering** - Click stat cards to filter dashboard by Revenue, Expenses, Profit, or Clients
- **Interactive Charts** - Visual representation of trends using Chart.js
- **Export Functionality** - Download data as CSV
- **Responsive Design** - Works on desktop and mobile devices

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or XAMPP/WAMP
- Modern web browser

## 🔧 Installation

### Option 1: Using XAMPP (Recommended for Windows)

1. **Install XAMPP**
   ```
   Download from: https://www.apachefriends.org/download.html
   ```

2. **Clone the repository**
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/369network/arbitrage.git
   ```

3. **Configure Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create a new database named `arbitrage_db`
   - Import `includes/database.sql`

4. **Configure Settings**
   - Copy `includes/config.php.example` to `includes/config.php`
   - Update database credentials

5. **Start Apache**
   - Open XAMPP Control Panel
   - Start Apache and MySQL

6. **Access Dashboard**
   ```
   http://localhost/arbitrage/
   ```

### Option 2: Using PHP Built-in Server (Development)

```bash
cd arbitrage
php -S localhost:8000
```

Then open: http://localhost:8000

## 📁 Project Structure

```
369Arbitrage/
├── api/                    # API endpoints
│   ├── auth.php           # Authentication API
│   ├── clients.php        # Client management API
│   ├── domains.php        # Domain management API
│   └── get_data.php       # Data retrieval API
├── auth/                   # Authentication
│   ├── check.php          # Auth verification
│   ├── login.php          # Login handler
│   └── logout.php         # Logout handler
├── css/                    # Stylesheets
│   └── style.css          # Main styles
├── includes/               # PHP includes
│   ├── config.php         # Database configuration
│   ├── database.sql       # Database schema
│   ├── modals.php         # Modal components
│   └── sidebar.php        # Sidebar navigation
├── js/                     # JavaScript files
│   ├── app.js             # Main application logic
│   └── payment-tracker.js # Payment tracking
├── uploads/                # File uploads directory
├── index.php              # Main dashboard
├── login.php              # Login page
├── client-dashboard.php   # Client view
└── README.md              # This file
```

## 🎨 Features Breakdown

### All Page
- Comprehensive overview of all data
- Aggregated domain statistics
- Revenue and expense breakdowns
- Complete payment history
- All clients and domains in one view

### Dashboard
- Monthly performance metrics
- Revenue, Expense, Profit, and Client counts
- Interactive charts (Revenue & Expenses Trend, Traffic Sources)
- Top performing domains
- Recent payment history
- Click stat cards to filter by section

### Month Navigation
- Browse data by month
- Dynamic updates of all stats and charts
- Real-time calculation of totals

### Section Filtering
- Click Revenue card → Show only revenue data
- Click Expense card → Show only expense data
- Click Profit card → Show only profit data
- Click Clients card → Show client information
- Visual feedback with glowing effects

## 🔐 Security Features

- Session-based authentication
- Role-based access control (Admin/Client)
- SQL injection prevention with prepared statements
- XSS protection
- CSRF token validation
- Secure password hashing

## 🛠️ Technologies Used

- **Frontend:**
  - HTML5, CSS3 (Custom Dark Theme)
  - JavaScript (ES6+)
  - Chart.js for data visualization
  - Custom fonts: Outfit, JetBrains Mono

- **Backend:**
  - PHP 7.4+
  - MySQL/MariaDB
  - RESTful API architecture

## 📊 Data Structure

The system tracks:
- **Clients** - Business partners and their information
- **Domains** - Websites with revenue/expense tracking
- **Transactions** - Payment records
- **Monthly Data** - Performance metrics by month

## 🐛 Bug Fixes (Latest Update)

✅ Fixed logout button redirect
✅ Implemented month-wise dashboard updates
✅ Added live data display with dynamic calculations
✅ Created section filtering functionality
✅ Added comprehensive "All" page

## 🤝 Contributing

This is a private project for 369Network. For any issues or suggestions, please contact the development team.

## 📝 License

Proprietary - All rights reserved by 369Network

## 👥 Credits

Developed for 369Network Arbitration Hub
Admin: Nipam Patel

## 📞 Support

For support or questions, please contact the 369Network team.

---

**Version:** 1.0.0  
**Last Updated:** December 2025

