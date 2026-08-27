<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Wine & Co. Eswatini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top py-2">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-wine-bottle me-2"></i>Wine & Co. <span>Eswatini</span>
            </a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto gap-3">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="shop.php">Wines</a></li>
                    <li class="nav-item"><a class="nav-link" href="pairings.php">Pairings</a></li>
                    <li class="nav-item"><a class="nav-link" href="subscription.php">Subscribe</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <div class="d-flex gap-3 align-items-center">
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        <span class="cart-count" id="cartCount">0</span>
                    </a>
                    <a href="login.php" class="btn btn-outline-wine">Login</a>
                    <a href="register.php" class="btn btn-wine">Join Free</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card p-4">
                    <h2 class="text-center mb-4">About Wine & Co.</h2>
                    <p>Wine & Co. was founded with a simple mission: to bring the world's finest wines to wine lovers across Eswatini.</p>
                    <p>We curate selections from prestigious vineyards in Bordeaux, Tuscany, Napa Valley, and our own beautiful Stellenbosch.</p>
                    <h4 class="mt-4">Our Promise</h4>
                    <ul>
                        <li>✓ Carefully selected premium wines</li>
                        <li>✓ Expert food pairing recommendations</li>
                        <li>✓ Fast delivery across Eswatini</li>
                        <li>✓ Exceptional customer service</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-wine-bottle me-2"></i>Wine & Co.</h5>
                    <p class="text-muted">Premium wines delivered to your doorstep in Eswatini.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="shop.php">Our Wines</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="subscription.php">Wine Club</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contact</h5>
                    <p class="text-muted"><i class="fas fa-phone me-2"></i>+268 1234 5678</p>
                    <p class="text-muted"><i class="fas fa-envelope me-2"></i>hello@wineco.co.sz</p>
                </div>
            </div>
            <hr>
            <div class="text-center text-muted">
                <p>© 2025 Wine & Co. — All prices in <strong>Swaziland Lilangeni (E/SZL)</strong></p>
                <small>Sip responsibly • 18+ only</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>