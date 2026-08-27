<script>
    // ==================== GENERATE PERSISTENT SESSION ID ====================
    let sessionId = localStorage.getItem('cartSessionId');
    if (!sessionId) {
        sessionId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('cartSessionId', sessionId);
    }
    console.log('Session ID:', sessionId);

    // ==================== ORDER MANAGEMENT FUNCTIONS ====================
    
    function processOrder(id) {
        if (confirm('Process this order? This will mark it as processing.')) {
            window.location.href = '?section=orders&action=process_order&id=' + id;
        }
    }
    
    function cancelOrder(id) {
        if (confirm('Cancel this order? This cannot be undone.')) {
            window.location.href = '?section=orders&action=cancel_order&id=' + id;
        }
    }
    
    function completeOrder(id) {
        if (confirm('Mark this order as delivered?')) {
            window.location.href = '?section=orders&action=complete_order&id=' + id;
        }
    }
    
    function updateStock(id, currentStock) {
        let newStock = prompt('Enter new stock quantity for this wine:', currentStock);
        if (newStock !== null && !isNaN(newStock) && newStock >= 0) {
            window.location.href = '?section=wines&action=update_stock&id=' + id + '&stock=' + newStock;
        } else if (newStock !== null) {
            alert('Please enter a valid number');
        }
    }
    
    // ==================== INVOICE FUNCTIONS ====================
    
    function viewInvoice(id) {
        window.open(`../backend/generate-invoice.php?id=${id}`, '_blank', 'width=900,height=700');
    }
    
    function printInvoice() {
        window.print();
    }
    
    // ==================== PURCHASE ORDER FUNCTIONS ====================
    
    function openPurchaseOrder() {
        window.open('../backend/create-purchase-order.php', '_blank', 'width=1000,height=800');
    }
    
    // ==================== WINE MANAGEMENT FUNCTIONS ====================
    
    function showAddWineModal() {
        document.getElementById('modalTitle').innerText = 'Add New Wine';
        document.getElementById('modalBody').innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name *</label>
                    <input type="text" id="wineName" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Variety *</label>
                    <input type="text" id="wineVariety" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Origin *</label>
                    <input type="text" id="wineOrigin" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Price (E) *</label>
                    <input type="number" id="winePrice" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Stock Quantity</label>
                    <input type="number" id="wineStock" class="form-control" value="100">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Vintage</label>
                    <input type="number" id="wineVintage" class="form-control" placeholder="e.g., 2020">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Structure</label>
                    <input type="text" id="wineStructure" class="form-control" placeholder="Full-bodied, Crisp, Light">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Taste Notes</label>
                    <input type="text" id="wineTaste" class="form-control" placeholder="Fruity, Earthy, Spicy">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Alcohol %</label>
                    <input type="text" id="wineStrength" class="form-control" placeholder="14.5%">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Description</label>
                    <textarea id="wineDescription" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input type="checkbox" id="wineFeatured" class="form-check-input">
                        <label class="form-check-label">Featured Wine</label>
                    </div>
                </div>
            </div>
        `;
        window.currentEditId = null;
        new bootstrap.Modal(document.getElementById('itemModal')).show();
    }
    
    function editWine(id) {
        fetch(`../backend/get-wine.php?id=${id}`)
            .then(response => response.json())
            .then(wine => {
                document.getElementById('modalTitle').innerText = 'Edit Wine';
                document.getElementById('modalBody').innerHTML = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" id="wineName" class="form-control" value="${escapeHtml(wine.name)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Variety *</label>
                            <input type="text" id="wineVariety" class="form-control" value="${escapeHtml(wine.variety)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Origin *</label>
                            <input type="text" id="wineOrigin" class="form-control" value="${escapeHtml(wine.origin)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price (E) *</label>
                            <input type="number" id="winePrice" class="form-control" step="0.01" value="${wine.price}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Stock Quantity</label>
                            <input type="number" id="wineStock" class="form-control" value="${wine.stock_quantity}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Vintage</label>
                            <input type="number" id="wineVintage" class="form-control" value="${wine.vintage || ''}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Structure</label>
                            <input type="text" id="wineStructure" class="form-control" value="${escapeHtml(wine.structure || '')}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Taste Notes</label>
                            <input type="text" id="wineTaste" class="form-control" value="${escapeHtml(wine.taste || '')}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Alcohol %</label>
                            <input type="text" id="wineStrength" class="form-control" value="${escapeHtml(wine.strength || '')}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Description</label>
                            <textarea id="wineDescription" class="form-control" rows="2">${escapeHtml(wine.description || '')}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input type="checkbox" id="wineFeatured" class="form-check-input" ${wine.featured ? 'checked' : ''}>
                                <label class="form-check-label">Featured Wine</label>
                            </div>
                        </div>
                    </div>
                `;
                window.currentEditId = id;
                new bootstrap.Modal(document.getElementById('itemModal')).show();
            })
            .catch(error => alert('Error loading wine: ' + error));
    }
    
    function saveWine() {
        const data = {
            id: window.currentEditId || 0,
            name: document.getElementById('wineName').value,
            variety: document.getElementById('wineVariety').value,
            origin: document.getElementById('wineOrigin').value,
            price: parseFloat(document.getElementById('winePrice').value),
            stock_quantity: parseInt(document.getElementById('wineStock').value) || 0,
            vintage: parseInt(document.getElementById('wineVintage').value) || 0,
            structure: document.getElementById('wineStructure').value,
            taste: document.getElementById('wineTaste').value,
            strength: document.getElementById('wineStrength').value,
            description: document.getElementById('wineDescription').value,
            featured: document.getElementById('wineFeatured').checked ? 1 : 0
        };
        
        if (!data.name || !data.variety || !data.origin || !data.price) {
            alert('Please fill in all required fields');
            return;
        }
        
        const url = window.currentEditId ? '../backend/update-wine.php' : '../backend/add-wine.php';
        
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(window.currentEditId ? 'Wine updated successfully!' : 'Wine added successfully!');
                bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (result.error || 'Unknown error'));
            }
        })
        .catch(error => alert('Error saving wine: ' + error));
    }
    
    function deleteWine(id) {
        if (confirm('Are you sure you want to delete this wine? This cannot be undone.')) {
            fetch('../backend/delete-wine.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Wine deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            })
            .catch(error => alert('Error deleting wine: ' + error));
        }
    }
    
    // ==================== SUBSCRIPTION MANAGEMENT FUNCTIONS ====================
    
    function openSubscriptionModal() {
        document.getElementById('subscriptionModalTitle').innerText = 'Add Subscription Plan';
        document.getElementById('subscriptionId').value = '';
        document.getElementById('subTierName').value = '';
        document.getElementById('subDisplayName').value = '';
        document.getElementById('subTagline').value = '';
        document.getElementById('subPrice').value = '';
        document.getElementById('subWinesPerMonth').value = '';
        document.getElementById('subSavingsPercent').value = '0';
        document.getElementById('subDisplayOrder').value = '';
        document.getElementById('subIsPopular').checked = false;
        document.getElementById('subDescription').value = '';
        document.getElementById('subFeatures').value = '';
        document.getElementById('subPackaging').value = '';
        window.currentSubId = null;
        new bootstrap.Modal(document.getElementById('subscriptionModal')).show();
    }
    
    function editSubscription(id) {
        fetch(`../backend/get-subscription.php?id=${id}`)
            .then(response => response.json())
            .then(sub => {
                document.getElementById('subscriptionModalTitle').innerText = 'Edit Subscription Plan';
                document.getElementById('subscriptionId').value = sub.id;
                document.getElementById('subTierName').value = sub.tier_name || '';
                document.getElementById('subDisplayName').value = sub.display_name || '';
                document.getElementById('subTagline').value = sub.tagline || '';
                document.getElementById('subPrice').value = sub.price;
                document.getElementById('subWinesPerMonth').value = sub.wines_per_month;
                document.getElementById('subSavingsPercent').value = sub.savings_percent || 0;
                document.getElementById('subDisplayOrder').value = sub.display_order || '';
                document.getElementById('subIsPopular').checked = sub.is_popular == 1;
                document.getElementById('subDescription').value = sub.description || '';
                document.getElementById('subFeatures').value = sub.features || '';
                document.getElementById('subPackaging').value = sub.packaging || '';
                window.currentSubId = id;
                new bootstrap.Modal(document.getElementById('subscriptionModal')).show();
            })
            .catch(error => alert('Error loading subscription: ' + error));
    }
    
    function saveSubscription() {
        const data = {
            id: document.getElementById('subscriptionId').value || 0,
            tier_name: document.getElementById('subTierName').value,
            display_name: document.getElementById('subDisplayName').value,
            tagline: document.getElementById('subTagline').value,
            price: parseFloat(document.getElementById('subPrice').value),
            wines_per_month: parseInt(document.getElementById('subWinesPerMonth').value),
            savings_percent: parseInt(document.getElementById('subSavingsPercent').value) || 0,
            display_order: parseInt(document.getElementById('subDisplayOrder').value) || 0,
            is_popular: document.getElementById('subIsPopular').checked,
            description: document.getElementById('subDescription').value,
            features: document.getElementById('subFeatures').value,
            packaging: document.getElementById('subPackaging').value
        };
        
        if (!data.display_name || !data.price || !data.wines_per_month) {
            alert('Please fill in all required fields (Display Name, Price, Wines Per Month)');
            return;
        }
        
        const url = data.id ? '../backend/update-subscription.php' : '../backend/add-subscription.php';
        
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(data.id ? 'Subscription updated successfully!' : 'Subscription added successfully!');
                bootstrap.Modal.getInstance(document.getElementById('subscriptionModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (result.error || 'Unknown error'));
            }
        })
        .catch(error => alert('Error saving subscription: ' + error));
    }
    
    function deleteSubscription(id) {
        if (confirm('Are you sure you want to delete this subscription plan? This cannot be undone.')) {
            fetch('../backend/delete-subscription.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Subscription deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            })
            .catch(error => alert('Error deleting subscription: ' + error));
        }
    }
    
    // ==================== SUBSCRIPTION REQUEST FUNCTIONS ====================
    
    function approveSubscription(id) {
        if (!confirm('Approve this subscription request?\n\nThe user will receive a confirmation email with their 30-day subscription details.')) {
            return;
        }
        
        showToast('Processing approval...', true);
        
        fetch('../backend/approve-subscription.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('✅ Subscription approved! User has been notified via email.', true);
                setTimeout(() => { location.reload(); }, 2000);
            } else {
                showToast('❌ Error: ' + (data.error || 'Unknown error'), false);
            }
        })
        .catch(error => {
            showToast('❌ Error processing request: ' + error, false);
        });
    }
    
    function rejectSubscription(id) {
        if (!confirm('Reject this subscription request?\n\nThis action cannot be undone.')) {
            return;
        }
        
        let reason = prompt('Please provide a reason for rejection (optional):');
        if (reason === null) {
            return;
        }
        
        showToast('Processing rejection...', true);
        
        fetch('../backend/reject-subscription.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                id: id, 
                reason: reason.trim() || 'No reason provided' 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('❌ Subscription rejected.', false);
                setTimeout(() => { location.reload(); }, 2000);
            } else {
                showToast('❌ Error: ' + (data.error || 'Unknown error'), false);
            }
        })
        .catch(error => {
            showToast('❌ Error processing request: ' + error, false);
        });
    }
    
    function viewPOP(path) {
        if (path) {
            window.open(path, '_blank', 'width=800,height=600');
        } else {
            showToast('No POP file uploaded for this request.', false);
        }
    }
    
    function filterSubscriptionRequests(status) {
        const rows = document.querySelectorAll('.sub-request-row');
        rows.forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        document.querySelectorAll('.sub-filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`.sub-filter-btn[data-status="${status}"]`)?.classList.add('active');
    }
    
    // ==================== STAFF MANAGEMENT FUNCTIONS ====================
    
    let currentEditStaffId = null;
    
    function showAddStaffModal() {
        document.getElementById('newStaffName').value = '';
        document.getElementById('newStaffEmail').value = '';
        document.getElementById('newStaffPassword').value = '';
        document.getElementById('newStaffConfirm').value = '';
        document.getElementById('newStaffRole').value = 'staff';
        currentEditStaffId = null;
        new bootstrap.Modal(document.getElementById('addStaffModal')).show();
    }
    
    function editStaff(id, name, email, role) {
        currentEditStaffId = id;
        document.getElementById('editStaffName').value = name;
        document.getElementById('editStaffEmail').value = email;
        document.getElementById('editStaffRole').value = role;
        new bootstrap.Modal(document.getElementById('editStaffModal')).show();
    }
    
    function confirmEditStaff() {
        const name = document.getElementById('editStaffName').value;
        const email = document.getElementById('editStaffEmail').value;
        const role = document.getElementById('editStaffRole').value;
        
        if (!name || !email) {
            alert('Please fill in all required fields');
            return;
        }
        
        fetch('../backend/update-staff.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: currentEditStaffId, name, email, role })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Staff updated successfully!');
                bootstrap.Modal.getInstance(document.getElementById('editStaffModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => alert('Error updating staff: ' + error));
    }
    
    function confirmAddStaff() {
        const name = document.getElementById('newStaffName').value;
        const email = document.getElementById('newStaffEmail').value;
        const password = document.getElementById('newStaffPassword').value;
        const confirm = document.getElementById('newStaffConfirm').value;
        const role = document.getElementById('newStaffRole').value;
        
        if (!name || !email || !password) {
            alert('Please fill in all required fields');
            return;
        }
        
        if (password !== confirm) {
            alert('Passwords do not match');
            return;
        }
        
        if (password.length < 6) {
            alert('Password must be at least 6 characters');
            return;
        }
        
        fetch('../backend/add-staff.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email, password, role })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Staff added successfully!');
                bootstrap.Modal.getInstance(document.getElementById('addStaffModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => alert('Error adding staff: ' + error));
    }
    
    function resetStaffPassword(id, email) {
        document.getElementById('resetStaffId').value = id;
        document.getElementById('resetStaffEmail').value = email;
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
        new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
    }
    
    function confirmResetPassword() {
        const id = document.getElementById('resetStaffId').value;
        const password = document.getElementById('newPassword').value;
        const confirm = document.getElementById('confirmPassword').value;
        
        if (!password) {
            alert('Please enter a new password');
            return;
        }
        
        if (password !== confirm) {
            alert('Passwords do not match');
            return;
        }
        
        if (password.length < 6) {
            alert('Password must be at least 6 characters');
            return;
        }
        
        fetch('../backend/reset-staff-password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Password reset successfully!\nNew password: ' + password);
                bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal')).hide();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => alert('Error resetting password: ' + error));
    }
    
    function deleteStaff(id) {
        if (confirm('Are you sure you want to delete this staff member? This action cannot be undone.')) {
            fetch('../backend/delete-staff.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Staff deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => alert('Error deleting staff: ' + error));
        }
    }
    
    // ==================== PAIRING MANAGEMENT FUNCTIONS ====================
    
    function showAddPairingModal() {
        document.getElementById('modalTitle').innerText = 'Add New Pairing';
        document.getElementById('modalBody').innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name *</label>
                    <input type="text" id="pairingName" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Price (E) *</label>
                    <input type="number" id="pairingPrice" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Description *</label>
                    <textarea id="pairingDescription" class="form-control" rows="3" required></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Compatible Wines</label>
                    <input type="text" id="pairingCompatibleWines" class="form-control" placeholder="Cabernet Sauvignon, Merlot, Pinot Noir">
                </div>
            </div>
        `;
        window.currentPairingId = null;
        new bootstrap.Modal(document.getElementById('itemModal')).show();
    }
    
    function editPairing(id) {
        fetch(`../backend/get-pairing.php?id=${id}`)
            .then(response => response.json())
            .then(pairing => {
                document.getElementById('modalTitle').innerText = 'Edit Pairing';
                document.getElementById('modalBody').innerHTML = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" id="pairingName" class="form-control" value="${escapeHtml(pairing.name)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price (E) *</label>
                            <input type="number" id="pairingPrice" class="form-control" step="0.01" value="${pairing.price}" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Description *</label>
                            <textarea id="pairingDescription" class="form-control" rows="3" required>${escapeHtml(pairing.description)}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Compatible Wines</label>
                            <input type="text" id="pairingCompatibleWines" class="form-control" value="${escapeHtml(pairing.compatible_wines || '')}">
                        </div>
                    </div>
                `;
                window.currentPairingId = id;
                new bootstrap.Modal(document.getElementById('itemModal')).show();
            })
            .catch(error => alert('Error loading pairing: ' + error));
    }
    
    function savePairing() {
        const data = {
            id: window.currentPairingId || 0,
            name: document.getElementById('pairingName').value,
            description: document.getElementById('pairingDescription').value,
            price: parseFloat(document.getElementById('pairingPrice').value),
            compatible_wines: document.getElementById('pairingCompatibleWines').value
        };
        
        if (!data.name || !data.description || !data.price) {
            alert('Please fill in all required fields');
            return;
        }
        
        const url = window.currentPairingId ? '../backend/update-pairing.php' : '../backend/add-pairing.php';
        
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(window.currentPairingId ? 'Pairing updated successfully!' : 'Pairing added successfully!');
                bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (result.error || 'Unknown error'));
            }
        })
        .catch(error => alert('Error saving pairing: ' + error));
    }
    
    function deletePairing(id) {
        if (confirm('Are you sure you want to delete this pairing?')) {
            fetch('../backend/delete-pairing.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Pairing deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            })
            .catch(error => alert('Error deleting pairing: ' + error));
        }
    }
    
    // ==================== CORPORATE GIFT FUNCTIONS ====================
    
    function showAddCorporateGiftModal() {
        document.getElementById('modalTitle').innerText = 'Add Corporate Gift';
        document.getElementById('modalBody').innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name *</label>
                    <input type="text" id="giftName" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Tier *</label>
                    <input type="text" id="giftTier" class="form-control" placeholder="Executive, Boardroom" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Price (E) *</label>
                    <input type="number" id="giftPrice" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Wines Included</label>
                    <input type="number" id="giftWines" class="form-control" value="3">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Description *</label>
                    <textarea id="giftDescription" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Features</label>
                    <input type="text" id="giftFeatures" class="form-control" placeholder="3 Premium Wines, Chocolates, Card">
                </div>
            </div>
        `;
        window.currentGiftId = null;
        new bootstrap.Modal(document.getElementById('itemModal')).show();
    }
    
    function editCorporateGift(id) {
        fetch(`../backend/get-corporate-gift.php?id=${id}`)
            .then(response => response.json())
            .then(gift => {
                document.getElementById('modalTitle').innerText = 'Edit Corporate Gift';
                document.getElementById('modalBody').innerHTML = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" id="giftName" class="form-control" value="${escapeHtml(gift.name)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tier *</label>
                            <input type="text" id="giftTier" class="form-control" value="${escapeHtml(gift.tier)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price (E) *</label>
                            <input type="number" id="giftPrice" class="form-control" step="0.01" value="${gift.price}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Wines Included</label>
                            <input type="number" id="giftWines" class="form-control" value="${gift.wines_included || 3}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Description *</label>
                            <textarea id="giftDescription" class="form-control" rows="2" required>${escapeHtml(gift.description)}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Features</label>
                            <input type="text" id="giftFeatures" class="form-control" value="${escapeHtml(gift.features)}">
                        </div>
                    </div>
                `;
                window.currentGiftId = id;
                new bootstrap.Modal(document.getElementById('itemModal')).show();
            })
            .catch(error => alert('Error loading gift: ' + error));
    }
    
    function saveCorporateGift() {
        const data = {
            id: window.currentGiftId || 0,
            name: document.getElementById('giftName').value,
            tier: document.getElementById('giftTier').value,
            description: document.getElementById('giftDescription').value,
            features: document.getElementById('giftFeatures').value,
            price: parseFloat(document.getElementById('giftPrice').value),
            wines_included: parseInt(document.getElementById('giftWines').value) || 0
        };
        
        if (!data.name || !data.tier || !data.description || !data.price) {
            alert('Please fill in all required fields');
            return;
        }
        
        const url = window.currentGiftId ? '../backend/update-corporate-gift.php' : '../backend/add-corporate-gift.php';
        
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(window.currentGiftId ? 'Corporate gift updated!' : 'Corporate gift added!');
                bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (result.error || 'Unknown error'));
            }
        })
        .catch(error => alert('Error saving: ' + error));
    }
    
    function deleteCorporateGift(id) {
        if (confirm('Delete this corporate gift?')) {
            fetch('../backend/delete-corporate-gift.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            })
            .catch(error => alert('Error deleting: ' + error));
        }
    }
    
    // ==================== GIFT BASKET FUNCTIONS ====================
    
    function showAddGiftBasketModal() {
        document.getElementById('modalTitle').innerText = 'Add Gift Basket';
        document.getElementById('modalBody').innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Name *</label>
                    <input type="text" id="basketName" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Price (E) *</label>
                    <input type="number" id="basketPrice" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Wines Included</label>
                    <input type="number" id="basketWines" class="form-control" value="2">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Description *</label>
                    <textarea id="basketDescription" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Features</label>
                    <input type="text" id="basketFeatures" class="form-control" placeholder="2 wines, chocolates, cheese">
                </div>
            </div>
        `;
        window.currentBasketId = null;
        new bootstrap.Modal(document.getElementById('itemModal')).show();
    }
    
    function editGiftBasket(id) {
        fetch(`../backend/get-gift-basket.php?id=${id}`)
            .then(response => response.json())
            .then(basket => {
                document.getElementById('modalTitle').innerText = 'Edit Gift Basket';
                document.getElementById('modalBody').innerHTML = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name *</label>
                            <input type="text" id="basketName" class="form-control" value="${escapeHtml(basket.name)}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price (E) *</label>
                            <input type="number" id="basketPrice" class="form-control" step="0.01" value="${basket.price}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Wines Included</label>
                            <input type="number" id="basketWines" class="form-control" value="${basket.wines_included || 2}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Description *</label>
                            <textarea id="basketDescription" class="form-control" rows="2" required>${escapeHtml(basket.description)}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Features</label>
                            <input type="text" id="basketFeatures" class="form-control" value="${escapeHtml(basket.features)}">
                        </div>
                    </div>
                `;
                window.currentBasketId = id;
                new bootstrap.Modal(document.getElementById('itemModal')).show();
            })
            .catch(error => alert('Error loading basket: ' + error));
    }
    
    function saveGiftBasket() {
        const data = {
            id: window.currentBasketId || 0,
            name: document.getElementById('basketName').value,
            description: document.getElementById('basketDescription').value,
            features: document.getElementById('basketFeatures').value,
            price: parseFloat(document.getElementById('basketPrice').value),
            wines_included: parseInt(document.getElementById('basketWines').value) || 0
        };
        
        if (!data.name || !data.description || !data.price) {
            alert('Please fill in all required fields');
            return;
        }
        
        const url = window.currentBasketId ? '../backend/update-gift-basket.php' : '../backend/add-gift-basket.php';
        
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(window.currentBasketId ? 'Gift basket updated!' : 'Gift basket added!');
                bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (result.error || 'Unknown error'));
            }
        })
        .catch(error => alert('Error saving: ' + error));
    }
    
    function deleteGiftBasket(id) {
        if (confirm('Delete this gift basket?')) {
            fetch('../backend/delete-gift-basket.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            })
            .catch(error => alert('Error deleting: ' + error));
        }
    }
    
    // ==================== CART FUNCTIONS ====================
    
    function showToast(message, isSuccess = true) {
        let toast = document.getElementById('customToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'customToast';
            toast.style.cssText = `
                position: fixed; 
                bottom: 20px; 
                right: 20px; 
                padding: 12px 24px; 
                border-radius: 40px; 
                z-index: 9999; 
                display: none; 
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                font-family: 'Segoe UI', system-ui;
                max-width: 400px;
            `;
            document.body.appendChild(toast);
        }
        
        toast.style.backgroundColor = isSuccess ? '#1a6b3c' : '#dc3545';
        toast.style.color = 'white';
        toast.innerText = message;
        toast.style.display = 'block';
        
        setTimeout(() => { toast.style.display = 'none'; }, 3000);
    }
    
    function updateCartCount() {
        fetch('../backend/get-cart-count.php?sessionId=' + sessionId)
            .then(response => response.json())
            .then(data => {
                const count = data.count || 0;
                document.querySelectorAll('.cart-count').forEach(el => el.innerText = count);
                const navCart = document.getElementById('cartCount');
                if (navCart) navCart.innerText = count;
            })
            .catch(() => {
                let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                const total = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
                document.querySelectorAll('.cart-count').forEach(el => el.innerText = total);
            });
    }
    
    function addToCart(id, name, price, type = 'wine') {
        console.log('Adding to cart:', { id, name, price, type, sessionId });
        
        fetch('../backend/add-to-cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                productId: id, 
                productType: type, 
                productName: name, 
                price: price, 
                quantity: 1,
                sessionId: sessionId 
            })
        })
        .then(response => response.json())
        .then(result => {
            console.log('Response:', result);
            if (result.success) {
                showToast(`✓ ${name} added to cart!`);
                updateCartCount();
            } else {
                showToast('Error: ' + (result.error || 'Could not add to cart'), false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error adding to cart. Please try again.', false);
        });
    }
    
    // ==================== CHECKOUT FUNCTIONS ====================
    
    function loadOrderSummary() {
        const orderSummary = document.getElementById('orderSummary');
        if (!orderSummary) return;
        
        fetch('../backend/get-cart.php?sessionId=' + sessionId)
            .then(response => response.json())
            .then(data => {
                const items = data.items || [];
                if (!items.length) {
                    window.location.href = 'cart.php';
                    return;
                }
                
                let subtotal = 0;
                let html = '';
                items.forEach(item => {
                    const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
                    subtotal += itemTotal;
                    html += `
                        <div class="order-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>${escapeHtml(item.product_name)}</strong>
                                    <br>
                                    <small class="text-muted">${item.quantity} × E${parseFloat(item.price).toFixed(2)}</small>
                                </div>
                                <span>E${itemTotal.toFixed(2)}</span>
                            </div>
                        </div>
                    `;
                });
                
                const tax = subtotal * 0.15;
                // Delivery fee: E50 if subtotal < 500, otherwise FREE
                const shipping = subtotal > 500 ? 0 : 50;
                const total = subtotal + tax + shipping;
                
                html += `
                    <div class="mt-3">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>E${subtotal.toFixed(2)}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tax (15% VAT):</span>
                            <span>E${tax.toFixed(2)}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Delivery Fee:</span>
                            <span>${shipping === 0 ? 'FREE' : 'E' + shipping.toFixed(2)}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong style="color: #722f37; font-size: 1.2rem;">E${total.toFixed(2)}</strong>
                        </div>
                    </div>
                `;
                
                orderSummary.innerHTML = html;
                
                // Store for order submission
                window.checkoutData = {
                    items: items,
                    subtotal: subtotal,
                    tax: tax,
                    shipping: shipping,
                    total: total
                };
            })
            .catch(error => {
                console.error('Error loading cart:', error);
                orderSummary.innerHTML = '<div class="alert alert-danger">Error loading cart</div>';
            });
    }
    
    function placeOrder(event) {
    if (event) event.preventDefault();
    
    const fullName = document.getElementById('fullName')?.value.trim();
    const email = document.getElementById('email')?.value.trim();
    const phone = document.getElementById('phone')?.value.trim();
    const address = document.getElementById('address')?.value.trim();
    const paymentMethod = document.getElementById('paymentMethod')?.value || 'cash';
    const submitBtn = document.getElementById('placeOrderBtn');
    
    // Validate
    if (!fullName || !email || !phone || !address) {
        showToast('Please fill in all required fields', false);
        return;
    }
    
    if (!email.includes('@')) {
        showToast('Please enter a valid email address', false);
        return;
    }
    
    // Check POP for non-cash payments
    if (paymentMethod !== 'cash') {
        const fileInput = document.getElementById('popFile');
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            showToast('Please upload proof of payment (POP)', false);
            return;
        }
    }
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    }
    
    const orderData = {
        sessionId: sessionId,
        customerName: fullName,
        customerEmail: email,
        customerPhone: phone,
        customerAddress: address,
        paymentMethod: paymentMethod,
        items: window.checkoutData?.items || [],
        total: window.checkoutData?.total || 0,
        popUploaded: document.getElementById('popFile')?.files[0]?.name || null
    };
    
    fetch('../backend/place-order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showToast(`✅ Order placed successfully! Order #${result.orderNumber}`, true);
            // Clear cart from localStorage
            localStorage.removeItem('cart');
            setTimeout(() => {
                window.location.href = 'order-confirmation.php?order_id=' + result.orderId;
            }, 1500);
        } else {
            showToast('❌ Error: ' + (result.error || 'Could not place order'), false);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Place Order';
            }
        }
    })
    .catch(error => {
        console.error('Error placing order:', error);
        showToast('❌ Network error. Please try again.', false);
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Place Order';
        }
    });
}
        })
        .catch(error => {
            console.error('Error placing order:', error);
            showToast('❌ Network error. Please try again.', false);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Place Order';
            }
        });
    }
    
    // ==================== HELPER FUNCTIONS ====================
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatCurrency(amount) {
        return 'E' + parseFloat(amount).toFixed(2);
    }
    
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-ZA', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }
    
    function getStatusBadgeClass(status) {
        const classes = {
            'pending': 'bg-warning',
            'approved': 'bg-info',
            'active': 'bg-success',
            'expired': 'bg-danger',
            'cancelled': 'bg-secondary',
            'processing': 'bg-primary',
            'completed': 'bg-success'
        };
        return classes[status] || 'bg-secondary';
    }
    
    // ==================== SAVE ITEM DISPATCHER ====================
    
    function saveItem() {
        const modalTitle = document.getElementById('modalTitle').innerText;
        if (modalTitle.includes('Wine')) {
            saveWine();
        } else if (modalTitle.includes('Pairing')) {
            savePairing();
        } else if (modalTitle.includes('Corporate Gift')) {
            saveCorporateGift();
        } else if (modalTitle.includes('Gift Basket')) {
            saveGiftBasket();
        } else {
            alert('Save feature - implement for this item type');
            bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
        }
    }
    
    // ==================== PAGE INITIALIZATION ====================
    
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
        
        // Initialize subscription request filters if they exist
        const filterBtns = document.querySelectorAll('.sub-filter-btn');
        if (filterBtns.length > 0) {
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterSubscriptionRequests(this.dataset.status);
                });
            });
        }
        
        // Load order summary if on checkout page
       function loadOrderSummary() {
    const orderSummary = document.getElementById('orderSummary');
    if (!orderSummary) return;
    
    fetch('../backend/get-cart.php?sessionId=' + sessionId)
        .then(response => response.json())
        .then(data => {
            const items = data.items || [];
            if (!items.length) {
                window.location.href = 'cart.php';
                return;
            }
            
            let subtotal = 0;
            let html = '';
            items.forEach(item => {
                const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
                subtotal += itemTotal;
                html += `
                    <div class="order-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>${escapeHtml(item.product_name)}</strong>
                                <br>
                                <small class="text-muted">${item.quantity} × E${parseFloat(item.price).toFixed(2)}</small>
                            </div>
                            <span>E${itemTotal.toFixed(2)}</span>
                        </div>
                    </div>
                `;
            });
            
            const tax = subtotal * 0.15;
            const shipping = subtotal > 500 ? 0 : 50;
            const total = subtotal + tax + shipping;
            
            html += `
                <div class="mt-3">
                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span>E${subtotal.toFixed(2)}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Tax (15% VAT):</span>
                        <span>E${tax.toFixed(2)}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Delivery Fee:</span>
                        <span>${shipping === 0 ? 'FREE' : 'E' + shipping.toFixed(2)}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong style="color: #722f37; font-size: 1.2rem;">E${total.toFixed(2)}</strong>
                    </div>
                </div>
            `;
            
            orderSummary.innerHTML = html;
            
            // Store for order submission
            window.checkoutData = {
                items: items,
                subtotal: subtotal,
                tax: tax,
                shipping: shipping,
                total: total
            };
        })
        .catch(error => {
            console.error('Error loading cart:', error);
            orderSummary.innerHTML = '<div class="alert alert-danger">Error loading cart</div>';
        });
}
</script>