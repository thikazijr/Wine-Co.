<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfect Pairings - Wine & Co. Eswatini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --wine: #722f37; --gold: #c9a03d; --green: #1a6b3c; }
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; }
        .navbar { background: white; border-bottom: 2px solid var(--gold); padding: 15px 0; }
        .navbar-brand { color: var(--wine) !important; font-size: 1.8rem; font-weight: 700; }
        .nav-link { color: #4a2c2a !important; font-weight: 500; margin: 0 10px; }
        .btn-wine { background: var(--wine); color: white; border-radius: 40px; padding: 8px 28px; border: none; }
        .btn-wine:hover { background: #5a232a; color: white; }
        .btn-outline-wine { border: 2px solid var(--wine); color: var(--wine); border-radius: 40px; padding: 6px 24px; background: transparent; }
        .btn-outline-wine:hover { background: var(--wine); color: white; }
        .pairing-card { border: none; border-radius: 20px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); transition: 0.3s; background: white; height: 100%; overflow: hidden; }
        .pairing-card:hover { transform: translateY(-5px); box-shadow: 0 16px 32px rgba(114,47,55,0.12); }
        .pairing-image { width: 100%; height: 200px; object-fit: cover; background: #f8f4f0; }
        .pairing-body { padding: 20px; text-align: center; }
        .price { font-size: 1.5rem; font-weight: bold; color: var(--green); }
        .cart-icon { position: relative; color: var(--wine); text-decoration: none; margin-left: 15px; }
        .cart-count { position: absolute; top: -12px; right: -18px; background: var(--gold); color: #333; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; }
        footer { background: #1a1a2e; color: #aaa; margin-top: 60px; padding: 50px 0; }
        .loading { text-align: center; padding: 50px; }
        .toast-msg { position: fixed; bottom: 20px; right: 20px; background: var(--green); color: white; padding: 12px 24px; border-radius: 40px; z-index: 9999; display: none; box-shadow: 0 4px 12px rgba(0,0,0,0.2); font-weight: 500; }
        .toast-error { background: #dc3545; }
        .image-placeholder { width: 100%; height: 200px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #e8ddd0, #d4c5b8); color: #999; font-size: 3rem; }
        .compatible-badge { display: inline-block; background: #f5ede6; color: var(--wine); padding: 2px 12px; border-radius: 20px; font-size: 0.75rem; margin: 2px; }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2><i class="fas fa-utensils me-2" style="color:var(--wine);"></i>Perfect Pairings</h2>
                <p class="text-muted">Expertly curated to complement your favorite wines</p>
            </div>
        </div>
        <div id="pairingsList" class="row"></div>
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

        function addToCart(id, name, price, type) {
            if (!id || !name || !price) {
                showToast('Error: Missing product information', true);
                return;
            }
            
            showToast('Adding to cart...');
            
            fetch(API_URL + 'add-to-cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    productId: id,
                    productType: type || 'pairing',
                    productName: name,
                    price: price,
                    quantity: 1,
                    sessionId: sessionId
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showToast(`✓ ${name} added to cart!`);
                    updateCartCount();
                } else {
                    showToast('Error: ' + (result.error || 'Could not add to cart'), true);
                }
            })
            .catch(error => {
                // Fallback to localStorage
                try {
                    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                    const existing = cart.find(item => item.id === id && item.type === type);
                    if (existing) { 
                        existing.quantity++; 
                    } else { 
                        cart.push({ id, name, price, quantity: 1, type: type || 'pairing' }); 
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

        function safeJsString(str) {
            if (!str) return '';
            return str.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
        }

        function loadPairings() {
            const container = document.getElementById('pairingsList');
            container.innerHTML = `
                <div class="col-12 loading">
                    <i class="fas fa-spinner fa-spin fa-2x" style="color:var(--wine);"></i>
                    <p class="mt-3 text-muted">Loading pairings...</p>
                </div>
            `;
            
            fetch(API_URL + 'get-pairings.php')
                .then(response => response.json())
                .then(pairings => {
                    if (pairings.error) {
                        container.innerHTML = `<div class="col-12 text-center text-danger">Error: ${pairings.error}</div>`;
                        return;
                    }
                    
                    if (!pairings || !pairings.length) {
                        container.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">No pairings available yet.</p></div>';
                        return;
                    }
                    
                    container.innerHTML = pairings.map(p => {
                        const imageUrl = p.image_url || '';
                        const hasImage = imageUrl && imageUrl.trim() !== '';
                        const price = parseFloat(p.price).toFixed(2);
                        const safeName = safeJsString(p.name);
                        
                        return `
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="pairing-card">
                                    ${hasImage ? 
                                        `<img src="${imageUrl}" class="pairing-image" alt="${escapeHtml(p.name)}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">` :
                                        ''
                                    }
                                    <div class="image-placeholder" ${hasImage ? 'style="display:none;"' : ''}>
                                        <i class="fas fa-cheese"></i>
                                    </div>
                                    <div class="pairing-body">
                                        <h5 class="mb-2">${escapeHtml(p.name)}</h5>
                                        <p class="small text-muted">${escapeHtml(p.description)}</p>
                                        <div class="mb-2">
                                            <span class="compatible-badge"><i class="fas fa-wine-glass-alt me-1"></i>${escapeHtml(p.compatible_wines || 'Any Wine')}</span>
                                        </div>
                                        <div class="price">E${price}</div>
                                        <button class="btn btn-outline-wine btn-sm mt-3 w-100" onclick="addToCart(${p.id}, '${safeName}', ${p.price}, 'pairing')">
                                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                })
                .catch(error => {
                    container.innerHTML = `<div class="col-12 text-center text-danger">Error loading pairings: ${error.message}</div>`;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadPairings();
            updateCartCount();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>