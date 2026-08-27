<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corporate Wine Gifts - Wine & Co. Eswatini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --wine: #722f37; --gold: #c9a03d; --green: #1a6b3c; }
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; }
        .navbar { background: white; border-bottom: 2px solid var(--gold); padding: 15px 0; }
        .navbar-brand { color: var(--wine) !important; font-size: 1.8rem; font-weight: 700; }
        .nav-link { color: #4a2c2a !important; font-weight: 500; margin: 0 10px; }
        .nav-link:hover { color: var(--wine) !important; }
        .btn-wine { background: var(--wine); color: white; border-radius: 40px; padding: 8px 28px; border: none; transition: 0.3s; }
        .btn-wine:hover { background: #5a232a; color: white; transform: translateY(-2px); }
        .btn-gold { background: var(--gold); color: #1a1a2e; border-radius: 40px; padding: 10px 30px; border: none; font-weight: bold; transition: 0.3s; }
        .btn-gold:hover { background: #b8922f; transform: translateY(-2px); }
        .gift-card { border: none; border-radius: 20px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); transition: 0.3s; background: white; height: 100%; overflow: hidden; }
        .gift-card:hover { transform: translateY(-5px); box-shadow: 0 16px 32px rgba(114,47,55,0.12); }
        .gift-header { 
            background: linear-gradient(135deg, var(--wine), var(--gold)); 
            color: white; 
            padding: 20px; 
            text-align: center;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .gift-header .tier-badge { 
            display: inline-block; 
            background: rgba(255,255,255,0.2); 
            padding: 2px 16px; 
            border-radius: 20px; 
            font-size: 0.8rem; 
            margin-top: 5px; 
        }
        .gift-image-container {
            width: 100%;
            height: 220px;
            overflow: hidden;
            background: #f8f4f0;
            position: relative;
        }
        .gift-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .gift-card:hover .gift-image-container img {
            transform: scale(1.05);
        }
        .gift-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e8ddd0, #d4c5b8);
            color: #999;
            font-size: 3rem;
        }
        .gift-body { padding: 20px; }
        .price { font-size: 1.8rem; font-weight: bold; color: var(--green); }
        .cart-icon { position: relative; color: var(--wine); text-decoration: none; font-size: 1.2rem; margin-left: 15px; }
        .cart-count { position: absolute; top: -12px; right: -18px; background: var(--gold); color: #333; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; }
        .feature-list { list-style: none; padding: 0; }
        .feature-list li { padding: 5px 0; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .feature-list li i { color: var(--gold); margin-right: 10px; }
        footer { background: #1a1a2e; color: #aaa; margin-top: 60px; padding: 50px 0; }
        .hero-section { background: linear-gradient(135deg, #1a0f0f, #3d2020, #5a3028); color: white; padding: 60px 0; margin-bottom: 50px; border-radius: 0 0 50px 50px; }
        .toast-msg { position: fixed; bottom: 20px; right: 20px; background: #1a6b3c; color: white; padding: 12px 24px; border-radius: 40px; z-index: 9999; display: none; box-shadow: 0 4px 12px rgba(0,0,0,0.2); font-weight: 500; }
        .toast-error { background: #dc3545; }
        .featured-badge { position: absolute; top: 15px; right: 15px; background: var(--gold); color: #1a1a2e; padding: 4px 14px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; z-index: 10; }
        .gift-card { position: relative; }
        .loading { text-align: center; padding: 50px; }
        .loading i { font-size: 2.5rem; color: var(--wine); }
        .gift-tier-icon {
            font-size: 2rem;
            margin-bottom: 5px;
        }
        .out-of-stock-badge {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: bold;
            z-index: 5;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="hero-section">
        <div class="container text-center">
            <h1 class="fw-bold"><i class="fas fa-gift me-3"></i>Corporate Wine Gifts</h1>
            <p class="lead">Luxury gift boxes for your valued clients & partners</p>
        </div>
    </div>

    <div class="container">
        <div class="row" id="giftsList"></div>
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
        
        console.log('Session ID:', sessionId);

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

        // ==================== ADD TO CART FUNCTION ====================
        function addToCart(id, name, price, type) {
            console.log('🛒 addToCart called with:', { id, name, price, type, sessionId });
            
            if (!id || !name || !price) {
                console.error('❌ Invalid parameters:', { id, name, price });
                showToast('Error: Missing product information', true);
                return;
            }
            
            showToast('Adding to cart...');
            
            fetch(API_URL + 'add-to-cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    productId: id,
                    productType: type || 'corporate',
                    productName: name,
                    price: price,
                    quantity: 1,
                    sessionId: sessionId
                })
            })
            .then(response => response.json())
            .then(result => {
                console.log('📦 Server response:', result);
                if (result.success) {
                    showToast(`✓ ${name} added to cart!`);
                    updateCartCount();
                } else {
                    showToast('Error: ' + (result.error || 'Could not add to cart'), true);
                }
            })
            .catch(error => {
                console.error('❌ Fetch error:', error);
                // Fallback to localStorage
                try {
                    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                    const existing = cart.find(item => item.id === id && item.type === type);
                    if (existing) { 
                        existing.quantity++; 
                    } else { 
                        cart.push({ id, name, price, quantity: 1, type: type || 'corporate' }); 
                    }
                    localStorage.setItem('cart', JSON.stringify(cart));
                    showToast(`✓ ${name} added to cart!`);
                    updateCartCount();
                } catch(err) {
                    showToast('Error adding to cart. Please try again.', true);
                }
            });
        }

        function escapeHtml(t) { 
            if (!t) return ''; 
            const d = document.createElement('div'); 
            d.textContent = t; 
            return d.innerHTML; 
        }

        // FIX: Safe string for JavaScript
        function safeJsString(str) {
            if (!str) return '';
            return str.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
        }

        function formatPrice(p) { 
            return 'E' + parseFloat(p).toFixed(2); 
        }

        // Get tier icon based on tier name
        function getTierIcon(tier) {
            if (!tier) return 'fa-gift';
            const t = tier.toLowerCase();
            if (t.includes('executive')) return 'fa-briefcase';
            if (t.includes('boardroom')) return 'fa-building';
            if (t.includes('chairman')) return 'fa-crown';
            if (t.includes('premium')) return 'fa-diamond';
            if (t.includes('gold')) return 'fa-star';
            return 'fa-gift';
        }

        // Get tier color
        function getTierColor(tier) {
            if (!tier) return 'var(--wine)';
            const t = tier.toLowerCase();
            if (t.includes('executive')) return '#2c3e50';
            if (t.includes('boardroom')) return '#8b4513';
            if (t.includes('chairman')) return '#c9a03d';
            if (t.includes('premium')) return '#1a6b3c';
            if (t.includes('gold')) return '#c9a03d';
            return 'var(--wine)';
        }

        async function loadGifts() {
            const container = document.getElementById('giftsList');
            container.innerHTML = `
                <div class="col-12 loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p class="mt-3 text-muted">Loading corporate gifts...</p>
                </div>
            `;
            
            try {
                const response = await fetch(API_URL + 'get-corporate-gifts.php');
                const gifts = await response.json();
                
                console.log('📦 Gifts loaded:', gifts);
                
                if (gifts.error) {
                    container.innerHTML = `<div class="col-12 text-center text-danger">Error: ${gifts.error}</div>`;
                    return;
                }
                
                if (!gifts || !gifts.length) {
                    container.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">No corporate gifts available at the moment.</p></div>';
                    return;
                }
                
                gifts.sort((a, b) => a.price - b.price);
                
                container.innerHTML = gifts.map((gift, index) => {
                    let features = [];
                    if (gift.features) {
                        if (typeof gift.features === 'string') {
                            features = gift.features.split(',').map(f => f.trim());
                        } else if (Array.isArray(gift.features)) {
                            features = gift.features;
                        }
                    }
                    if (!features.length) {
                        features = ['Premium wines', 'Gift wrapping', 'Personalised card'];
                    }
                    
                    const isFeatured = index === 0 || gift.is_popular;
                    const giftName = escapeHtml(gift.name);
                    const safeName = safeJsString(gift.name);
                    const imageUrl = gift.image_url || '';
                    const tierName = escapeHtml(gift.tier || 'Corporate Gift');
                    const tierIcon = getTierIcon(gift.tier);
                    const tierColor = getTierColor(gift.tier);
                    
                    console.log(`🎁 Gift: ${gift.name} (ID: ${gift.id}, Price: ${gift.price}, Image: ${imageUrl || 'No image'})`);
                    
                    // Determine if we have a valid image
                    const hasImage = imageUrl && imageUrl.trim() !== '';
                    
                    return `
                        <div class="col-md-4 mb-4">
                            <div class="gift-card">
                                ${isFeatured ? '<div class="featured-badge">⭐ Featured</div>' : ''}
                                
                                <!-- Image Section -->
                                <div class="gift-image-container">
                                    ${hasImage ? 
                                        `<img src="${imageUrl}" alt="${giftName}" onerror="this.parentElement.innerHTML='<div class=\\'gift-image-placeholder\\'><i class=\\'fas ${tierIcon}\\'></i></div>'">` :
                                        `<div class="gift-image-placeholder"><i class="fas ${tierIcon}"></i></div>`
                                    }
                                </div>
                                
                                <!-- Header Section -->
                                <div class="gift-header" style="background: linear-gradient(135deg, ${tierColor}, ${tierColor}dd);">
                                    <div class="gift-tier-icon"><i class="fas ${tierIcon}"></i></div>
                                    <h3 class="mb-0">${giftName}</h3>
                                    <span class="tier-badge">${tierName}</span>
                                </div>
                                
                                <div class="gift-body">
                                    <p>${escapeHtml(gift.description || 'A premium corporate gift selection')}</p>
                                    <ul class="feature-list">
                                        ${features.map(f => `<li><i class="fas fa-check-circle"></i> ${escapeHtml(f)}</li>`).join('')}
                                    </ul>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="price">${formatPrice(gift.price)}</div>
                                        <small class="text-muted">${gift.wines_included || 3} wines included</small>
                                    </div>
                                    <button class="btn btn-gold mt-3 w-100" onclick="addToCart(${gift.id}, '${safeName}', ${gift.price}, 'corporate')">
                                        <i class="fas fa-shopping-cart me-2"></i>Order Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            } catch(e) {
                console.error('❌ Error loading gifts:', e);
                container.innerHTML = `<div class="col-12 text-center text-danger">Error loading gifts: ${e.message}</div>`;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadGifts();
            updateCartCount();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>