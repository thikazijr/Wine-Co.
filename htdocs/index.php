<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wine & Co. - Premium Wine Club | Eswatini</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="apple-touch-icon" href="favicon.png">
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
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
        .nav-link {
            color: #4a2c2a !important;
            font-weight: 500;
            margin: 0 8px;
            padding: 8px 12px !important;
            border-radius: 8px;
            transition: 0.2s;
        }
        .nav-link:hover {
            color: var(--wine) !important;
            background: rgba(114,47,55,0.05);
        }
        
        .cart-icon {
            position: relative;
            color: var(--wine);
            text-decoration: none;
            font-size: 1.2rem;
            padding: 8px 12px;
            border-radius: 8px;
            transition: 0.2s;
        }
        .cart-icon:hover {
            background: rgba(114,47,55,0.05);
        }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--gold);
            color: #333;
            border-radius: 50%;
            padding: 2px 7px;
            font-size: 11px;
            font-weight: bold;
            min-width: 20px;
            text-align: center;
        }
        
        /* ==================== SLIDESHOW ==================== */
        .slideshow-container {
            position: relative;
            max-width: 1200px;
            margin: 0 auto 50px auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        }
        .slideshow-container .slide {
            display: none;
            width: 100%;
            height: 520px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        .slideshow-container .slide.active {
            display: block;
            animation: fadeIn 0.8s ease-in-out;
        }
        .slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 100%);
        }
        .slide-content {
            position: absolute;
            bottom: 80px;
            left: 60px;
            color: white;
            max-width: 500px;
            z-index: 2;
        }
        .slide-content h2 {
            font-size: 2.8rem;
            font-weight: 700;
            text-shadow: 2px 4px 12px rgba(0,0,0,0.5);
            margin-bottom: 8px;
        }
        .slide-content p {
            font-size: 1.2rem;
            opacity: 0.9;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
            margin-bottom: 16px;
        }
        .slide-content .btn {
            border-radius: 40px;
            padding: 10px 32px;
            font-weight: 600;
            transition: 0.3s;
        }
        .slide-content .btn-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        
        .slide-dots {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 10;
        }
        .slide-dots .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.4);
            cursor: pointer;
            transition: 0.3s;
            border: 2px solid transparent;
        }
        .slide-dots .dot.active {
            background: white;
            border-color: var(--gold);
            transform: scale(1.25);
        }
        .slide-dots .dot:hover {
            background: white;
            transform: scale(1.15);
        }
        
        .slide-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.35);
            color: white;
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            font-size: 20px;
            cursor: pointer;
            transition: 0.3s;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        .slide-arrow:hover {
            background: rgba(0,0,0,0.6);
            transform: translateY(-50%) scale(1.05);
        }
        .slide-arrow.prev { left: 20px; }
        .slide-arrow.next { right: 20px; }
        
        @keyframes fadeIn {
            from { opacity: 0.4; transform: scale(1.02); }
            to { opacity: 1; transform: scale(1); }
        }
        
        /* ==================== HERO ==================== */
        .hero {
            background: linear-gradient(135deg, #1a0f0f 0%, #3d2020 50%, #5a3028 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
            border-radius: 0 0 50px 50px;
        }
        .hero h1 {
            font-size: 3.2rem;
            font-weight: 700;
            text-shadow: 2px 4px 12px rgba(0,0,0,0.3);
        }
        .hero .btn-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        
        /* ==================== WINE CARDS ==================== */
        .wine-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            transition: 0.3s;
            background: white;
            height: 100%;
            padding: 20px;
            text-align: center;
        }
        .wine-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(114,47,55,0.12);
        }
        .wine-card-img {
            width: 100%;
            height: 260px;
            object-fit: contain;
            object-position: center;
            padding: 10px;
            background: white;
            transition: transform 0.4s;
        }
        .wine-card:hover .wine-card-img {
            transform: scale(1.03);
        }
        .wine-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c1a1a;
            margin-bottom: 2px;
        }
        .wine-card-variety {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 5px;
        }
        .wine-card-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--green);
            margin: 8px 0;
        }
        .wine-card .btn {
            border-radius: 40px;
            padding: 8px 24px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        /* ==================== BOX CARDS ==================== */
        .box-section {
            padding: 60px 0;
            background: white;
            border-radius: 30px;
            margin: 40px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        .box-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
            transition: 0.3s;
            background: white;
            height: 100%;
            padding: 30px;
            text-align: center;
            border: 1px solid #f0ece8;
        }
        .box-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.08);
        }
        .box-card .box-icon {
            font-size: 2.8rem;
            color: var(--wine);
            margin-bottom: 15px;
        }
        .box-card .box-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c1a1a;
        }
        .box-card .box-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--green);
            margin: 10px 0;
        }
        .box-card.featured {
            border: 2px solid var(--gold);
            position: relative;
        }
        .box-card .popular-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gold);
            color: #1a1a2e;
            padding: 4px 18px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-gold {
            background: var(--gold);
            color: #1a1a2e;
            border-radius: 40px;
            padding: 10px 30px;
            border: none;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-gold:hover {
            background: #b8922f;
            transform: translateY(-2px);
            color: #1a1a2e;
        }
        
        /* ==================== OFFER BANNER ==================== */
        .offer-banner {
            background: linear-gradient(135deg, var(--wine), var(--gold));
            color: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            margin: 40px 0;
        }
        .offer-banner h3 {
            font-size: 1.8rem;
            font-weight: 700;
        }
        .offer-banner .btn-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        /* ==================== MAGAZINE SECTION ==================== */
        .magazine-section {
            padding: 50px 0;
            background: white;
            border-radius: 30px;
            margin: 40px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        .magazine-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.05);
            transition: 0.3s;
            background: white;
            height: 100%;
        }
        .magazine-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.1);
        }
        .magazine-card .magazine-img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            background: #f8f4f0;
        }
        .magazine-card .magazine-body {
            padding: 25px;
            text-align: center;
        }
        .magazine-card .magazine-body h4 {
            color: var(--wine);
            font-weight: 700;
        }
        .magazine-card .magazine-body .badge-free {
            background: #d4edda;
            color: #155724;
            padding: 4px 16px;
            border-radius: 30px;
            font-weight: 600;
        }
        .magazine-card .magazine-body .badge-paid {
            background: #fff3cd;
            color: #856404;
            padding: 4px 16px;
            border-radius: 30px;
            font-weight: 600;
        }
        .magazine-features-list {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }
        .magazine-features-list li {
            padding: 5px 0;
            color: #555;
            font-size: 0.9rem;
        }
        .magazine-features-list li i {
            color: var(--gold);
            margin-right: 8px;
        }
        
        /* ==================== BUTTONS ==================== */
        .btn-wine {
            background: var(--wine);
            color: white;
            border-radius: 40px;
            padding: 8px 28px;
            border: none;
            transition: 0.3s;
            font-weight: 500;
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
            padding: 6px 24px;
            background: transparent;
            transition: 0.3s;
            font-weight: 500;
        }
        .btn-outline-wine:hover {
            background: var(--wine);
            color: white;
            transform: translateY(-2px);
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
            transition: 0.3s;
        }
        footer a:hover {
            color: var(--gold);
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
        
        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 992px) {
            .slideshow-container .slide { height: 400px; }
            .slide-content h2 { font-size: 2.2rem; }
            .hero h1 { font-size: 2.5rem; }
        }
        @media (max-width: 768px) {
            .slideshow-container .slide { height: 320px; }
            .slide-content { bottom: 40px; left: 25px; max-width: 85%; }
            .slide-content h2 { font-size: 1.6rem; }
            .slide-content p { font-size: 0.95rem; }
            .slide-arrow { width: 36px; height: 36px; font-size: 14px; }
            .slide-arrow.prev { left: 10px; }
            .slide-arrow.next { right: 10px; }
            .hero h1 { font-size: 2rem; }
            .hero { padding: 50px 0; }
            .wine-card-img { height: 200px; }
            .box-section h2 { font-size: 1.6rem; }
            .box-card { padding: 20px; }
            .offer-banner h3 { font-size: 1.3rem; }
            .offer-banner { padding: 25px; }
            .magazine-card .magazine-img { height: 200px; }
        }
        @media (max-width: 576px) {
            .slideshow-container .slide { height: 260px; }
            .slide-content h2 { font-size: 1.2rem; }
            .slide-content p { font-size: 0.8rem; }
            .slide-dots .dot { width: 8px; height: 8px; }
            .slide-arrow { width: 30px; height: 30px; font-size: 12px; }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div id="mainContent">
        <!-- ==================== SLIDESHOW ==================== -->
        <div class="container">
            <div class="slideshow-container" id="slideshowContainer">
                <div class="slide-dots" id="slideDots"></div>
                <button class="slide-arrow prev" onclick="changeSlide(-1)">❮</button>
                <button class="slide-arrow next" onclick="changeSlide(1)">❯</button>
            </div>
        </div>

        <!-- ==================== HERO ==================== -->
        <div class="hero">
            <div class="container text-center">
                <h1 class="display-4 fw-bold mb-3">Discover Fine Wines<br>in Eswatini</h1>
                <p class="lead mb-4">Curated selections • Delivered to your door</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="shop.php" class="btn btn-light btn-lg rounded-pill px-5">Explore Wines →</a>
                    <a href="subscription.php" class="btn btn-outline-light btn-lg rounded-pill px-5">Join Wine Club →</a>
                </div>
                <div class="mt-4">
                    <span class="badge bg-warning text-dark px-3 py-2">💰 All prices in Swaziland Lilangeni (E)</span>
                </div>
            </div>
        </div>

        <!-- ==================== FEATURED WINES ==================== -->
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Featured Wines</h2>
                <p class="text-muted">Hand-picked selections from our sommeliers</p>
            </div>
            <div class="row" id="featuredWines"></div>
        </div>

        <!-- ==================== WINE BOX SECTION ==================== -->
        <div class="container">
            <div class="box-section">
                <div class="text-center">
                    <h2>Monthly Wine Surprises</h2>
                    <p class="text-muted">Explore the world of undiscovered wines with our Wine Surprise Boxes</p>
                    <a href="subscription.php" class="btn btn-wine">Subscribe Now</a>
                </div>
                <div class="row mt-5">
                    <div class="col-md-4 mb-4">
                        <div class="box-card">
                            <div class="box-icon"><i class="fas fa-wine-bottle"></i></div>
                            <div class="box-name">Essential Elegance Box</div>
                            <div class="box-price">E499<span style="font-size:0.9rem;">/month</span></div>
                            <p class="text-muted small">2 premium wines • Tasting notes • Free delivery</p>
                            <a href="subscription.php" class="btn btn-gold">Subscribe Now</a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="box-card featured">
                            <div class="popular-badge">⭐ Most Popular</div>
                            <div class="box-icon"><i class="fas fa-crown"></i></div>
                            <div class="box-name">Vineyard Voyager Box</div>
                            <div class="box-price">E999<span style="font-size:0.9rem;">/month</span></div>
                            <p class="text-muted small">4 reserve wines • Premium notes • Free delivery</p>
                            <a href="subscription.php" class="btn btn-gold">Subscribe Now</a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="box-card">
                            <div class="box-icon"><i class="fas fa-gem"></i></div>
                            <div class="box-name">Luxury Reserve Box</div>
                            <div class="box-price">E1,999<span style="font-size:0.9rem;">/month</span></div>
                            <p class="text-muted small">6 rare vintages • Personal advisor • Exclusive access</p>
                            <a href="subscription.php" class="btn btn-gold">Subscribe Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== OFFER BANNER ==================== -->
        <div class="container">
            <div class="offer-banner">
                <h3 class="mb-2">🎁 Exclusive Offer for First-Time Subscribers</h3>
                <p>Explore our special boxes at an irresistible price, exclusively for new subscribers!</p>
                <a href="subscription.php" class="btn btn-light rounded-pill px-5">Subscribe Now</a>
            </div>
        </div>

        <!-- ==================== MAGAZINE SECTION (NEW) ==================== -->
        <div class="container">
            <div class="magazine-section">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="magazine-card">
                            <img src="images/magazine-cover.jpg" class="magazine-img" 
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22280%22%3E%3Crect fill=%22%23f5ede6%22 width=%22400%22 height=%22280%22/%3E%3Ctext x=%22200%22 y=%22140%22 text-anchor=%22middle%22 font-family=%22Georgia%22 font-size=%2230%22 fill=%22%23722f37%22%3EWINE%26Co.%3C/text%3E%3Ctext x=%22200%22 y=%22180%22 text-anchor=%22middle%22 font-family=%22Georgia%22 font-size=%2220%22 fill=%22%23c9a03d%22%3EBoutique Magazine%3C/text%3E%3C/svg%3E'" 
                                 alt="Wine&Co. Boutique Magazine">
                            <div class="magazine-body">
                                <div class="d-flex justify-content-center gap-3 mb-2">
                                    <span class="badge-free"><i class="fas fa-eye me-1"></i>Free to View</span>
                                    <span class="badge-paid"><i class="fas fa-download me-1"></i>E45 to Download</span>
                                </div>
                                <h4>Wine&Co. Boutique Magazine</h4>
                                <p class="text-muted small">Curated · Crafted · Delivered</p>
                                <ul class="magazine-features-list">
                                    <li><i class="fas fa-check-circle"></i>Complete Wine Guide</li>
                                    <li><i class="fas fa-check-circle"></i>Expert Pairing Tips</li>
                                    <li><i class="fas fa-check-circle"></i>Cape Winelands Explorer</li>
                                    <li><i class="fas fa-check-circle"></i>Cellaring &amp; Gifting Guide</li>
                                </ul>
                                <a href="view-magazine.php" class="btn btn-wine">
                                    <i class="fas fa-book-open me-2"></i>Read Magazine Free
                                </a>
                                <a href="view-magazine.php#download" class="btn btn-gold ms-2">
                                    <i class="fas fa-download me-2"></i>Download PDF (E45)
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 p-md-4">
                            <h2 style="color:var(--wine);">Discover Our Boutique Magazine</h2>
                            <p class="text-muted">Your premium guide to the world of wine — from the Cape Winelands to your table.</p>
                            <div class="row g-3 mt-3">
                                <div class="col-6">
                                    <div class="bg-light p-3 rounded-3 text-center">
                                        <i class="fas fa-wine-glass-alt fa-2x" style="color:var(--gold);"></i>
                                        <h6 class="mt-2 mb-0">24 Pages</h6>
                                        <small class="text-muted">Full guide</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light p-3 rounded-3 text-center">
                                        <i class="fas fa-images fa-2x" style="color:var(--gold);"></i>
                                        <h6 class="mt-2 mb-0">Premium Design</h6>
                                        <small class="text-muted">High quality</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light p-3 rounded-3 text-center">
                                        <i class="fas fa-globe-africa fa-2x" style="color:var(--gold);"></i>
                                        <h6 class="mt-2 mb-0">Cape Winelands</h6>
                                        <small class="text-muted">Explore regions</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light p-3 rounded-3 text-center">
                                        <i class="fas fa-utensils fa-2x" style="color:var(--gold);"></i>
                                        <h6 class="mt-2 mb-0">Pairings</h6>
                                        <small class="text-muted">Food &amp; wine</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="small text-muted">
                                    <i class="fas fa-info-circle me-1" style="color:var(--gold);"></i>
                                    Browse the full magazine online for free. Download the PDF for a small fee.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <div id="toastMsg" class="toast-msg"></div>

    <script>
        // ==================== CONSTANTS & SESSION ====================
        const API_URL = 'backend/';
        
        // Generate persistent session ID
        let sessionId = localStorage.getItem('cartSessionId');
        if (!sessionId) {
            sessionId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('cartSessionId', sessionId);
        }
        console.log('Session ID:', sessionId);

        // ==================== TOAST NOTIFICATION ====================
        function showToast(message, isError = false) {
            const toast = document.getElementById('toastMsg');
            toast.innerText = message;
            toast.className = 'toast-msg' + (isError ? ' toast-error' : '');
            toast.style.display = 'block';
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => { toast.style.display = 'none'; }, 3000);
        }

        // ==================== SLIDESHOW ====================
        const slides = [
            { image: '/uploads/wines/cloudybay.png', title: 'Cloudy Bay', subtitle: 'Premium New Zealand Chardonnay', link: 'shop.php' },
            { image: '/uploads/wines/kanonkop.png', title: 'Kanonkop Pinotage', subtitle: 'South Africa\'s Flagship Wine', link: 'shop.php' },
            { image: '/uploads/wines/grand-vin-bordeaux-medoc.png', title: 'Grand Vin de Bordeaux', subtitle: 'Prestigious Médoc Selection', link: 'shop.php' },
            { image: '/uploads/wines/franschhoek-cellar-sauvignon-blanc.png', title: 'Franschhoek Cellar', subtitle: 'Vibrant Sauvignon Blanc', link: 'shop.php' },
            { image: '/uploads/wines/billingham-big-oak-red.png', title: 'Billingham Big Oak', subtitle: 'Bold Mozambican Red Blend', link: 'shop.php' }
        ];

        let currentSlide = 0;
        let slideInterval;

        function initSlideshow() {
            const container = document.getElementById('slideshowContainer');
            const dotsContainer = document.getElementById('slideDots');
            
            // Clear existing slides
            container.querySelectorAll('.slide').forEach(s => s.remove());
            dotsContainer.innerHTML = '';
            
            slides.forEach((slide, index) => {
                const slideDiv = document.createElement('div');
                slideDiv.className = `slide ${index === 0 ? 'active' : ''}`;
                // Pre-check if image exists
                const img = new Image();
                img.onload = function() {
                    slideDiv.style.backgroundImage = `url('${slide.image}')`;
                };
                img.onerror = function() {
                    slideDiv.style.backgroundImage = `linear-gradient(135deg, #722f37, #c9a03d)`;
                };
                img.src = slide.image;
                
                slideDiv.innerHTML = `
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <h2>${slide.title}</h2>
                        <p>${slide.subtitle}</p>
                        <a href="${slide.link}" class="btn btn-light rounded-pill px-4">Explore →</a>
                    </div>
                `;
                container.appendChild(slideDiv);
                
                const dot = document.createElement('span');
                dot.className = `dot ${index === 0 ? 'active' : ''}`;
                dot.onclick = () => goToSlide(index);
                dotsContainer.appendChild(dot);
            });
            
            setTimeout(startAutoSlide, 1000);
        }

        function changeSlide(direction) {
            const slidesList = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');
            if (slidesList.length === 0) return;
            
            slidesList[currentSlide].classList.remove('active');
            dots[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + direction + slidesList.length) % slidesList.length;
            slidesList[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
            resetAutoSlide();
        }

        function goToSlide(index) {
            const slidesList = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');
            if (slidesList.length === 0) return;
            
            slidesList[currentSlide].classList.remove('active');
            dots[currentSlide].classList.remove('active');
            currentSlide = index;
            slidesList[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
            resetAutoSlide();
        }

        function startAutoSlide() {
            if (slideInterval) clearInterval(slideInterval);
            slideInterval = setInterval(() => changeSlide(1), 5000);
        }

        function resetAutoSlide() {
            clearInterval(slideInterval);
            startAutoSlide();
        }

        // ==================== CART FUNCTIONS (GLOBALLY AVAILABLE) ====================
        
        /**
         * Update the cart count in the UI
         */
        async function updateCartCount() {
            try {
                const res = await fetch(API_URL + 'get-cart-count.php?sessionId=' + sessionId);
                const data = await res.json();
                const count = data.count || 0;
                document.querySelectorAll('.cart-count').forEach(el => el.innerText = count);
                const navCart = document.getElementById('cartCount');
                if (navCart) navCart.innerText = count;
            } catch(e) {
                console.error('Error updating cart count:', e);
                // Fallback to localStorage
                let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                const total = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
                document.querySelectorAll('.cart-count').forEach(el => el.innerText = total);
            }
        }

        /**
         * Add item to cart - GLOBAL function accessible from onclick
         */
        window.addToCart = async function(id, name, price) {
            console.log('🛒 Adding to cart:', { id, name, price, sessionId });
            
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
                console.log('📦 Response:', result);
                
                if (result.success) {
                    showToast(`✓ ${name} added to cart!`);
                    updateCartCount();
                } else {
                    showToast('Error: ' + (result.error || 'Could not add to cart'), true);
                }
            } catch(e) {
                console.error('❌ Error adding to cart:', e);
                // Fallback to localStorage
                try {
                    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                    const existing = cart.find(item => item.id === id && item.type === 'wine');
                    if (existing) {
                        existing.quantity++;
                    } else {
                        cart.push({ id, name, price, quantity: 1, type: 'wine' });
                    }
                    localStorage.setItem('cart', JSON.stringify(cart));
                    showToast(`✓ ${name} added to cart!`);
                    updateCartCount();
                } catch(err) {
                    showToast('Error adding to cart. Please try again.', true);
                }
            }
        };

        // ==================== HELPER FUNCTIONS ====================
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

        // ==================== LOAD FEATURED WINES ====================
        async function loadFeaturedWines() {
            const container = document.getElementById('featuredWines');
            if (!container) return;
            
            container.innerHTML = '<div class="col-12 text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br><span class="text-muted">Loading wines...</span></div>';
            
            try {
                const res = await fetch(API_URL + 'get-wines.php?featured=true');
                const wines = await res.json();
                
                if (wines.error) {
                    container.innerHTML = '<div class="col-12 text-center py-4"><p class="text-muted">Error loading wines</p></div>';
                    return;
                }
                
                if (!wines || wines.length === 0) {
                    container.innerHTML = '<div class="col-12 text-center py-4"><p class="text-muted">No featured wines available</p></div>';
                    return;
                }
                
                container.innerHTML = wines.slice(0, 4).map(w => `
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="wine-card">
                            <img src="${getImageUrl(w.image_url)}" class="wine-card-img" onerror="this.onerror=null;this.src='images/placeholder.jpg'" alt="${escapeHtml(w.name)}">
                            <div class="wine-card-title">${escapeHtml(w.name)}</div>
                            <div class="wine-card-variety">${escapeHtml(w.variety)} • ${w.vintage || 'N/A'}</div>
                            <div class="wine-card-price">${formatPrice(w.price)}</div>
                            <button class="btn btn-wine btn-sm" onclick="addToCart(${w.id}, '${escapeHtml(w.name)}', ${w.price})">
                                <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                `).join('');
            } catch(e) {
                console.error('Error loading wines:', e);
                container.innerHTML = '<div class="col-12 text-center py-4"><p class="text-muted">Error loading wines</p></div>';
            }
        }

        // ==================== INITIALIZE ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize slideshow
            const container = document.getElementById('slideshowContainer');
            if (container) {
                container.addEventListener('mouseenter', () => clearInterval(slideInterval));
                container.addEventListener('mouseleave', startAutoSlide);
                initSlideshow();
            }
            
            // Load featured wines
            loadFeaturedWines();
            
            // Update cart count
            updateCartCount();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>