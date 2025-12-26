// 369Network Dashboard - Payment Status Enhanced JavaScript
class PaymentTracker {
    constructor() {
        this.paymentStatuses = {
            'pending': { label: 'Pending (21st)', color: '#f59e0b', icon: 'clock' },
            'received': { label: 'Received', color: '#10b981', icon: 'check' },
            'hold': { label: 'On Hold', color: '#f97316', icon: 'pause' },
            'deducted': { label: 'Deducted', color: '#8b5cf6', icon: 'minus' },
            'account_disabled': { label: 'Account Disabled', color: '#ef4444', icon: 'x' },
            'not_received': { label: 'Not Received', color: '#ef4444', icon: 'x' }
        };
    }

    /**
     * Get expected payment date (21st of next month)
     */
    getExpectedPaymentDate(earningMonth, earningYear) {
        let nextMonth = earningMonth + 1;
        let nextYear = earningYear;
        
        if (nextMonth > 12) {
            nextMonth = 1;
            nextYear++;
        }
        
        return new Date(nextYear, nextMonth - 1, 21);
    }

    /**
     * Check if payment is overdue
     */
    isOverdue(earningMonth, earningYear, status) {
        if (status !== 'pending') return false;
        
        const expectedDate = this.getExpectedPaymentDate(earningMonth, earningYear);
        return new Date() > expectedDate;
    }

    /**
     * Calculate effective profit based on payment status
     */
    calculateEffectiveProfit(domain) {
        const status = domain.paymentStatus || 'pending';
        const grossProfit = domain.revenue - domain.expense;
        const deduction = domain.deductionAmount || 0;
        
        switch (status) {
            case 'received':
                return { 
                    amount: grossProfit, 
                    confirmed: true, 
                    label: 'Confirmed' 
                };
            
            case 'deducted':
                return { 
                    amount: grossProfit - deduction, 
                    confirmed: true, 
                    label: `Deducted: $${deduction.toLocaleString()}`,
                    originalAmount: grossProfit
                };
            
            case 'pending':
                return { 
                    amount: grossProfit, 
                    confirmed: false, 
                    label: 'Pending Payment' 
                };
            
            case 'hold':
                return { 
                    amount: grossProfit, 
                    confirmed: false, 
                    label: 'Payment On Hold',
                    warning: true
                };
            
            case 'account_disabled':
            case 'not_received':
                return { 
                    amount: 0, 
                    confirmed: true, 
                    label: 'No Payment Expected',
                    originalAmount: grossProfit,
                    lost: true
                };
            
            default:
                return { amount: grossProfit, confirmed: false };
        }
    }

    /**
     * Get status badge HTML
     */
    getStatusBadge(status) {
        const statusInfo = this.paymentStatuses[status] || this.paymentStatuses['pending'];
        return `
            <span class="status-badge ${status}" style="--status-color: ${statusInfo.color}">
                <span class="status-dot" style="background: ${statusInfo.color}"></span>
                ${statusInfo.label}
            </span>
        `;
    }

    /**
     * Get payment due indicator
     */
    getPaymentDueIndicator(earningMonth, earningYear, status) {
        if (status !== 'pending') return '';
        
        const expectedDate = this.getExpectedPaymentDate(earningMonth, earningYear);
        const today = new Date();
        const daysUntil = Math.ceil((expectedDate - today) / (1000 * 60 * 60 * 24));
        
        if (daysUntil < 0) {
            return `<span class="payment-due-indicator overdue">Overdue by ${Math.abs(daysUntil)} days</span>`;
        } else if (daysUntil <= 7) {
            return `<span class="payment-due-indicator upcoming">Due in ${daysUntil} days</span>`;
        }
        
        return `<span class="payment-due-indicator">Expected: ${expectedDate.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' })}</span>`;
    }

    /**
     * Render domain row with status color
     */
    renderDomainRow(domain) {
        const status = domain.paymentStatus || 'pending';
        const profitData = this.calculateEffectiveProfit(domain);
        
        let profitDisplay = `$${profitData.amount.toLocaleString()}`;
        if (profitData.originalAmount && profitData.originalAmount !== profitData.amount) {
            profitDisplay = `
                <span class="profit-strikethrough">$${profitData.originalAmount.toLocaleString()}</span>
                <span class="${profitData.lost ? 'money negative' : 'money positive'}">$${profitData.amount.toLocaleString()}</span>
            `;
        }
        
        return `
            <tr class="status-${status}">
                <td>
                    <div class="domain-cell">
                        <div class="domain-favicon status-${status}">${domain.name.charAt(0).toUpperCase()}</div>
                        <div>
                            <div class="domain-name">${domain.name}</div>
                            <div class="domain-source">${domain.country || domain.source}</div>
                        </div>
                    </div>
                </td>
                <td><span class="source-tag ${domain.source}">${domain.source}</span></td>
                <td class="money positive">$${domain.revenue.toLocaleString()}</td>
                <td class="money negative">$${domain.expense.toLocaleString()}</td>
                <td class="${profitData.confirmed ? 'money positive' : 'money'} ${profitData.lost ? 'negative' : ''}">${profitDisplay}</td>
                <td>
                    ${this.getStatusBadge(status)}
                    ${domain.deductionAmount ? `<div class="deduction-badge">-$${domain.deductionAmount.toLocaleString()}</div>` : ''}
                </td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="paymentTracker.openStatusModal(${domain.id})">
                        Update
                    </button>
                </td>
            </tr>
        `;
    }

    /**
     * Calculate monthly totals with payment status consideration
     */
    calculateMonthlyTotals(domains) {
        const totals = {
            grossRevenue: 0,
            grossExpense: 0,
            grossProfit: 0,
            confirmedProfit: 0,
            pendingProfit: 0,
            heldProfit: 0,
            lostProfit: 0,
            deductions: 0
        };
        
        domains.forEach(domain => {
            const grossProfit = domain.revenue - domain.expense;
            totals.grossRevenue += domain.revenue;
            totals.grossExpense += domain.expense;
            totals.grossProfit += grossProfit;
            
            const status = domain.paymentStatus || 'pending';
            const deduction = domain.deductionAmount || 0;
            
            switch (status) {
                case 'received':
                    totals.confirmedProfit += grossProfit;
                    break;
                case 'deducted':
                    totals.confirmedProfit += (grossProfit - deduction);
                    totals.deductions += deduction;
                    break;
                case 'pending':
                    totals.pendingProfit += grossProfit;
                    break;
                case 'hold':
                    totals.heldProfit += grossProfit;
                    break;
                case 'account_disabled':
                case 'not_received':
                    totals.lostProfit += grossProfit;
                    break;
            }
        });
        
        // Calculate shares only on confirmed profit
        totals.networkShare = totals.confirmedProfit / 2;
        totals.clientShare = totals.confirmedProfit / 2;
        
        return totals;
    }

    /**
     * Render payment summary cards
     */
    renderPaymentSummary(totals) {
        return `
            <div class="stats-grid" style="grid-template-columns: repeat(5, 1fr);">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon revenue">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23,6 13.5,15.5 8.5,10.5 1,18"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value" style="color: #10b981;">$${totals.confirmedProfit.toLocaleString()}</div>
                    <div class="stat-label">Confirmed Profit</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value" style="color: #f59e0b;">$${totals.pendingProfit.toLocaleString()}</div>
                    <div class="stat-label">Pending (21st)</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: rgba(249, 115, 22, 0.15); color: #f97316;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value" style="color: #f97316;">$${totals.heldProfit.toLocaleString()}</div>
                    <div class="stat-label">On Hold</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value" style="color: #8b5cf6;">$${totals.deductions.toLocaleString()}</div>
                    <div class="stat-label">Deductions</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value" style="color: #ef4444;">$${totals.lostProfit.toLocaleString()}</div>
                    <div class="stat-label">Lost (Disabled)</div>
                </div>
            </div>
        `;
    }

    /**
     * Open status update modal
     */
    openStatusModal(domainId) {
        const modalHTML = `
            <div class="modal-overlay active" id="statusUpdateModal">
                <div class="modal">
                    <div class="modal-header">
                        <h3 class="modal-title">Update Payment Status</h3>
                        <button class="modal-close" onclick="document.getElementById('statusUpdateModal').remove()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <form id="statusUpdateForm" onsubmit="paymentTracker.updateStatus(event, ${domainId})">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label">Payment Status</label>
                                <select name="status" class="form-select" required onchange="paymentTracker.toggleDeductionField(this.value)">
                                    <option value="pending">Pending (Waiting for 21st)</option>
                                    <option value="received">Received (Full Payment)</option>
                                    <option value="deducted">Received with Deductions</option>
                                    <option value="hold">On Hold by Google</option>
                                    <option value="account_disabled">Account Disabled</option>
                                    <option value="not_received">Not Received (Other)</option>
                                </select>
                            </div>
                            
                            <div id="deductionFields" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Deduction Amount ($)</label>
                                    <input type="number" name="deductionAmount" class="form-input" placeholder="0">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Deduction Reason</label>
                                    <select name="deductionReason" class="form-select">
                                        <option value="">Select reason</option>
                                        <option value="invalid_traffic">Invalid Traffic</option>
                                        <option value="policy_violation">Policy Violation</option>
                                        <option value="tax_deduction">Tax Deduction</option>
                                        <option value="adjustment">Adjustment</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-textarea" placeholder="Additional details..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('statusUpdateModal').remove()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    /**
     * Toggle deduction fields visibility
     */
    toggleDeductionField(status) {
        const deductionFields = document.getElementById('deductionFields');
        if (deductionFields) {
            deductionFields.style.display = status === 'deducted' ? 'block' : 'none';
        }
    }

    /**
     * Update payment status via API
     */
    async updateStatus(event, domainId) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        data.id = domainId;
        
        try {
            const response = await fetch('api/adsense_payments.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                document.getElementById('statusUpdateModal').remove();
                window.dashboard.showToast('Status Updated', 'Payment status has been updated successfully.', 'success');
                window.dashboard.updateDashboard();
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            console.error('Error updating status:', error);
            window.dashboard.showToast('Error', 'Failed to update status. Please try again.', 'error');
        }
    }

    /**
     * Render alerts for payment issues
     */
    renderPaymentAlerts(domains) {
        const alerts = [];
        const today = new Date();
        
        // Check for overdue payments
        const overdue = domains.filter(d => 
            d.paymentStatus === 'pending' && 
            this.isOverdue(d.earningMonth, d.earningYear, d.paymentStatus)
        );
        
        if (overdue.length > 0) {
            alerts.push({
                type: 'danger',
                title: `${overdue.length} Overdue Payment${overdue.length > 1 ? 's' : ''}`,
                message: `Payment expected on 21st but not received yet. Please check Google AdSense/AdX.`
            });
        }
        
        // Check for held payments
        const held = domains.filter(d => d.paymentStatus === 'hold');
        if (held.length > 0) {
            alerts.push({
                type: 'warning',
                title: `${held.length} Payment${held.length > 1 ? 's' : ''} On Hold`,
                message: `Google is holding payment for verification. This may take additional time.`
            });
        }
        
        // Check for disabled accounts
        const disabled = domains.filter(d => d.paymentStatus === 'account_disabled');
        if (disabled.length > 0) {
            alerts.push({
                type: 'danger',
                title: `${disabled.length} Disabled Account${disabled.length > 1 ? 's' : ''}`,
                message: `These domains will not receive payment. Revenue has been excluded from profit calculations.`
            });
        }
        
        return alerts.map(alert => `
            <div class="alert-banner ${alert.type}">
                <svg class="alert-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    ${alert.type === 'danger' ? 
                        '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>' :
                        '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'
                    }
                </svg>
                <div class="alert-banner-content">
                    <h4>${alert.title}</h4>
                    <p>${alert.message}</p>
                </div>
            </div>
        `).join('');
    }
}

// Initialize payment tracker
const paymentTracker = new PaymentTracker();

// Export for use in main app
window.paymentTracker = paymentTracker;
