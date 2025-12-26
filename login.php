<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - 369Network</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            animation: fadeInUp 0.6s ease;
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-logo {
            width: 72px;
            height: 72px;
            background: var(--gradient-primary);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            color: white;
            margin: 0 auto 20px;
            box-shadow: 0 8px 32px rgba(99, 102, 241, 0.3);
            position: relative;
            overflow: hidden;
        }

        .login-logo::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, transparent 40%, rgba(255,255,255,0.3) 50%, transparent 60%);
            animation: shimmer 3s infinite;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-header p {
            color: var(--text-secondary);
            font-size: 15px;
        }

        .login-form .form-group {
            margin-bottom: 24px;
        }

        .login-form .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .login-form .form-input {
            padding: 16px;
            font-size: 15px;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper .form-input {
            padding-left: 48px;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: var(--text-primary);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--accent-primary);
            cursor: pointer;
        }

        .remember-me span {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .forgot-link {
            font-size: 14px;
            color: var(--accent-primary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: var(--accent-secondary);
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }

        .login-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, transparent 40%, rgba(255,255,255,0.2) 50%, transparent 60%);
            transform: translateX(-100%);
            transition: transform 0.5s;
        }

        .login-btn:hover::after {
            transform: translateX(100%);
        }

        .login-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 28px 0;
        }

        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-subtle);
        }

        .login-divider span {
            font-size: 13px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .demo-accounts {
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
            padding: 16px;
        }

        .demo-accounts h4 {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .demo-account {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            background: var(--bg-card);
            border-radius: var(--radius-sm);
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .demo-account:last-child {
            margin-bottom: 0;
        }

        .demo-account:hover {
            border-color: var(--accent-primary);
            transform: translateX(4px);
        }

        .demo-account-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .demo-account-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
        }

        .demo-account-avatar.admin {
            background: var(--gradient-primary);
            color: white;
        }

        .demo-account-avatar.client {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
        }

        .demo-account-details h5 {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .demo-account-details span {
            font-size: 11px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }

        .demo-account-role {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 500;
        }

        .demo-account-role.admin {
            background: rgba(99, 102, 241, 0.15);
            color: var(--accent-primary);
        }

        .demo-account-role.client {
            background: rgba(139, 92, 246, 0.15);
            color: #8b5cf6;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .error-message.show {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .login-btn.loading .loading-spinner {
            display: inline-block;
        }

        .login-btn.loading span {
            opacity: 0;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border-subtle);
        }

        .login-footer p {
            font-size: 13px;
            color: var(--text-muted);
        }

        .login-footer a {
            color: var(--accent-primary);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">369</div>
                <h1>369Network</h1>
                <p>Sign in to your dashboard</p>
            </div>

            <div class="error-message" id="errorMessage">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <span id="errorText">Invalid username or password</span>
            </div>

            <form class="login-form" id="loginForm" method="POST" action="auth/login.php">
                <div class="form-group">
                    <label class="form-label">Username or Email</label>
                    <div class="input-icon-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input type="text" name="username" id="username" class="form-input" placeholder="Enter your username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-icon-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input type="password" name="password" id="password" class="form-input" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" value="1">
                        <span>Remember me</span>
                    </label>
                    <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary login-btn" id="loginBtn">
                    <span>Sign In</span>
                    <div class="loading-spinner"></div>
                </button>
            </form>

            <div class="login-divider">
                <span>Quick Login</span>
            </div>

            <div class="demo-accounts">
                <div class="demo-account" onclick="fillCredentials('contact@369network.com', 'Spidigoo@#369')">
                    <div class="demo-account-info">
                        <div class="demo-account-avatar admin">NP</div>
                        <div class="demo-account-details">
                            <h5>Nipam Patel</h5>
                            <span>contact@369network.com</span>
                        </div>
                    </div>
                    <span class="demo-account-role admin">Admin</span>
                </div>
                <div class="demo-account" onclick="fillCredentials('Usman@369network.com', 'Password@#123')">
                    <div class="demo-account-info">
                        <div class="demo-account-avatar client">U</div>
                        <div class="demo-account-details">
                            <h5>Usmanbhai</h5>
                            <span>Usman@369network.com</span>
                        </div>
                    </div>
                    <span class="demo-account-role client">Client</span>
                </div>
                <div class="demo-account" onclick="fillCredentials('vpmedia@369network.com', 'Password@#123')">
                    <div class="demo-account-info">
                        <div class="demo-account-avatar client">VP</div>
                        <div class="demo-account-details">
                            <h5>VP Media</h5>
                            <span>vpmedia@369network.com</span>
                        </div>
                    </div>
                    <span class="demo-account-role client">Client</span>
                </div>
                <div class="demo-account" onclick="fillCredentials('thebes@369network.com', 'Password@#123')">
                    <div class="demo-account-info">
                        <div class="demo-account-avatar client">TH</div>
                        <div class="demo-account-details">
                            <h5>Thebes Media</h5>
                            <span>thebes@369network.com</span>
                        </div>
                    </div>
                    <span class="demo-account-role client">Client</span>
                </div>
            </div>

            <div class="login-footer">
                <p>© 2025 369Network. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }

        function fillCredentials(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
        }

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
        });

        // Check for error parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('error')) {
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            
            const errors = {
                'invalid': 'Invalid username or password',
                'expired': 'Your session has expired. Please login again.',
                'logout': 'You have been logged out successfully.'
            };
            
            errorText.textContent = errors[urlParams.get('error')] || 'An error occurred';
            errorMessage.classList.add('show');
            
            if (urlParams.get('error') === 'logout') {
                errorMessage.style.background = 'rgba(16, 185, 129, 0.1)';
                errorMessage.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                errorMessage.style.color = '#10b981';
            }
        }
    </script>
</body>
</html>
