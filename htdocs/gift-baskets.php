<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wine Gift Baskets - Wine & Co. Eswatini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --wine: #722f37; --gold: #c9a03d; --green: #1a6b3c; }
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; }
        .navbar { background: white; border-bottom: 2px solid var(--gold); padding: 15px 0; }
        .navbar-brand { color: var(--wine) !important; font-size: 1.8rem; font-weight: 700; }
        .nav-link { color: #4a2c2a !important; font-weight: 500; margin: 0 10px; }
        .nav-link:hover { color: var(--wine) !important; }
        .btn-wine { background: var(--wine); color: white; border-radius: 40px; padding: 8px 28px; border: none; }
        .btn-wine:hover { background: #5a232a; color: white; transform: translateY(-2px); }
        .btn-gold { background: var(--gold); color: #1a1a2e; border-radius: 40px; padding: 10px 30px; border: none; font-weight: bold; transition: 0.3s; }
        .btn-gold:hover { background: #b8922f; transform: translateY(-2px); }
        
        .basket-card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 8px 24px rgba(0,0,0,0.08); 
            transition: 0.3s; 
            background: white; 
            height: 100%; 
            overflow: hidden; 
        }
        .basket-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 16px 32px rgba(114,47,55,0.12); 
        }
        
        .basket-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #f0ece8;
        }
        
        .basket-header { 
            background: linear-gradient(135deg, var(--gold), var(--wine)); 
            color: white; 
            padding: 20px; 
            text-align: center; 
        }
        .basket-body { padding: 20px; }
        .price { font-size: 1.8rem; font-weight: bold; color: var(--green); }
        
        .cart-icon { 
            position: relative; 
            color: var(--wine); 
            text-decoration: none; 
            font-size: 1.2rem; 
            margin-left: 15px; 
        }
        .cart-count { 
            position: absolute; 
            top: -12px; 
            right: -18px; 
            background: var(--gold); 
            color: #333; 
            border-radius: 50%; 
            padding: 2px 6px; 
            font-size: 11px; 
            font-weight: bold; 
        }
        
        .feature-list { list-style: none; padding: 0; }
        .feature-list li { padding: 5px 0; border-bottom: 1px solid #eee; }
        .feature-list li i { color: var(--gold); margin-right: 10px; }
        .feature-list li:last-child { border-bottom: none; }
        
        footer { background: #1a1a2e; color: #aaa; margin-top: 60px; padding: 50px 0; }
        footer a { color: #aaa; text-decoration: none; }
        footer a:hover { color: var(--gold); }
        
        .hero-section { 
            background: linear-gradient(135deg, #1a0f0f, #3d2020, #5a3028); 
            color: white; 
            padding: 60px 0; 
            margin-bottom: 50px; 
            border-radius: 0 0 50px 50px; 
        }
        
        .toast-msg { 
            position: fixed; 
            bottom: 20px; 
            right: 20px; 
            background: #1a6b3c; 
            color: white; 
            padding: 12px 24px; 
            border-radius: 40px; 
            z-index: 9999; 
            display: none; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.2); 
            font-weight: 500; 
        }
        .toast-error { background: #dc3545; }
        
        .loading { text-align: center; padding: 50px; }
        .loading i { font-size: 2.5rem; color: var(--wine); }
        
        .placeholder-img {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #f5ede6, #e8ddd4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #aaa;
            font-size: 3rem;
        }
        
        @media (max-width: 768px) {
            .basket-image { height: 180px; }
            .placeholder-img { height: 180px; }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold"><i class="fas fa-basket-shopping me-3"></i>Wine Gift Baskets</h1>
            <p class="lead">The perfect gift for every occasion</p>
        </div>
    </div>

    <div class="container">
        <div class="row" id="basketsList"></div>
    </div>

    <?php include 'footer.php'; ?>

    <div id="toastMsg" class="toast-msg"></div>

    <script>
        const API_URL = 'backend/';
        
        // Use consistent session ID
        let sessionId = localStorage.getItem('cartSessionId');
        if (!sessionId) {
            sessionId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('cartSessionId', sessionId);
        }

        function showToast(message, isError = false) {
            const toast = document.getElementById('toastMsg');
            toast.innerText = message;
            toast.className = 'toast-msg' + (isError ? ' toast-error' : '');
            toast.style.display = 'block';
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => { toast.style.display = 'none'; }, 3000);
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

        async function addToCart(id, name, price, type) {
            try {
                const response = await fetch(API_URL + 'add-to-cart.php', {
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
                });
                const result = await response.json();
                if (result.success) {
                    showToast(`✓ ${name} added to cart!`);
                    updateCartCount();
                } else {
                    showToast('Error: ' + (result.error || 'Unknown error'), true);
                }
            } catch(e) {
                let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                const existing = cart.find(item => item.id === id && item.type === type);
                if (existing) { 
                    existing.quantity++; 
                } else { 
                    cart.push({ id, name, price, quantity: 1, type }); 
                }
                localStorage.setItem('cart', JSON.stringify(cart));
                showToast(`✓ ${name} added to cart!`);
                updateCartCount();
            }
        }

        function escapeHtml(t) { 
            if (!t) return ''; 
            const d = document.createElement('div'); 
            d.textContent = t; 
            return d.innerHTML; 
        }

        function formatPrice(p) { 
            return 'E' + parseFloat(p).toFixed(2); 
        }

        function getImageUrl(path) {
            if (!path) return '';
            if (path.startsWith('http')) return path;
            if (path.startsWith('/')) return path;
            return '/' + path;
        }

        async function loadBaskets() {
            const container = document.getElementById('basketsList');
            container.innerHTML = `
                <div class="col-12 loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p class="mt-3 text-muted">Loading gift baskets...</p>
                </div>
            `;
            
            try {
                const response = await fetch(API_URL + 'get-gift-baskets.php');
                const baskets = await response.json();
                
                if (baskets.error) {
                    container.innerHTML = `<div class="col-12 text-center text-danger">Error: ${baskets.error}</div>`;
                    return;
                }
                
                if (!baskets || !baskets.length) {
                    container.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">No gift baskets available at the moment.</p></div>';
                    return;
                }
                
                container.innerHTML = baskets.map(basket => {
                    let features = [];
                    if (basket.features) {
                        if (typeof basket.features === 'string') {
                            features = basket.features.split(',').map(f => f.trim());
                        } else if (Array.isArray(basket.features)) {
                            features = basket.features;
                        }
                    }
                    if (!features.length) {
                        features = ['Premium wines', 'Gift wrapping', 'Personalised card'];
                    }
                    
                    const imageUrl = getImageUrl(basket.image_url);
                    const hasImage = imageUrl && imageUrl !== '';
                    
                    return `
                        <div class="col-md-4 mb-4">
                            <div class="basket-card">
                                ${hasImage ? `
                                    <img src="${imageUrl}" class="basket-image" alt="${escapeHtml(basket.name)}" onerror="this.src='images/placeholder.jpg'">
                                ` : `
                                    <div class="basket-header">
                                        <h3 class="mb-0">${escapeHtml(basket.name)}</h3>
                                        <small class="opacity-75">${escapeHtml(basket.tier || 'Gift Basket')}</small>
                                    </div>
                                `}
                                <div class="basket-body">
                                    <h5 class="mb-2">${escapeHtml(basket.name)}</h5>
                                    <p class="text-muted small">${escapeHtml(basket.description || 'A beautifully curated gift basket')}</p>
                                    <ul class="feature-list">
                                        ${features.map(f => `<li><i class="fas fa-check-circle"></i> ${escapeHtml(f)}</li>`).join('')}
                                    </ul>
                                    <div class="price">${formatPrice(basket.price)}</div>
                                    <button class="btn btn-gold mt-3 w-100" onclick="addToCart(${basket.id}, '${escapeHtml(basket.name)}', ${basket.price}, 'basket')">
                                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            } catch(e) {
                console.error('Error loading baskets:', e);
                container.innerHTML = `<div class="col-12 text-center text-danger">Error loading baskets: ${e.message}</div>`;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadBaskets();
            updateCartCount();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>