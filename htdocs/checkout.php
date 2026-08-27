<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Wine & Co. Eswatini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --wine: #722f37; --gold: #c9a03d; --green: #1a6b3c; }
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; }
        .navbar { background: white; border-bottom: 2px solid var(--gold); padding: 15px 0; }
        .navbar-brand { color: var(--wine) !important; font-size: 1.8rem; font-weight: 700; }
        .nav-link { color: #4a2c2a !important; font-weight: 500; margin: 0 10px; }
        .nav-link:hover { color: var(--wine) !important; }
        .btn-wine { background: var(--wine); color: white; border-radius: 40px; border: none; padding: 12px 30px; width: 100%; font-weight: 600; transition: 0.3s; }
        .btn-wine:hover { background: #5a232a; transform: translateY(-2px); }
        .btn-wine:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .checkout-card { background: white; border-radius: 20px; padding: 30px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .payment-option { border: 2px solid #e0e0e0; border-radius: 12px; padding: 15px; margin-bottom: 10px; cursor: pointer; transition: 0.2s; }
        .payment-option.selected { border-color: var(--wine); background: #fef5f0; }
        .payment-option:hover { border-color: var(--wine); }
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            background: #fafafa;
        }
        .upload-area:hover { border-color: var(--wine); background: #fef5f0; }
        .upload-area.dragover { border-color: var(--wine); background: #fef5f0; }
        .upload-area i { font-size: 3rem; color: #ccc; margin-bottom: 15px; }
        .upload-area p { color: #888; margin: 0; }
        .upload-area .file-info { color: var(--green); font-weight: 600; }
        .uploaded-file {
            background: #f0f8f0;
            border-radius: 10px;
            padding: 12px 20px;
            display: none;
            align-items: center;
            justify-content: space-between;
            border-left: 4px solid var(--green);
        }
        .uploaded-file .remove-file { cursor: pointer; color: #dc3545; }
        .uploaded-file .remove-file:hover { text-decoration: underline; }
        footer { background: #1a1a2e; color: #aaa; margin-top: 60px; padding: 50px 0; }
        footer a { color: #aaa; text-decoration: none; }
        footer a:hover { color: var(--gold); }
        .pop-preview { max-width: 200px; max-height: 150px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .toast-msg { position: fixed; bottom: 20px; right: 20px; padding: 12px 24px; border-radius: 40px; z-index: 9999; display: none; box-shadow: 0 4px 12px rgba(0,0,0,0.2); font-weight: 500; max-width: 400px; }
        .toast-success { background: var(--green); color: white; }
        .toast-error { background: #dc3545; color: white; }
        .required { color: #dc3545; }
        .form-control { border-radius: 12px; padding: 12px 16px; border: 2px solid #e8e0d8; }
        .form-control:focus { border-color: var(--wine); box-shadow: 0 0 0 3px rgba(114,47,55,0.1); }
        .form-label { font-weight: 600; color: #2c1a1a; }
        .order-item { border-bottom: 1px solid #eee; padding: 10px 0; }
        .order-item:last-child { border-bottom: none; }
        .cart-icon { position: relative; color: var(--wine); text-decoration: none; font-size: 1.2rem; }
        .cart-count { position: absolute; top: -12px; right: -18px; background: var(--gold); color: #333; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; }
        .processing-spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .order-number-display { font-size: 1.1rem; color: var(--wine); font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4"><i class="fas fa-credit-card me-2" style="color: var(--wine);"></i>Checkout</h2>
        <div class="row">
            <div class="col-md-7">
                <div class="checkout-card">
                    <h4><i class="fas fa-truck me-2" style="color: var(--wine);"></i>Delivery Information</h4>
                    <hr>
                    <form id="checkoutForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="required">*</span></label>
                            <input type="text" id="fullName" class="form-control" required placeholder="John Doe">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="required">*</span></label>
                            <input type="email" id="email" class="form-control" required placeholder="john@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="phone" class="form-control" required placeholder="+268 1234 5678">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Delivery Address <span class="required">*</span></label>
                            <textarea id="address" class="form-control" rows="3" required placeholder="Street, City, Eswatini"></textarea>
                        </div>

                        <h4 class="mt-4"><i class="fas fa-money-bill-wave me-2" style="color: var(--gold);"></i>Payment Method</h4>
                        <div class="payment-option selected" onclick="selectPayment('cash')" id="paymentCash">
                            <i class="fas fa-money-bill-wave me-2"></i> Cash on Delivery
                        </div>
                        <div class="payment-option" onclick="selectPayment('bank')" id="paymentBank">
                            <i class="fas fa-university me-2"></i> Bank Transfer (Eswatini Bank)
                        </div>
                        <div class="payment-option" onclick="selectPayment('mobile')" id="paymentMobile">
                            <i class="fas fa-mobile-alt me-2"></i> Mobile Money (MTN MoMo)
                        </div>
                        <input type="hidden" id="paymentMethod" value="cash">

                        <!-- POP Upload Section -->
                        <div id="popSection" style="display: none;" class="mt-4">
                            <h4><i class="fas fa-upload me-2"></i>Upload Payment Proof (POP)</h4>
                            <p class="text-muted small">Please upload a screenshot, PDF, or photo of your deposit slip</p>

                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p><strong>Click to upload</strong> or drag and drop</p>
                                <p class="text-muted small">Acceptable formats: JPG, PNG, PDF (Max 5MB)</p>
                                <input type="file" id="popFile" accept=".jpg,.jpeg,.png,.pdf,.gif,.bmp" style="display: none;">
                                <div id="fileInfo" class="file-info mt-2"></div>
                            </div>

                            <div id="uploadedFile" class="uploaded-file mt-3">
                                <div>
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <span id="fileName">document.pdf</span>
                                    <br><small class="text-muted" id="fileSize">2.4 MB</small>
                                </div>
                                <div>
                                    <span class="badge bg-success me-2">Uploaded</span>
                                    <span class="remove-file" onclick="removeFile()"><i class="fas fa-times"></i> Remove</span>
                                </div>
                            </div>

                            <div id="previewContainer" class="mt-2"></div>
                        </div>

                        <button type="submit" class="btn btn-wine mt-4" id="placeOrderBtn">
                            <i class="fas fa-check-circle me-2"></i>Place Order
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-md-5">
                <div class="checkout-card">
                    <h4><i class="fas fa-receipt me-2" style="color: var(--gold);"></i>Order Summary</h4>
                    <hr>
                    <div id="orderSummary"></div>
                    <div id="bankDetails" style="display: none; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                        <h6>Bank Transfer Details</h6>
                        <p class="small">
                            <strong>Bank:</strong> Eswatini Bank<br>
                            <strong>Account Name:</strong> Wine & Co. Eswatini<br>
                            <strong>Account Number:</strong> 1234 5678 9012<br>
                            <strong>Branch:</strong> Mbabane<br>
                            <strong>Reference:</strong> Your Order Number
                        </p>
                    </div>
                    <div id="mobileDetails" style="display: none; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                        <h6>Mobile Money Details</h6>
                        <p class="small">
                            <strong>Mobile Money Number:</strong> +268 1234 5678<br>
                            <strong>Network:</strong> MTN MoMo / Eswatini Mobile<br>
                            <strong>Reference:</strong> Your Order Number
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <div id="toastMsg" class="toast-msg"></div>

    <script>
        // ============================================================
        //  COMPLETE JAVASCRIPT FOR CHECKOUT
        // ============================================================

        const API_URL = 'backend/';
        let sessionId = localStorage.getItem('cartSessionId');
        if (!sessionId) {
            sessionId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('cartSessionId', sessionId);
        }

        let cartItems = [];
        let orderTotal = 0;
        let currentFile = null;

        // ============================================================
        //  TOAST NOTIFICATION
        // ============================================================
        function showToast(message, isError = false) {
            const toast = document.getElementById('toastMsg');
            toast.innerText = message;
            toast.className = 'toast-msg ' + (isError ? 'toast-error' : 'toast-success');
            toast.style.display = 'block';
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => { toast.style.display = 'none'; }, 4000);
        }

        // ============================================================
        //  PAYMENT SELECTION
        // ============================================================
        function selectPayment(method) {
            document.getElementById('paymentMethod').value = method;
            document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
            document.getElementById(`payment${method.charAt(0).toUpperCase() + method.slice(1)}`).classList.add('selected');
            
            const popSection = document.getElementById('popSection');
            const bankDetails = document.getElementById('bankDetails');
            const mobileDetails = document.getElementById('mobileDetails');
            
            if (method === 'cash') {
                popSection.style.display = 'none';
                bankDetails.style.display = 'none';
                mobileDetails.style.display = 'none';
            } else {
                popSection.style.display = 'block';
                bankDetails.style.display = method === 'bank' ? 'block' : 'none';
                mobileDetails.style.display = method === 'mobile' ? 'block' : 'none';
            }
        }

        // ============================================================
        //  FILE UPLOAD HANDLING
        // ============================================================
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('popFile');
        const uploadedFileDiv = document.getElementById('uploadedFile');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const fileInfo = document.getElementById('fileInfo');
        const previewContainer = document.getElementById('previewContainer');

        if (uploadArea) {
            uploadArea.addEventListener('click', () => fileInput.click());

            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFile(e.target.files[0]);
                }
            });

            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                if (e.dataTransfer.files.length > 0) {
                    handleFile(e.dataTransfer.files[0]);
                }
            });
        }

        function handleFile(file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                showToast('Please upload JPG, PNG, GIF, BMP, or PDF files only.', true);
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showToast('File size must be less than 5MB.', true);
                return;
            }

            currentFile = file;
            fileName.innerText = file.name;
            fileSize.innerText = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            uploadedFileDiv.style.display = 'flex';
            fileInfo.innerHTML = `<i class="fas fa-check-circle text-success"></i> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;

            previewContainer.innerHTML = '';
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewContainer.innerHTML = `
                        <div class="mt-2">
                            <small class="text-muted">Preview:</small><br>
                            <img src="${e.target.result}" class="pop-preview mt-1">
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.innerHTML = `
                    <div class="mt-2">
                        <span class="badge bg-info">PDF Document</span>
                        <p class="small text-muted mt-1">PDF uploaded successfully</p>
                    </div>
                `;
            }
        }

        function removeFile() {
            currentFile = null;
            uploadedFileDiv.style.display = 'none';
            fileInfo.innerHTML = '';
            previewContainer.innerHTML = '';
            fileInput.value = '';
        }

        // ============================================================
        //  LOAD ORDER SUMMARY FROM CART
        // ============================================================
        async function loadOrderSummary() {
            try {
                const res = await fetch(API_URL + 'get-cart.php?sessionId=' + sessionId);
                const data = await res.json();
                cartItems = data.items || [];
                
                if (!cartItems.length) {
                    window.location.href = 'cart.php';
                    return;
                }
                
                let subtotal = 0;
                let html = '';
                cartItems.forEach(item => {
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
                orderTotal = total;
                
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
                            <strong style="color: var(--wine); font-size: 1.2rem;">E${total.toFixed(2)}</strong>
                        </div>
                    </div>
                `;
                
                document.getElementById('orderSummary').innerHTML = html;
            } catch(e) {
                showToast('Error loading cart: ' + e.message, true);
            }
        }

        // ============================================================
        //  HELPER FUNCTIONS
        // ============================================================
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ============================================================
        //  SUBMIT ORDER
        // ============================================================
        document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = document.getElementById('placeOrderBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            
            const paymentMethod = document.getElementById('paymentMethod').value;
            const fullName = document.getElementById('fullName').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('address').value.trim();
            
            // Validate required fields
            if (!fullName || !email || !phone || !address) {
                showToast('Please fill in all required fields', true);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Place Order';
                return;
            }
            
            if (!email.includes('@')) {
                showToast('Please enter a valid email address', true);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Place Order';
                return;
            }
            
            if (paymentMethod !== 'cash' && !currentFile) {
                showToast('Please upload proof of payment (POP)', true);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Place Order';
                return;
            }
            
            try {
                // First, upload the POP file if it exists
                let popFileName = null;
                let popFilePath = null;
                
                if (currentFile) {
                    const formData = new FormData();
                    formData.append('pop_file', currentFile);
                    formData.append('sessionId', sessionId);
                    
                    const uploadRes = await fetch(API_URL + 'upload-pop.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const uploadResult = await uploadRes.json();
                    console.log('Upload result:', uploadResult);
                    
                    if (uploadResult.success) {
                        popFileName = uploadResult.fileName;
                        popFilePath = uploadResult.filePath;
                    } else {
                        showToast('Error uploading POP: ' + (uploadResult.error || 'Unknown error'), true);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Place Order';
                        return;
                    }
                }
                
                // Now place the order
                const orderData = {
                    sessionId: sessionId,
                    customerName: fullName,
                    customerEmail: email,
                    customerPhone: phone,
                    customerAddress: address,
                    paymentMethod: paymentMethod,
                    items: cartItems,
                    total: orderTotal,
                    popFileName: popFileName,
                    popFilePath: popFilePath
                };
                
                console.log('Submitting order:', orderData);
                
                const response = await fetch(API_URL + 'place-order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orderData)
                });
                
                const result = await response.json();
                console.log('Order response:', result);
                
                if (result.success) {
                    // Clear the cart
                    await fetch(API_URL + 'clear-cart.php?sessionId=' + sessionId, { method: 'POST' });
                    localStorage.removeItem('cart');
                    
                    showToast(`✅ Order placed successfully! Order #${result.orderNumber}`);
                    setTimeout(() => {
                        window.location.href = 'order-confirmation.php?order_id=' + result.orderId;
                    }, 2000);
                } else {
                    showToast('❌ Error: ' + (result.error || 'Could not place order'), true);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Place Order';
                }
            } catch(error) {
                console.error('Error placing order:', error);
                showToast('❌ Network error. Please try again.', true);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Place Order';
            }
        });

        // ============================================================
        //  INITIALIZE
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            loadOrderSummary();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>