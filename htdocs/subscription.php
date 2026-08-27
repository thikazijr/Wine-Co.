<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wine Club - Wine & Co. Eswatini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { 
            --wine: #722f37; 
            --wine-light: #9e4a55; 
            --gold: #c9a03d; 
            --green: #1a6b3c;
            --dark: #1a1a2e;
        }
        body { background: linear-gradient(135deg, #fdf8f5 0%, #f5ede6 100%); font-family: 'Segoe UI', system-ui; }
        
        .navbar { background: white; border-bottom: 2px solid var(--gold); box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar-brand { color: var(--wine) !important; font-size: 1.8rem; font-weight: 700; }
        .navbar-brand span { font-size: 0.8rem; }
        .nav-link { color: #4a2c2a !important; font-weight: 500; transition: color 0.2s; }
        .nav-link:hover { color: var(--wine) !important; }
        
        .btn-wine { background: linear-gradient(135deg, var(--wine), var(--wine-light)); color: white; border-radius: 40px; padding: 8px 28px; border: none; transition: all 0.3s; }
        .btn-wine:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(114,47,55,0.3); color: white; }
        .btn-outline-wine { border: 2px solid var(--wine); color: var(--wine); border-radius: 40px; padding: 6px 24px; background: transparent; transition: all 0.3s; }
        .btn-outline-wine:hover { background: var(--wine); color: white; }
        .btn-gold { background: var(--gold); color: #1a1a2e; border-radius: 40px; padding: 10px 30px; border: none; font-weight: 600; transition: all 0.3s; }
        .btn-gold:hover { background: #b8922f; transform: translateY(-2px); }
        
        .subscription-card { border: none; border-radius: 24px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); transition: all 0.3s; background: white; height: 100%; }
        .subscription-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(114,47,55,0.15); }
        .featured-plan { border: 2px solid var(--gold); position: relative; background: linear-gradient(white, #fff8f0); }
        .popular-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--gold); color: #1a1a2e; padding: 6px 20px; border-radius: 30px; font-size: 0.8rem; font-weight: bold; white-space: nowrap; }
        .price-sub { font-size: 2.2rem; font-weight: bold; color: var(--green); }
        
        .feature-list { list-style: none; padding: 0; text-align: left; margin-top: 20px; }
        .feature-list li { padding: 8px 0; border-bottom: 1px solid #eee; }
        .feature-list li i { color: var(--green); margin-right: 10px; width: 20px; }
        
        .cart-icon { position: relative; color: var(--wine); text-decoration: none; font-size: 1.2rem; }
        .cart-count { position: absolute; top: -10px; right: -15px; background: var(--gold); color: #333; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; }
        
        .subscription-modal .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .subscription-modal .modal-header {
            background: linear-gradient(135deg, var(--wine), var(--wine-light));
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 20px 30px;
        }
        .subscription-modal .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        .subscription-modal .modal-body {
            padding: 30px;
        }
        .subscription-modal .form-control,
        .subscription-modal .form-select {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e8e0d8;
            transition: 0.3s;
        }
        .subscription-modal .form-control:focus,
        .subscription-modal .form-select:focus {
            border-color: var(--wine);
            box-shadow: 0 0 0 3px rgba(114,47,55,0.1);
        }
        .subscription-modal .form-label {
            font-weight: 600;
            color: #2c1a1a;
        }
        .subscription-modal .form-label .required {
            color: #dc3545;
        }
        
        .upload-area {
            border: 2px dashed #d4c5b8;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            background: #faf8f6;
        }
        .upload-area:hover {
            border-color: var(--wine);
            background: #f5ede6;
        }
        .upload-area.dragover {
            border-color: var(--green);
            background: #e8f5e9;
        }
        .upload-area i {
            font-size: 3rem;
            color: #b8a89a;
        }
        .upload-area p {
            margin: 10px 0 0;
            color: #888;
        }
        .upload-area .file-name {
            color: var(--wine);
            font-weight: 600;
        }
        
        .payment-info {
            background: #f8f4f0;
            border-radius: 12px;
            padding: 15px 20px;
            margin: 15px 0;
        }
        .payment-info h6 {
            color: var(--wine);
        }
        
        .plan-summary {
            background: linear-gradient(135deg, #faf8f6, #f5ede6);
            border-radius: 12px;
            padding: 15px 20px;
            margin: 15px 0;
            border-left: 4px solid var(--gold);
        }
        
        .toast-notification { 
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
        }
        .toast-success { background: var(--green); color: white; }
        .toast-error { background: #dc3545; color: white; }
        
        footer { background: var(--dark); color: #aaa; margin-top: 60px; padding: 50px 0; }
        footer a { color: #aaa; text-decoration: none; }
        footer a:hover { color: var(--gold); }
        
        .loading { text-align: center; padding: 50px; font-size: 1.2rem; color: var(--wine); }
        
        @media (max-width: 768px) {
            .price-sub { font-size: 1.5rem; }
            .subscription-card { margin-bottom: 20px; }
            .subscription-modal .modal-body { padding: 20px; }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container mt-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Join the Wine & Co. Club</h2>
            <p class="lead text-muted">Choose the perfect plan for your wine journey</p>
            <p class="text-muted">Cancel anytime • Free shipping on all plans • Members-only wines</p>
        </div>
        <div class="row" id="subscriptionsList">
            <div class="col-12 text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--wine);"></i>
                <p class="mt-3 text-muted">Loading plans...</p>
            </div>
        </div>
        <div class="alert alert-info text-center mt-4">
            <i class="fas fa-credit-card me-2"></i> Secure payments • Cancel anytime
        </div>
    </div>

    <!-- SUBSCRIPTION FORM MODAL -->
    <div class="modal fade subscription-modal" id="subscriptionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold"><i class="fas fa-wine-bottle me-2"></i>Subscribe to Wine Club</h5>
                        <small class="opacity-75">Complete the form below to activate your subscription</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="subscriptionForm" enctype="multipart/form-data">
                        <input type="hidden" id="subPlanId" name="plan_id">
                        <input type="hidden" id="subPlanName" name="plan_name">
                        <input type="hidden" id="subPrice" name="price">
                        
                        <div class="plan-summary" id="planSummary">
                            <h6><i class="fas fa-gem me-2"></i>Selected Plan</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <span id="selectedPlanName" class="fw-bold">-</span>
                                <span id="selectedPlanPrice" class="fw-bold" style="color: var(--green);">-</span>
                            </div>
                            <small class="text-muted">30-day subscription • Valid from today</small>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="subFullName" name="full_name" required placeholder="e.g. John Doe">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" id="subEmail" name="email" required placeholder="john@example.com">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number <span class="required">*</span></label>
                                <input type="tel" class="form-control" id="subPhone" name="phone" required placeholder="+268 1234 5678">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Delivery Address <span class="required">*</span></label>
                                <input type="text" class="form-control" id="subAddress" name="address" required placeholder="Street, City, Eswatini">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City <span class="required">*</span></label>
                                <select class="form-select" id="subCity" name="city" required>
                                    <option value="">Select your city</option>
                                    <option value="Mbabane">Mbabane</option>
                                    <option value="Manzini">Manzini</option>
                                    <option value="Ezulwini">Ezulwini</option>
                                    <option value="Matsapha">Matsapha</option>
                                    <option value="Lobamba">Lobamba</option>
                                    <option value="Nhlangano">Nhlangano</option>
                                    <option value="Pigg's Peak">Pigg's Peak</option>
                                    <option value="Big Bend">Big Bend</option>
                                    <option value="Mhlume">Mhlume</option>
                                    <option value="Siteki">Siteki</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ID Number (Optional)</label>
                                <input type="text" class="form-control" id="subIdNumber" name="id_number" placeholder="e.g. 1234567890123">
                            </div>
                        </div>
                        
                        <h6 class="mt-4 mb-3"><i class="fas fa-money-bill-wave me-2" style="color: var(--green);"></i>Payment Information</h6>
                        
                        <div class="payment-info">
                            <h6>Bank Transfer Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Bank:</strong> Standard Bank Eswatini</p>
                                    <p class="mb-1"><strong>Account Name:</strong> Wine & Co. (Pty) Ltd</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Account Number:</strong> 1234567890</p>
                                    <p class="mb-1"><strong>Reference:</strong> [Your Name] - Wine Club</p>
                                </div>
                            </div>
                            <small class="text-muted">* Please use your full name as reference for easy identification</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="required">*</span></label>
                            <div class="d-flex gap-3 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paymentBank" value="bank_transfer" checked>
                                    <label class="form-check-label" for="paymentBank">
                                        <i class="fas fa-university me-1"></i> Bank Transfer
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paymentMobile" value="mobile_money">
                                    <label class="form-check-label" for="paymentMobile">
                                        <i class="fas fa-mobile-alt me-1"></i> Mobile Money
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paymentEwallet" value="e-wallet">
                                    <label class="form-check-label" for="paymentEwallet">
                                        <i class="fas fa-wallet me-1"></i> E-Wallet
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Upload Payment Proof / POP <span class="required">*</span></label>
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Drag & drop your payment proof here or <strong>click to browse</strong></p>
                                <p class="file-name" id="fileNameDisplay">No file selected</p>
                                <input type="file" id="popFile" name="pop_file" accept=".pdf,.jpg,.jpeg,.png,.gif" style="display:none" required>
                            </div>
                            <small class="text-muted">Supported formats: PDF, JPG, PNG, GIF (Max 5MB)</small>
                        </div>
                        
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="termsCheck" required>
                            <label class="form-check-label" for="termsCheck">
                                I agree to the Terms & Conditions and confirm that:
                                <ul class="mt-2 small">
                                    <li>I am 18 years or older</li>
                                    <li>My subscription will be valid for 30 days from today</li>
                                    <li>I understand this is a recurring subscription</li>
                                </ul>
                            </label>
                        </div>
                        
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-clock me-2"></i>
                            <strong>30-Day Validity:</strong> Your subscription will be active for 30 days from the date of approval. 
                            You will receive an email confirmation once your payment is verified.
                        </div>
                        
                        <button type="submit" class="btn btn-gold w-100 mt-3" id="submitSubscription">
                            <i class="fas fa-check-circle me-2"></i>Submit Subscription
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <div id="toast" class="toast-notification"></div>

    <script>
        const API_URL = 'backend/';
        
        // Use consistent session ID
        let sessionId = localStorage.getItem('cartSessionId');
        if (!sessionId) {
            sessionId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('cartSessionId', sessionId);
        }

        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            if (!toast) return;
            toast.innerText = message;
            toast.className = 'toast-notification ' + (isError ? 'toast-error' : 'toast-success');
            toast.style.display = 'block';
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => { toast.style.display = 'none'; }, 4000);
        }

        function updateCartCount() {
            fetch(API_URL + 'get-cart-count.php?sessionId=' + sessionId)
                .then(r => r.json())
                .then(data => {
                    const count = data.count || 0;
                    document.querySelectorAll('.cart-count').forEach(el => el.innerText = count);
                })
                .catch(() => {
                    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                    const total = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
                    document.querySelectorAll('.cart-count').forEach(el => el.innerText = total);
                });
        }

        function openSubscriptionModal(planId, planName, price) {
            document.getElementById('subPlanId').value = planId;
            document.getElementById('subPlanName').value = planName;
            document.getElementById('subPrice').value = price;
            document.getElementById('selectedPlanName').innerText = planName;
            document.getElementById('selectedPlanPrice').innerText = `E${parseFloat(price).toFixed(2)}/month`;
            
            document.getElementById('subscriptionForm').reset();
            document.getElementById('fileNameDisplay').innerText = 'No file selected';
            document.getElementById('popFile').value = '';
            document.getElementById('termsCheck').checked = false;
            
            new bootstrap.Modal(document.getElementById('subscriptionModal')).show();
        }

        function subscribe(planName, price) {
            const card = event.target.closest('.subscription-card');
            if (!card) return;
            const planId = card.dataset.planId;
            openSubscriptionModal(planId, planName, price);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('popFile');
            const fileNameDisplay = document.getElementById('fileNameDisplay');

            if (uploadArea) {
                uploadArea.addEventListener('click', function() {
                    fileInput.click();
                });

                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                });

                uploadArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                });

                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                    if (e.dataTransfer.files.length > 0) {
                        fileInput.files = e.dataTransfer.files;
                        updateFileName(fileInput.files[0]);
                    }
                });
            }

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        const file = this.files[0];
                        if (file.size > 5 * 1024 * 1024) {
                            showToast('File too large! Maximum 5MB allowed.', true);
                            this.value = '';
                            fileNameDisplay.innerText = 'No file selected';
                            return;
                        }
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                        if (!allowedTypes.includes(file.type)) {
                            showToast('Invalid file type! Please upload PDF, JPG, PNG, or GIF.', true);
                            this.value = '';
                            fileNameDisplay.innerText = 'No file selected';
                            return;
                        }
                        updateFileName(file);
                    }
                });
            }

            function updateFileName(file) {
                fileNameDisplay.innerText = `✅ ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                fileNameDisplay.style.color = '#1a6b3c';
            }

            const form = document.getElementById('subscriptionForm');
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const fileInput = document.getElementById('popFile');
                    if (!fileInput.files || fileInput.files.length === 0) {
                        showToast('Please upload your payment proof/POP', true);
                        return;
                    }

                    if (!document.getElementById('termsCheck').checked) {
                        showToast('Please agree to the terms and conditions', true);
                        return;
                    }

                    const submitBtn = document.getElementById('submitSubscription');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                    try {
                        const formData = new FormData(this);
                        formData.append('sessionId', sessionId);
                        
                        const response = await fetch(API_URL + 'submit-subscription.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            showToast('🎉 Subscription submitted successfully!');
                            setTimeout(() => {
                                bootstrap.Modal.getInstance(document.getElementById('subscriptionModal')).hide();
                            }, 2000);
                        } else {
                            showToast('❌ Error: ' + (result.error || 'Something went wrong'), true);
                        }
                    } catch (error) {
                        showToast('❌ Network error. Please try again.', true);
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Submit Subscription';
                    }
                });
            }

            loadSubscriptions();
            updateCartCount();
        });

        async function loadSubscriptions() {
            const container = document.getElementById('subscriptionsList');
            container.innerHTML = '<div class="col-12 text-center py-5"><i class="fas fa-spinner fa-spin fa-2x" style="color: var(--wine);"></i><p class="mt-3 text-muted">Loading plans...</p></div>';
            
            try {
                const response = await fetch(API_URL + 'get-subscriptions.php');
                const subs = await response.json();
                
                if (subs.error) {
                    container.innerHTML = `<div class="col-12 text-center text-danger">Error: ${subs.error}</div>`;
                    return;
                }
                
                if (!subs || !subs.length) {
                    container.innerHTML = '<div class="col-12 text-center">No subscription plans available yet.</div>';
                    return;
                }
                
                subs.sort((a, b) => a.price - b.price);
                
                container.innerHTML = subs.map(sub => `
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="subscription-card p-4 text-center ${sub.is_popular ? 'featured-plan' : ''}" data-plan-id="${sub.id}">
                            ${sub.is_popular ? '<div class="popular-badge">⭐ Most Popular</div>' : ''}
                            <h3 class="mb-2">${escapeHtml(sub.display_name)}</h3>
                            <small class="text-muted">${escapeHtml(sub.tagline || 'Curated. Crafted. Delivered.')}</small>
                            <div class="price-sub mt-3">E${parseFloat(sub.price).toFixed(2)}<span class="fs-6">/month</span></div>
                            <p class="mt-3 text-muted small">${escapeHtml(sub.description || 'Premium wine experience')}</p>
                            <p><i class="fas fa-wine-glass-alt"></i> ${sub.wines_per_month} wines per month</p>
                            ${sub.savings_percent > 0 ? `<p class="text-success"><i class="fas fa-tag"></i> Save ${sub.savings_percent}%</p>` : ''}
                            <div class="feature-list">
                                ${sub.features ? JSON.parse(sub.features).map(f => `<li><i class="fas fa-check-circle"></i> ${escapeHtml(f)}</li>`).join('') : `
                                    <li><i class="fas fa-check-circle"></i> ${sub.wines_per_month} premium wines</li>
                                    <li><i class="fas fa-check-circle"></i> Tasting notes included</li>
                                    <li><i class="fas fa-check-circle"></i> Free delivery</li>
                                    <li><i class="fas fa-check-circle"></i> Cancel anytime</li>
                                `}
                            </div>
                            <button class="btn btn-wine mt-4 w-100" onclick="subscribe('${escapeHtml(sub.display_name)}', ${sub.price})">
                                Subscribe Now
                            </button>
                        </div>
                    </div>
                `).join('');
            } catch(e) {
                container.innerHTML = `<div class="col-12 text-center text-danger">Error loading subscriptions: ${e.message}</div>`;
            }
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>