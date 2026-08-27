<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Wine & Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --wine: #722f37;
            --gold: #c9a03d;
            --cream: #f5ede6;
            --green: #1a6b3c;
        }
        
        body { 
            background: var(--cream); 
            font-family: 'Segoe UI', system-ui; 
        }
        
        .navbar { 
            background: white; 
            border-bottom: 2px solid var(--gold); 
            padding: 15px 0; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .navbar-brand { 
            color: var(--wine) !important; 
            font-size: 1.8rem; 
            font-weight: 700; 
        }
        .navbar-brand i {
            color: var(--wine);
        }
        
        .btn-wine { 
            background: var(--wine); 
            color: white; 
            border-radius: 40px; 
            border: none; 
            padding: 10px 30px;
            transition: 0.3s;
        }
        .btn-wine:hover { 
            background: #5a232a; 
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-outline-wine {
            border: 2px solid var(--wine);
            color: var(--wine);
            border-radius: 40px;
            padding: 8px 24px;
            background: transparent;
            transition: 0.3s;
        }
        .btn-outline-wine:hover {
            background: var(--wine);
            color: white;
        }
        
        .cart-item { 
            background: white; 
            border-radius: 15px; 
            padding: 20px; 
            margin-bottom: 15px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: 0.3s;
            border-left: 4px solid var(--gold);
        }
        .cart-item:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        
        .cart-item-image { 
            width: 80px; 
            height: 80px; 
            object-fit: cover; 
            border-radius: 10px;
            border: 1px solid #eee;
        }
        
        .cart-item-title {
            font-weight: 600;
            color: #2c1a1a;
        }
        
        .quantity-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #ddd;
            background: white;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .quantity-btn:hover {
            background: var(--wine);
            color: white;
            border-color: var(--wine);
        }
        
        .remove-btn {
            color: #dc3545;
            background: transparent;
            border: none;
            transition: 0.3s;
            padding: 8px 12px;
            border-radius: 8px;
        }
        .remove-btn:hover {
            background: #dc3545;
            color: white;
        }
        
        .cart-summary {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            position: sticky;
            top: 20px;
        }
        
        .cart-summary h3 {
            color: var(--wine);
            font-weight: 700;
        }
        
        .cart-summary .total-amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--green);
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px 0;
        }
        .empty-cart i {
            font-size: 4rem;
            color: #ddd;
        }
        
        footer { 
            background: #1a1a2e; 
            color: #aaa; 
            margin-top: 60px; 
            padding: 50px 0; 
        }
        footer a {
            color: #aaa;
            text-decoration: none;
        }
        footer a:hover {
            color: var(--gold);
        }
        
        .toast-msg {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--green);
            color: white;
            padding: 14px 28px;
            border-radius: 40px;
            z-index: 9999;
            display: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            font-weight: 500;
            animation: slideUp 0.4s ease-out;
        }
        .toast-error {
            background: #dc3545;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .cart-item-image { width: 60px; height: 60px; }
            .cart-summary { margin-top: 20px; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-wine-bottle me-2"></i>Wine & Co.
            </a>
            <div>
                <a href="shop.php" class="btn btn-outline-wine me-2">
                    <i class="fas fa-arrow-left me-1"></i>Continue Shopping
                </a>
                <span class="badge bg-warning text-dark" id="cartCount">
                    <i class="fas fa-shopping-cart me-1"></i><span id="cartCountNum">0</span>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <h2 class="mb-4">
                    <i class="fas fa-shopping-cart me-2" style="color: var(--wine);"></i>
                    Your Shopping Cart
                </h2>
                <div id="cartItems"></div>
            </div>
            <div class="col-lg-4">
                <div class="cart-summary" id="cartSummary">
                    <h4><i class="fas fa-receipt me-2"></i>Order Summary</h4>
                    <hr>
                    <div id="cartTotal">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span id="subtotal">E0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <span>Delivery:</span>
                            <span id="delivery">E0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="total-amount" id="grandTotal">E0.00</strong>
                        </div>
                    </div>
                    <a href="checkout.php" class="btn btn-wine w-100 mt-3">
                        <i class="fas fa-lock me-2"></i>Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container text-center">
            <p>© 2025 Wine & Co. — All prices in Swaziland Lilangeni (E)</p>
            <p class="small">
                <a href="index.php">Home</a> • 
                <a href="shop.php">Shop</a> • 
                <a href="subscription.php">Subscriptions</a>
            </p>
        </div>
    </footer>

    <div id="toastMsg" class="toast-msg"></div>

    <script>
        const API_URL = 'backend/';
        let cart = [];
        
        // ========== FIX: Use SAME session ID as shop.php and index.php ==========
        let sessionId = localStorage.getItem('cartSessionId');
        if (!sessionId) {
            sessionId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('cartSessionId', sessionId);
        }
        console.log('Cart Page Session ID:', sessionId);

        function showToast(message, isError = false) {
            const toast = document.getElementById('toastMsg');
            toast.innerText = message;
            toast.className = 'toast-msg' + (isError ? ' toast-error' : '');
            toast.style.display = 'block';
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => { toast.style.display = 'none'; }, 3000);
        }

        function getImageUrl(path) {
            if (!path) return 'images/placeholder.jpg';
            if (path.startsWith('http')) return path;
            if (path.startsWith('/uploads/')) return path;
            if (path.startsWith('uploads/')) return '/' + path;
            return '/uploads/wines/' + path;
        }

        async function loadCart() {
            try {
                console.log('Loading cart with sessionId:', sessionId);
                const res = await fetch(API_URL + 'get-cart.php?sessionId=' + sessionId);
                const data = await res.json();
                console.log('Cart data received:', data);
                cart = data.items || [];
                renderCart();
                updateCartCount();
            } catch(e) { 
                console.error('Error loading cart:', e);
                document.getElementById('cartItems').innerHTML = '<div class="alert alert-danger">Error loading cart</div>';
            }
        }

        async function updateCartCount() {
            try {
                const res = await fetch(API_URL + 'get-cart-count.php?sessionId=' + sessionId);
                const data = await res.json();
                const count = data.count || 0;
                document.getElementById('cartCountNum').innerText = count;
                document.querySelectorAll('.cart-count').forEach(el => el.innerText = count);
            } catch(e) {
                console.error('Error updating cart count:', e);
            }
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            const subtotalEl = document.getElementById('subtotal');
            const deliveryEl = document.getElementById('delivery');
            const grandTotalEl = document.getElementById('grandTotal');
            
            if (!cart || cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-basket"></i>
                        <h4 class="mt-3">Your cart is empty</h4>
                        <p class="text-muted">Looks like you haven't added any wines yet.</p>
                        <a href="shop.php" class="btn btn-wine mt-2">
                            <i class="fas fa-wine-glass me-2"></i>Start Shopping
                        </a>
                    </div>
                `;
                subtotalEl.innerText = 'E0.00';
                deliveryEl.innerText = 'E0.00';
                grandTotalEl.innerText = 'E0.00';
                return;
            }
            
            let html = '';
            let subtotal = 0;
            
            cart.forEach((item) => {
                let itemTotal = parseFloat(item.price) * parseInt(item.quantity);
                subtotal += itemTotal;
                
                let imagePath = item.image_url || '';
                
                html += `
                    <div class="cart-item" id="cart-item-${item.id}">
                        <div class="row align-items-center">
                            <div class="col-md-1">
                                <img src="${getImageUrl(imagePath)}" 
                                     class="cart-item-image" 
                                     alt="${escapeHtml(item.product_name)}"
                                     onerror="this.onerror=null;this.src='images/placeholder.jpg'">
                            </div>
                            <div class="col-md-4">
                                <div class="cart-item-title">${escapeHtml(item.product_name)}</div>
                                <small class="text-muted">${escapeHtml(item.product_type || 'Wine')}</small>
                            </div>
                            <div class="col-md-2">E${parseFloat(item.price).toFixed(2)}</div>
                            <div class="col-md-2">
                                <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${parseInt(item.quantity) - 1})">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="mx-2" style="min-width: 20px; display: inline-block; text-align: center;">
                                    ${item.quantity}
                                </span>
                                <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${parseInt(item.quantity) + 1})">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="col-md-2">E${itemTotal.toFixed(2)}</div>
                            <div class="col-md-1 text-center">
                                <button class="remove-btn" onclick="removeItem(${item.id})" title="Remove item">
                                    <i class="fas fa-trash fa-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            
            const delivery = subtotal > 0 ? (subtotal >= 500 ? 0 : 50) : 0;
            const total = subtotal + delivery;
            
            subtotalEl.innerText = `E${subtotal.toFixed(2)}`;
            deliveryEl.innerText = delivery > 0 ? `E${delivery.toFixed(2)}` : 'FREE';
            grandTotalEl.innerText = `E${total.toFixed(2)}`;
        }

        async function updateQuantity(id, newQty) {
            if (newQty <= 0) { 
                removeItem(id); 
                return; 
            }
            
            try {
                const response = await fetch(API_URL + 'update-cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        cartId: id, 
                        quantity: newQty,
                        sessionId: sessionId 
                    })
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Quantity updated');
                    loadCart();
                } else {
                    showToast('Error updating quantity', true);
                }
            } catch(e) {
                showToast('Error updating quantity', true);
            }
        }

        async function removeItem(id) {
            if (!confirm('Remove this item from your cart?')) return;
            
            try {
                const response = await fetch(API_URL + 'remove-from-cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        cartId: id,
                        sessionId: sessionId 
                    })
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Item removed from cart');
                    loadCart();
                } else {
                    showToast('Error removing item', true);
                }
            } catch(e) {
                showToast('Error removing item', true);
            }
        }

        function escapeHtml(text) { 
            if (!text) return ''; 
            const div = document.createElement('div'); 
            div.textContent = text; 
            return div.innerHTML; 
        }
        
        // Load cart on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>