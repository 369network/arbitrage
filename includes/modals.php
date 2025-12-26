<!-- Add Client Modal -->
<div class="modal-overlay" id="addClientModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add New Client</h3>
            <button class="modal-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <form id="addClientForm">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Client Name *</label>
                    <input type="text" name="name" class="form-input" placeholder="Enter client name" required>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" placeholder="client@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" placeholder="+91 98765 43210">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Revenue Share %</label>
                        <input type="number" name="revenueShare" class="form-input" value="50" min="0" max="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-textarea" placeholder="Additional notes about this client..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Client</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Domain Modal -->
<div class="modal-overlay" id="addDomainModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add New Domain</h3>
            <button class="modal-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <form id="addDomainForm">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Domain Name *</label>
                    <input type="text" name="domain" class="form-input" placeholder="example.com" required>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Client *</label>
                        <select name="clientId" class="form-select" required>
                            <option value="">Select Client</option>
                            <option value="USM001">Usmanbhai</option>
                            <option value="DIV001">Diversity Media</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Traffic Source</label>
                        <select name="source" class="form-select">
                            <option value="facebook">Facebook Ads</option>
                            <option value="google">Google Ads</option>
                            <option value="tiktok">TikTok</option>
                            <option value="native">Native Ads</option>
                            <option value="search">Search/Organic</option>
                        </select>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Monetization</label>
                        <select name="monetization" class="form-select">
                            <option value="adx">Google AdX</option>
                            <option value="adsense">Google AdSense</option>
                            <option value="both">AdX + AdSense</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Target Country</label>
                        <input type="text" name="country" class="form-input" placeholder="e.g., Spain, India">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Initial Budget ($)</label>
                    <input type="number" name="budget" class="form-input" placeholder="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Domain</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal-overlay" id="addPaymentModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Record Payment</h3>
            <button class="modal-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <form id="addPaymentForm">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Payment Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="network_share">369Network Share</option>
                        <option value="client_payout">Client Payout</option>
                        <option value="ad_expense">Ad Platform Expense</option>
                        <option value="revenue_received">Revenue Received</option>
                    </select>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Amount ($) *</label>
                        <input type="number" name="amount" class="form-input" placeholder="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date *</label>
                        <input type="date" name="date" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Client/Platform</label>
                    <select name="entity" class="form-select">
                        <option value="">Select...</option>
                        <option value="USM001">Usmanbhai</option>
                        <option value="DIV001">Diversity Media</option>
                        <option value="facebook">Facebook Ads</option>
                        <option value="google">Google Ads</option>
                        <option value="adsense">Google AdSense</option>
                        <option value="adx">Google AdX</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Reference/Notes</label>
                    <input type="text" name="reference" class="form-input" placeholder="Payment reference or notes">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                <button type="submit" class="btn btn-primary">Record Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Transaction Modal -->
<div class="modal-overlay" id="addTransactionModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add Transaction</h3>
            <button class="modal-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <form id="addTransactionForm">
            <div class="modal-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Domain *</label>
                        <select name="domainId" class="form-select" required>
                            <option value="">Select Domain</option>
                            <option value="1">mahatmapost.com</option>
                            <option value="2">azeembux.online</option>
                            <option value="3">spookymilklife.org</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date *</label>
                        <input type="date" name="date" class="form-input" required>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Revenue ($)</label>
                        <input type="number" name="revenue" class="form-input" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Expense ($)</label>
                        <input type="number" name="expense" class="form-input" placeholder="0">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-textarea" placeholder="Additional notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Transaction</button>
            </div>
        </form>
    </div>
</div>

<!-- View Client Modal -->
<div class="modal-overlay" id="viewClientModal">
    <div class="modal" style="max-width: 800px;">
        <div class="modal-header">
            <h3 class="modal-title">Client Details</h3>
            <button class="modal-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div id="clientDetailsContent">
                <!-- Populated dynamically -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close">Close</button>
            <button type="button" class="btn btn-primary">Edit Client</button>
        </div>
    </div>
</div>
