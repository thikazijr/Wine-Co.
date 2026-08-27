<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Wines - Wine & Co. Eswatini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --wine: #722f37;
            --wine-light: #9e4a55;
            --gold: #c9a03d;
            --green: #1a6b3c;
            --dark: #1a1a2e;
            --cream: #f8f5f0;
            --text: #2c1a1a;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: var(--cream);
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }
        
        /* ==================== NAVIGATION ==================== */
        .navbar {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px);
            border-bottom: 2px solid var(--gold);
            padding: 12px 0;
        }
        .navbar-brand {
            color: var(--wine) !important;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            height: 40px;
            background: var(--wine);
            padding: 5px 12px;
            border-radius: 10px;
            margin-right: 10px;
        }
        .nav-link { color: #4a2c2a !important; font-weight: 500; margin: 0 10px; }
        .nav-link:hover { color: var(--wine) !important; }
        
        .cart-icon {
            position: relative;
            color: var(--wine);
            text-decoration: none;
            font-size: 1.2rem;
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
            min-width: 18px;
            text-align: center;
        }
        
        /* ==================== PAGE HEADER ==================== */
        .shop-header {
            background: linear-gradient(135deg, #1a0f0f 0%, #3d2020 50%, #5a3028 100%);
            color: white;
            padding: 60px 0 50px 0;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        .shop-header::after {
            content: '🍷';
            position: absolute;
            right: 50px;
            bottom: 20px;
            font-size: 8rem;
            opacity: 0.08;
            transform: rotate(-10deg);
        }
        .shop-header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .shop-header p {
            opacity: 0.8;
            font-size: 1.1rem;
        }
        .shop-header .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 15px 0 0 0;
        }
        .shop-header .breadcrumb a {
            color: var(--gold);
            text-decoration: none;
        }
        .shop-header .breadcrumb a:hover {
            text-decoration: underline;
        }
        .shop-header .breadcrumb .active {
            color: rgba(255,255,255,0.6);
        }
        
        /* ==================== FILTER SECTION ==================== */
        .filter-section {
            background: white;
            border-radius: 16px;
            padding: 20px 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            border: 1px solid #f0ece8;
        }
        .filter-section .filter-label {
            font-weight: 600;
            color: var(--text);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 6px;
        }
        .filter-chip {
            background: #f5f0ec;
            border-radius: 30px;
            padding: 5px 16px;
            margin: 3px 4px 3px 0;
            cursor: pointer;
            border: none;
            display: inline-block;
            font-size: 0.8rem;
            transition: 0.25s;
            color: #555;
            font-weight: 500;
        }
        .filter-chip.active, .filter-chip:hover {
            background: var(--wine);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(114,47,55,0.2);
        }
        .filter-chip i {
            margin-right: 4px;
        }
        .filter-results {
            font-size: 0.85rem;
            color: #888;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f0ece8;
        }
        
        /* ==================== WINE CARDS - PREMIUM LAYOUT ==================== */
        .wine-card {
            border: none;
            border-radius: 16px;
            background: white;
            overflow: hidden;
            transition: all 0.35s ease;
            height: 100%;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            border: 1px solid #f0ece8;
        }
        .wine-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(114,47,55,0.10);
            border-color: var(--gold);
        }
        .wine-card .card-image-wrap {
            position: relative;
            overflow: hidden;
            background: #faf8f6;
            padding: 20px;
            height: 260px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wine-card .card-image-wrap img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.5s ease;
        }
        .wine-card:hover .card-image-wrap img {
            transform: scale(1.04);
        }
        .wine-card .card-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--gold);
            color: #1a1a2e;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 30px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .wine-card .card-body {
            padding: 18px 20px 20px 20px;
            text-align: center;
        }
        .wine-card .wine-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 2px;
            line-height: 1.3;
        }
        .wine-card .wine-variety {
            font-size: 0.82rem;
            color: #999;
            margin-bottom: 4px;
        }
        .wine-card .wine-origin {
            font-size: 0.75rem;
            color: #bbb;
            margin-bottom: 8px;
        }
        .wine-card .wine-origin i {
            margin-right: 4px;
        }
        .wine-card .wine-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--green);
            margin: 6px 0 10px 0;
        }
        .wine-card .wine-price .price-unit {
            font-size: 0.7rem;
            font-weight: 400;
            color: #999;
        }
        .wine-card .btn-add {
            background: var(--wine);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 8px 20px;
            font-size: 0.82rem;
            font-weight: 500;
            transition: 0.3s;
            width: 100%;
        }
        .wine-card .btn-add:hover {
            background: #5a232a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(114,47,55,0.25);
        }
        .wine-card .btn-add i {
            margin-right: 6px;
        }
        
        /* ==================== EMPTY STATE ==================== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
            border: 2px dashed #e8e0d8;
        }
        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        .empty-state h4 {
            color: #555;
        }
        .empty-state p {
            color: #999;
        }
        
        /* ==================== TOAST ==================== */
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
        
        /* ==================== FOOTER ==================== */
        footer {
            background: var(--dark);
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
        
        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 768px) {
            .shop-header h1 { font-size: 2rem; }
            .shop-header { padding: 40px 0 30px 0; }
            .shop-header::after { font-size: 4rem; right: 20px; bottom: 10px; }
            .wine-card .card-image-wrap { height: 200px; padding: 15px; }
            .filter-section { padding: 15px; }
            .filter-chip { font-size: 0.75rem; padding: 4px 12px; }
        }
        @media (max-width: 576px) {
            .shop-header h1 { font-size: 1.6rem; }
            .wine-card .card-image-wrap { height: 170px; }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <!-- ==================== SHOP HEADER ==================== -->
    <div class="shop-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-wine-glass-alt me-2" style="color:var(--gold);"></i>Our Wine Collection</h1>
                    <p>Handpicked selections from the world's finest vineyards</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Wines</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-light text-dark px-3 py-2" style="border-radius:30px;">
                        <i class="fas fa-wine-bottle me-1" style="color:var(--wine);"></i>
                        <span id="wineCount">0</span> wines
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="container">
        <div id="mainContent">
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-3x" style="color:var(--wine);"></i>
                <p class="mt-3 text-muted">Loading our wine collection...</p>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <div id="toastMsg" class="toast-msg"></div>

    <script>
        const API_URL = 'backend/';
        let wines = [];
        let currentFilter = { structure: "", taste: "", origin: "" };
        
        // Generate persistent session ID
        let sessionId = localStorage.getItem('cartSessionId');
        if (!sessionId) {
            sessionId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('cartSessionId', sessionId);
        }

        // ==================== TOAST ====================
        function showToast(message, isError = false) {
            const toast = document.getElementById('toastMsg');
            toast.innerText = message;
            toast.className = 'toast-msg' + (isError ? ' toast-error' : '');
            toast.style.display = 'block';
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => { toast.style.display = 'none'; }, 3000);
        }

        // ==================== API HELPERS ====================
        async function fetchAPI(endpoint) {
            try {
                const res = await fetch(API_URL + endpoint);
                return await res.json();
            } catch(e) {
                return [];
            }
        }

        async function updateCartCount() {
            try {
                const res = await fetch(API_URL + 'get-cart-count.php?sessionId=' + sessionId);
                const data = await res.json();
                const count = data.count || 0;
                document.querySelectorAll('.cart-count').forEach(el => el.innerText = count);
            } catch(e) {
                console.error('Error updating cart count:', e);
            }
        }

        // ==================== ADD TO CART ====================
        async function addToCart(id, name, price) {
            try {
                const response = await fetch(API_URL + 'add-to-cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        productId: id, 
                        productType: 'wine', 
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
                    showToast('Error: ' + (result.error || 'Could not add to cart'), true);
                }
            } catch(e) {
                showToast('Error adding to cart. Please try again.', true);
            }
        }

        // ==================== HELPERS ====================
        function getImageUrl(path) {
            if (!path) return 'images/placeholder.jpg';
            if (path.startsWith('http')) return path;
            return path;
        }

        function formatPrice(p) { 
            return 'E' + parseFloat(p).toFixed(2); 
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function getOriginShort(origin) {
            if (!origin) return '';
            const parts = origin.split(',');
            return parts[0].trim();
        }

        // ==================== RENDER WINES ====================
        function renderWines(winesToShow) {
            const container = document.getElementById('mainContent');
            const count = winesToShow.length;
            document.getElementById('wineCount').textContent = count;
            
            // Get filter options
            let structures = [...new Set(wines.map(w => w.structure).filter(Boolean))];
            let tastes = [...new Set(wines.map(w => w.taste ? w.taste.split(',')[0].trim() : '').filter(Boolean))];
            let origins = [...new Set(wines.map(w => w.origin ? w.origin.split(',')[0].trim() : '').filter(Boolean))];
            
            container.innerHTML = `
                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <span class="filter-label"><i class="fas fa-wine-bottle me-1"></i> Structure</span>
                            <div>
                                <span class="filter-chip ${currentFilter.structure === '' ? 'active' : ''}" onclick="setFilter('structure', '')">
                                    <i class="fas fa-undo-alt"></i> All
                                </span>
                                ${structures.map(s => `
                                    <span class="filter-chip ${currentFilter.structure === s ? 'active' : ''}" onclick="setFilter('structure', '${s}')">
                                        <i class="fas fa-circle" style="font-size:0.5rem;vertical-align:middle;"></i> ${s}
                                    </span>
                                `).join('')}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <span class="filter-label"><i class="fas fa-utensils me-1"></i> Taste Profile</span>
                            <div>
                                <span class="filter-chip ${currentFilter.taste === '' ? 'active' : ''}" onclick="setFilter('taste', '')">
                                    <i class="fas fa-undo-alt"></i> All
                                </span>
                                ${tastes.map(t => `
                                    <span class="filter-chip ${currentFilter.taste === t ? 'active' : ''}" onclick="setFilter('taste', '${t}')">
                                        <i class="fas fa-circle" style="font-size:0.5rem;vertical-align:middle;"></i> ${t}
                                    </span>
                                `).join('')}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <span class="filter-label"><i class="fas fa-map-marker-alt me-1"></i> Origin</span>
                            <div>
                                <span class="filter-chip ${currentFilter.origin === '' ? 'active' : ''}" onclick="setFilter('origin', '')">
                                    <i class="fas fa-undo-alt"></i> All
                                </span>
                                ${origins.map(o => `
                                    <span class="filter-chip ${currentFilter.origin === o ? 'active' : ''}" onclick="setFilter('origin', '${o}')">
                                        <i class="fas fa-circle" style="font-size:0.5rem;vertical-align:middle;"></i> ${o}
                                    </span>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                    <div class="filter-results">
                        <i class="fas fa-wine-glass-alt me-1"></i>
                        Showing <strong>${count}</strong> ${count === 1 ? 'wine' : 'wines'}
                        ${count < wines.length ? `(filtered from ${wines.length})` : ''}
                        ${count === 0 ? '— try adjusting your filters' : ''}
                    </div>
                </div>
                
                <!-- Wines Grid -->
                ${count === 0 ? `
                    <div class="empty-state">
                        <i class="fas fa-wine-bottle"></i>
                        <h4>No wines match your filters</h4>
                        <p>Try adjusting your filter selection above</p>
                        <button class="btn btn-outline-wine" onclick="setFilter('structure', ''); setFilter('taste', ''); setFilter('origin', '');">
                            <i class="fas fa-undo-alt me-2"></i>Clear all filters
                        </button>
                    </div>
                ` : `
                    <div class="row g-4">
                        ${winesToShow.map(w => `
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="wine-card">
                                    <div class="card-image-wrap">
                                        <img src="${getImageUrl(w.image_url)}" 
                                             onerror="this.onerror=null;this.src='images/placeholder.jpg'" 
                                             alt="${escapeHtml(w.name)}">
                                        ${w.featured ? '<span class="card-badge">⭐ Featured</span>' : ''}
                                    </div>
                                    <div class="card-body">
                                        <div class="wine-name">${escapeHtml(w.name)}</div>
                                        <div class="wine-variety">${escapeHtml(w.variety)} ${w.vintage ? '• ' + w.vintage : ''}</div>
                                        ${w.origin ? `<div class="wine-origin"><i class="fas fa-map-marker-alt"></i> ${escapeHtml(getOriginShort(w.origin))}</div>` : ''}
                                        <div class="wine-price">${formatPrice(w.price)} <span class="price-unit">/ bottle</span></div>
                                        <button class="btn-add" onclick="addToCart(${w.id}, '${escapeHtml(w.name)}', ${w.price})">
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `}
            `;
        }

        // ==================== SET FILTER ====================
        function setFilter(type, value) {
            currentFilter[type] = value;
            
            let filtered = wines.filter(w => 
                (currentFilter.structure === "" || (w.structure && w.structure.includes(currentFilter.structure))) &&
                (currentFilter.taste === "" || (w.taste && w.taste.toLowerCase().includes(currentFilter.taste.toLowerCase()))) &&
                (currentFilter.origin === "" || (w.origin && w.origin.includes(currentFilter.origin)))
            );
            
            renderWines(filtered);
        }

        // ==================== LOAD WINES ====================
        async function loadWines() {
            try {
                wines = await fetchAPI('get-wines.php');
                
                if (!wines || wines.length === 0) {
                    document.getElementById('mainContent').innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-wine-bottle"></i>
                            <h4>No wines available</h4>
                            <p>Check back soon for our latest collection</p>
                        </div>
                    `;
                    return;
                }
                
                // Sort by price (low to high)
                wines.sort((a, b) => a.price - b.price);
                
                renderWines(wines);
                updateCartCount();
            } catch(e) {
                console.error('Error loading wines:', e);
                document.getElementById('mainContent').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle" style="color:var(--gold);"></i>
                        <h4>Oops! Something went wrong</h4>
                        <p>We're having trouble loading our wine collection. Please try again later.</p>
                    </div>
                `;
            }
        }

        // ==================== INITIALIZE ====================
        document.addEventListener('DOMContentLoaded', function() {
            loadWines();
            updateCartCount();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>