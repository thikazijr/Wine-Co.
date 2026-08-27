const API_URL = '../backend/accounting/';

function openTransactionModal(type) {
    document.getElementById('transactionType').value = type;
    document.getElementById('transactionId').value = '';
    document.getElementById('transactionScenario').value = '';
    document.getElementById('transactionAmount').value = '';
    document.getElementById('transactionDescription').value = '';
    document.getElementById('transactionReference').value = '';
    document.getElementById('dynamicFields').innerHTML = '';
    
    const title = type === 'income' ? 'Record Income' : 'Record Expense';
    document.getElementById('transactionModalTitle').innerHTML = `<i class="fas fa-${type === 'income' ? 'plus-circle' : 'minus-circle'} me-2"></i>${title}`;
    
    new bootstrap.Modal(document.getElementById('transactionModal')).show();
}

function handleScenarioChange() {
    const scenario = document.getElementById('transactionScenario').value;
    const type = document.getElementById('transactionType').value;
    const dynamicFields = document.getElementById('dynamicFields');
    
    let html = '';
    let description = '';
    
    switch(scenario) {
        case 'invoice_paid':
            description = 'Customer paid invoice';
            html = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customerName" placeholder="Customer name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Invoice Number</label>
                        <input type="text" class="form-control" id="invoiceNumber" placeholder="INV-001">
                    </div>
                </div>
            `;
            break;
        case 'cash_sale':
            description = 'Cash sale';
            html = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customerName" placeholder="Customer name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sale Reference</label>
                        <input type="text" class="form-control" id="saleReference" placeholder="Sale #">
                    </div>
                </div>
            `;
            break;
        case 'subscription_paid':
            description = 'Subscription payment received';
            html = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subscriber Name</label>
                        <input type="text" class="form-control" id="subscriberName" placeholder="Subscriber name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Plan</label>
                        <select class="form-select" id="subscriptionPlan">
                            <option value="Basic">Basic</option>
                            <option value="Premium">Premium</option>
                            <option value="VIP">VIP</option>
                        </select>
                    </div>
                </div>
            `;
            break;
        case 'gift_sale':
            description = 'Corporate gift sold';
            html = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gift Type</label>
                        <input type="text" class="form-control" id="giftType" placeholder="Executive, Boardroom, etc.">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Client Name</label>
                        <input type="text" class="form-control" id="clientName" placeholder="Client/company name">
                    </div>
                </div>
            `;
            break;
        case 'supplier_invoice':
            description = 'Supplier invoice to pay';
            html = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" id="supplierName" placeholder="Supplier name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Supplier Invoice #</label>
                        <input type="text" class="form-control" id="supplierInvoice" placeholder="Supplier invoice number">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Expense Category</label>
                    <select class="form-select" id="expenseCategory">
                        <option value="Wine Inventory">Wine Inventory</option>
                        <option value="Packaging">Packaging</option>
                        <option value="Office Supplies">Office Supplies</option>
                        <option value="Utilities">Utilities</option>
                        <option value="Rent">Rent</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            `;
            break;
        case 'staff_payment':
            description = 'Staff salary payment';
            html = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Staff Name</label>
                        <input type="text" class="form-control" id="staffName" placeholder="Staff name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Period</label>
                        <input type="text" class="form-control" id="payPeriod" placeholder="e.g., July 2024" value="<?php echo date('F Y'); ?>">
                    </div>
                </div>
            `;
            break;
        case 'operating_expense':
            description = 'Operating expense';
            html = `
                <div class="mb-3">
                    <label class="form-label">Expense Category</label>
                    <select class="form-select" id="operatingCategory">
                        <option value="Rent">Rent</option>
                        <option value="Utilities">Utilities (Water, Electricity)</option>
                        <option value="Internet">Internet/Phone</option>
                        <option value="Cleaning">Cleaning</option>
                        <option value="Maintenance">Maintenance/Repairs</option>
                        <option value="Insurance">Insurance</option>
                        <option value="Marketing">Marketing/Advertising</option>
                        <option value="Office Supplies">Office Supplies</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vendor/Provider</label>
                    <input type="text" class="form-control" id="vendorName" placeholder="Vendor name">
                </div>
            `;
            break;
        case 'inventory_purchase':
            description = 'Inventory/Wine purchase';
            html = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Supplier</label>
                        <input type="text" class="form-control" id="inventorySupplier" placeholder="Supplier name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Wine Type</label>
                        <input type="text" class="form-control" id="wineType" placeholder="e.g., Pinot Noir">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" placeholder="Number of bottles">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unit Price</label>
                        <div class="input-group">
                            <span class="input-group-text">E</span>
                            <input type="number" class="form-control" id="unitPrice" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                </div>
            `;
            break;
        case 'delivery_cost':
            description = 'Delivery/Shipping cost';
            html = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Delivery Provider</label>
                        <input type="text" class="form-control" id="deliveryProvider" placeholder="Provider name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Order/Tracking #</label>
                        <input type="text" class="form-control" id="trackingNumber" placeholder="Tracking number">
                    </div>
                </div>
            `;
            break;
        case 'other_income':
            description = 'Other income';
            html = `
                <div class="mb-3">
                    <label class="form-label">Income Category</label>
                    <select class="form-select" id="otherIncomeCategory">
                        <option value="Interest Earned">Interest Earned</option>
                        <option value="Refund">Refund Received</option>
                        <option value="Commission">Commission</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Source</label>
                    <input type="text" class="form-control" id="incomeSource" placeholder="Source of income">
                </div>
            `;
            break;
        case 'other_expense':
            description = 'Other expense';
            html = `
                <div class="mb-3">
                    <label class="form-label">Expense Type</label>
                    <select class="form-select" id="otherExpenseType">
                        <option value="Bank Charges">Bank Charges</option>
                        <option value="Licenses & Permits">Licenses & Permits</option>
                        <option value="Professional Fees">Professional Fees</option>
                        <option value="Travel">Travel</option>
                        <option value="Training">Training</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Payee</label>
                    <input type="text" class="form-control" id="payeeName" placeholder="Who was paid">
                </div>
            `;
            break;
        default:
            html = '';
            description = '';
    }
    
    if (description && !document.getElementById('transactionDescription').value) {
        document.getElementById('transactionDescription').value = description;
    }
    
    dynamicFields.innerHTML = html;
}

function saveTransaction(e) {
    e.preventDefault();
    
    const form = document.getElementById('transactionForm');
    const data = {
        type: document.getElementById('transactionType').value,
        scenario: document.getElementById('transactionScenario').value,
        amount: parseFloat(document.getElementById('transactionAmount').value),
        description: document.getElementById('transactionDescription').value,
        payment_method: document.getElementById('transactionPaymentMethod').value,
        transaction_date: document.getElementById('transactionDate').value,
        reference: document.getElementById('transactionReference').value,
        id: document.getElementById('transactionId').value || null
    };
    
    const dynamicFields = document.getElementById('dynamicFields');
    const inputs = dynamicFields.querySelectorAll('input, select');
    inputs.forEach(input => {
        data[input.id] = input.value;
    });
    
    if (!data.type || !data.scenario || !data.amount || data.amount <= 0) {
        alert('Please fill in all required fields');
        return;
    }
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    submitBtn.disabled = true;
    
    fetch(API_URL + 'save-transaction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (result.success) {
            alert('✅ Transaction saved successfully!');
            bootstrap.Modal.getInstance(document.getElementById('transactionModal')).hide();
            location.reload();
        } else {
            alert('❌ Error: ' + (result.error || 'Unknown error'));
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        alert('❌ Error saving transaction: ' + error);
    });
}

function getCategoryFromScenario(data) {
    const categories = {
        'invoice_paid': { category: 'Accounts Receivable', color: '#d4edda', textColor: '#155724' },
        'cash_sale': { category: 'Sales Revenue', color: '#d4edda', textColor: '#155724' },
        'subscription_paid': { category: 'Subscription Revenue', color: '#cce5ff', textColor: '#004085' },
        'gift_sale': { category: 'Gift Revenue', color: '#fff3cd', textColor: '#856404' },
        'other_income': { category: 'Other Income', color: '#d6d8db', textColor: '#383d41' },
        'supplier_invoice': { category: 'Accounts Payable', color: '#f8d7da', textColor: '#721c24' },
        'staff_payment': { category: 'Salaries & Wages', color: '#f8d7da', textColor: '#721c24' },
        'operating_expense': { category: 'Operating Expenses', color: '#f8d7da', textColor: '#721c24' },
        'inventory_purchase': { category: 'Inventory Purchases', color: '#f8d7da', textColor: '#721c24' },
        'delivery_cost': { category: 'Delivery & Shipping', color: '#f8d7da', textColor: '#721c24' },
        'other_expense': { category: 'Other Expenses', color: '#f8d7da', textColor: '#721c24' }
    };
    
    return categories[data.scenario] || { category: 'Uncategorized', color: '#e0e0e0', textColor: '#333' };
}

function viewTransaction(id) {
    fetch(API_URL + 'get-transaction.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            
            const body = document.getElementById('viewTransactionBody');
            body.innerHTML = `
                <div class="mb-3">
                    <strong>Description:</strong>
                    <p>${data.description || 'N/A'}</p>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Type:</strong>
                        <span class="badge ${data.type === 'income' ? 'bg-success' : 'bg-danger'}">${data.type}</span>
                    </div>
                    <div class="col-6">
                        <strong>Amount:</strong>
                        <span style="color:${data.type === 'income' ? 'var(--green)' : '#dc3545'};font-weight:bold;">E${parseFloat(data.amount).toFixed(2)}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Category:</strong>
                        <span class="category-badge" style="background:${data.category_color || '#eee'};color:${data.category_text_color || '#333'};">${data.category || 'Uncategorized'}</span>
                    </div>
                    <div class="col-6">
                        <strong>Status:</strong>
                        <span class="badge ${data.status === 'posted' ? 'bg-success' : 'bg-warning'}">${data.status}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Payment Method:</strong>
                        <span>${data.payment_method || 'N/A'}</span>
                    </div>
                    <div class="col-6">
                        <strong>Date:</strong>
                        <span>${data.transaction_date ? new Date(data.transaction_date).toLocaleDateString() : 'N/A'}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Reference:</strong>
                    <span>${data.reference || 'N/A'}</span>
                </div>
                <div class="mb-3">
                    <strong>Created:</strong>
                    <span>${data.created_at ? new Date(data.created_at).toLocaleString() : 'N/A'}</span>
                </div>
                ${data.auto_classified ? '<div class="alert alert-info"><i class="fas fa-robot me-2"></i>This transaction was automatically classified by the system.</div>' : ''}
                <hr>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteTransaction(${data.id})"><i class="fas fa-trash me-1"></i>Delete</button>
                    <button class="btn btn-outline-warning btn-sm" onclick="voidTransaction(${data.id})"><i class="fas fa-ban me-1"></i>Void</button>
                </div>
            `;
            
            new bootstrap.Modal(document.getElementById('viewTransactionModal')).show();
        })
        .catch(error => alert('Error loading transaction: ' + error));
}

function deleteTransaction(id) {
    if (confirm('Are you sure you want to delete this transaction? This cannot be undone.')) {
        fetch(API_URL + 'delete-transaction.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Transaction deleted.');
                bootstrap.Modal.getInstance(document.getElementById('viewTransactionModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (result.error || 'Unknown error'));
            }
        });
    }
}

function voidTransaction(id) {
    if (confirm('Void this transaction? It will be marked as void.')) {
        fetch(API_URL + 'void-transaction.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Transaction voided.');
                bootstrap.Modal.getInstance(document.getElementById('viewTransactionModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (result.error || 'Unknown error'));
            }
        });
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Any initialization code
});