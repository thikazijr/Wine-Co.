<?php
// view-magazine.php - Complete working version
// Free to view, download after admin approval

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Continue without DB
}

// Get magazine settings
$settings = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM magazine_settings");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    } catch(PDOException $e) {}
}

// ============================================================
// 1. GET PDF PATH
// ============================================================
$pdfPath = $settings['pdf_path'] ?? 'downloads/WineCo_Boutique_Magazine_Professional_Edition.pdf';

// Check if file exists using absolute server path
$pdfFound = false;
$possiblePaths = [
    $_SERVER['DOCUMENT_ROOT'] . '/' . $pdfPath,
    $_SERVER['DOCUMENT_ROOT'] . '/downloads/WineCo_Boutique_Magazine_Professional_Edition.pdf',
    $_SERVER['DOCUMENT_ROOT'] . '/downloads/WineCo_Boutique_Magazine_Professional_Edition (1).pdf',
    'downloads/WineCo_Boutique_Magazine_Professional_Edition.pdf',
    'downloads/WineCo_Boutique_Magazine_Professional_Edition (1).pdf',
];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $pdfFound = true;
        // Get the relative path for the URL
        if (strpos($path, $_SERVER['DOCUMENT_ROOT']) === 0) {
            $pdfPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
        } else {
            $pdfPath = $path;
        }
        break;
    }
}

// If still not found, try to find any PDF in downloads folder
if (!$pdfFound) {
    $downloadsDir = $_SERVER['DOCUMENT_ROOT'] . '/downloads/';
    if (is_dir($downloadsDir)) {
        $files = scandir($downloadsDir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                $pdfPath = '/downloads/' . $file;
                $pdfFound = true;
                break;
            }
        }
    }
}

// Build full URL for PDF
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$pdfUrl = $baseUrl . $pdfPath;

// ============================================================
// 2. GET COVER IMAGE PATH
// ============================================================
$coverPath = $settings['cover_image'] ?? 'images/magazine-cover.png';
$coverFound = false;
$possibleCoverPaths = [
    $_SERVER['DOCUMENT_ROOT'] . '/' . $coverPath,
    $_SERVER['DOCUMENT_ROOT'] . '/images/magazine-cover.png',
    $_SERVER['DOCUMENT_ROOT'] . '/images/magazine-cover.jpg',
    'images/magazine-cover.png',
    'images/magazine-cover.jpg',
];

foreach ($possibleCoverPaths as $path) {
    if (file_exists($path)) {
        $coverFound = true;
        if (strpos($path, $_SERVER['DOCUMENT_ROOT']) === 0) {
            $coverPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
        } else {
            $coverPath = $path;
        }
        break;
    }
}

$coverUrl = $baseUrl . $coverPath;

// If no cover image found, use SVG placeholder
if (!$coverFound) {
    $coverUrl = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='350' height='490'%3E%3Crect fill='%23f5ede6' width='350' height='490'/%3E%3Ctext x='175' y='245' text-anchor='middle' font-family='Georgia' font-size='28' fill='%23722f37'%3EWINE%26Co.%3C/text%3E%3Ctext x='175' y='285' text-anchor='middle' font-family='Georgia' font-size='20' fill='%23c9a03d'%3EMagazine%3C/text%3E%3C/svg%3E";
}

$downloadFee = $settings['download_fee'] ?? '45.00';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wine&Co. Boutique Magazine - Wine & Co. Eswatini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --wine: #722f37; --gold: #c9a03d; --green: #1a6b3c; }
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; }
        
        .magazine-viewer { 
            background: white; 
            border-radius: 20px; 
            padding: 40px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin: 30px 0;
        }
        
        .pdf-viewer-container {
            width: 100%;
            height: 750px;
            border-radius: 15px;
            overflow: hidden;
            background: #f8f4f0;
            position: relative;
            border: 2px solid #e8e0d8;
        }
        
        .pdf-viewer-container embed,
        .pdf-viewer-container object,
        .pdf-viewer-container iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
        
        .pdf-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10;
            cursor: default;
            background: rgba(0,0,0,0.01);
        }
        
        .pdf-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 8rem;
            color: rgba(0,0,0,0.05);
            font-weight: bold;
            pointer-events: none;
            z-index: 5;
            white-space: nowrap;
            font-family: 'Georgia', serif;
            letter-spacing: 10px;
        }
        
        .download-section {
            background: linear-gradient(135deg, #f8f9fa, #f0ece8);
            border-radius: 15px;
            padding: 25px 30px;
            margin-top: 25px;
            border: 2px solid var(--gold);
        }
        
        .btn-gold { 
            background: var(--gold); 
            color: #1a1a2e; 
            border-radius: 40px; 
            border: none; 
            padding: 12px 35px; 
            font-weight: 600; 
            transition: 0.3s; 
        }
        .btn-gold:hover { background: #b8922f; transform: translateY(-2px); color: #1a1a2e; }
        
        .btn-wine { 
            background: var(--wine); 
            color: white; 
            border-radius: 40px; 
            border: none; 
            padding: 12px 30px; 
            font-weight: 600; 
            transition: 0.3s; 
        }
        .btn-wine:hover { background: #5a232a; color: white; transform: translateY(-2px); }
        
        .price-tag {
            background: var(--gold);
            color: #1a1a2e;
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: bold;
            display: inline-block;
            font-size: 1.2rem;
        }
        
        .cover-image {
            width: 100%;
            max-width: 350px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            transition: 0.3s;
            background: #f5ede6;
        }
        .cover-image:hover { transform: scale(1.02); }
        
        .magazine-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 15px 0;
        }
        .magazine-features .feature {
            text-align: center;
            padding: 12px;
            background: #f8f4f0;
            border-radius: 10px;
        }
        .magazine-features .feature i {
            font-size: 1.5rem;
            color: var(--gold);
        }
        .magazine-features .feature h6 {
            font-weight: 600;
            color: var(--wine);
            font-size: 0.8rem;
            margin-top: 4px;
        }
        
        .toast-msg { 
            position: fixed; 
            bottom: 20px; 
            right: 20px; 
            padding: 12px 24px; 
            border-radius: 40px; 
            z-index: 9999; 
            display: none; 
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            font-weight: 500; 
            max-width: 400px; 
        }
        .toast-success { background: var(--green); color: white; }
        .toast-error { background: #dc3545; color: white; }
        .toast-info { background: #17a2b8; color: white; }
        
        .modal-header { background: var(--wine); color: white; border-radius: 20px 20px 0 0; }
        .modal-header .btn-close { filter: brightness(0) invert(1); }
        
        .download-info-box {
            background: #fff3cd;
            border-left: 4px solid var(--gold);
            border-radius: 8px;
            padding: 12px 18px;
            margin-top: 12px;
        }
        
        .badge-free { background: #d4edda; color: #155724; padding: 5px 15px; border-radius: 30px; font-weight: 600; }
        .badge-paid { background: #fff3cd; color: #856404; padding: 5px 15px; border-radius: 30px; font-weight: 600; }
        
        .pdf-error-box {
            background: #f8d7da;
            border: 2px solid #dc3545;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .pdf-error-box i {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .pdf-error-box a {
            color: var(--wine);
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .pdf-viewer-container { height: 450px; }
            .magazine-viewer { padding: 15px; }
            .pdf-watermark { font-size: 4rem; }
            .cover-image { max-width: 200px; }
            .download-section { padding: 15px; }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2><i class="fas fa-book me-2" style="color:var(--wine);"></i>Wine&Co. Boutique Magazine</h2>
                <p class="text-muted">Browse the full magazine online for free. Request download access to save as PDF.</p>
            </div>
        </div>

        <div class="magazine-viewer">
            <!-- Top Section: Cover + Info -->
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="<?php echo $coverUrl; ?>" alt="Wine&Co. Boutique Magazine" class="cover-image" 
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22350%22 height=%22490%22%3E%3Crect fill=%22%23f5ede6%22 width=%22350%22 height=%22490%22/%3E%3Ctext x=%22175%22 y=%22245%22 text-anchor=%22middle%22 font-family=%22Georgia%22 font-size=%2228%22 fill=%22%23722f37%22%3EWINE%26Co.%3C/text%3E%3Ctext x=%22175%22 y=%22285%22 text-anchor=%22middle%22 font-family=%22Georgia%22 font-size=%2220%22 fill=%22%23c9a03d%22%3EMagazine%3C/text%3E%3C/svg%3E'">
                </div>
                <div class="col-md-8">
                    <h3 style="color:var(--wine);">Wine&Co. Boutique Magazine</h3>
                    <p class="text-muted">Curated · Crafted · Delivered</p>
                    
                    <div class="magazine-features">
                        <div class="feature">
                            <i class="fas fa-wine-glass-alt"></i>
                            <h6>Wine Guide</h6>
                        </div>
                        <div class="feature">
                            <i class="fas fa-utensils"></i>
                            <h6>Pairings</h6>
                        </div>
                        <div class="feature">
                            <i class="fas fa-map-marked-alt"></i>
                            <h6>Cape Winelands</h6>
                        </div>
                        <div class="feature">
                            <i class="fas fa-gift"></i>
                            <h6>Gifting Guide</h6>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <span class="badge-free me-2"><i class="fas fa-eye me-1"></i>FREE to View</span>
                        <span class="badge-paid"><i class="fas fa-download me-1"></i>Download: E<?php echo number_format($downloadFee, 2); ?></span>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <!-- PDF Viewer -->
            <div class="pdf-viewer-container" id="pdfViewer">
                <?php if (!$pdfFound || empty($pdfPath)): ?>
                <!-- Show error message if PDF not found -->
                <div class="pdf-error-box">
                    <i class="fas fa-file-pdf"></i>
                    <h4>PDF Magazine Not Found</h4>
                    <p class="text-muted">The magazine PDF file could not be found.</p>
                    <div class="mt-3">
                        <p class="small text-muted">Please upload the PDF to the <code>/downloads/</code> folder.</p>
                        <a href="admin/magazine-manager.php" class="btn btn-wine">Upload Magazine</a>
                    </div>
                </div>
                <?php else: ?>
                <!-- Watermark -->
                <div class="pdf-watermark">FREE PREVIEW</div>
                
                <!-- PDF Embed - Displays the PDF for free viewing -->
                <embed src="<?php echo $pdfUrl; ?>#toolbar=0&navpanes=1&scrollbar=1" 
                       type="application/pdf"
                       id="pdfEmbed"
                       width="100%" 
                       height="100%">
                
                <!-- Overlay to prevent right-click download -->
                <div class="pdf-overlay" id="pdfOverlay" 
                     oncontextmenu="showDownloadMessage(); return false;"
                     onclick="showDownloadMessage();">
                </div>
                <?php endif; ?>
            </div>
            
            <div class="mt-3 text-center text-muted small">
                <i class="fas fa-chevron-down me-1"></i>
                Scroll to browse the full magazine
                <i class="fas fa-chevron-down ms-1"></i>
            </div>
            
            <!-- Download Section -->
            <div class="download-section no-print">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5><i class="fas fa-file-pdf me-2" style="color:var(--wine);"></i>Download the Magazine</h5>
                        <p class="text-muted small mb-0">
                            Request download access to get the full PDF version. 
                            Your request will be reviewed and approved by our team.
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="price-tag me-2">E<?php echo number_format($downloadFee, 2); ?></span>
                        <button class="btn btn-gold" onclick="openRequestModal()">
                            <i class="fas fa-paper-plane me-2"></i>Request Download
                        </button>
                    </div>
                </div>
                <div class="download-info-box">
                    <i class="fas fa-info-circle me-2" style="color:var(--gold);"></i>
                    <small>
                        <strong>How it works:</strong> Submit a request with your details. 
                        Once approved by an admin, you'll receive a download link via email.
                        The E<?php echo number_format($downloadFee, 2); ?> fee covers the digital distribution.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Toast Notification -->
    <div id="toastMsg" class="toast-msg"></div>

    <!-- ==================== REQUEST MODAL ==================== -->
    <div class="modal fade" id="requestModal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-paper-plane me-2"></i>Request Download Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Fill in your details to request download access. An admin will review your request.</p>
                    
                    <form id="requestForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="reqName" class="form-control" required placeholder="John Doe">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" id="reqEmail" class="form-control" required placeholder="john@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="tel" id="reqPhone" class="form-control" placeholder="+268 1234 5678">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Payment Method</label>
                            <select id="reqPayment" class="form-select">
                                <option value="cash">Cash on Delivery</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_money">Mobile Money</option>
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Download fee: <strong>E<?php echo number_format($downloadFee, 2); ?></strong>
                            <br>
                            <small>You'll be contacted with payment instructions.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-wine" onclick="submitRequest()">
                        <i class="fas fa-paper-plane me-2"></i>Submit Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showToast(message, isError = false, isInfo = false) {
            const toast = document.getElementById('toastMsg');
            toast.innerText = message;
            if (isInfo) {
                toast.className = 'toast-msg toast-info';
            } else {
                toast.className = 'toast-msg ' + (isError ? 'toast-error' : 'toast-success');
            }
            toast.style.display = 'block';
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => { toast.style.display = 'none'; }, 4000);
        }

        function showDownloadMessage() {
            showToast('📄 To download, please click the "Request Download" button below.', false, true);
        }

        function openRequestModal() {
            document.getElementById('reqName').value = '';
            document.getElementById('reqEmail').value = '';
            document.getElementById('reqPhone').value = '';
            document.getElementById('reqPayment').value = 'cash';
            new bootstrap.Modal(document.getElementById('requestModal')).show();
        }

        function submitRequest() {
            const name = document.getElementById('reqName').value.trim();
            const email = document.getElementById('reqEmail').value.trim();
            const phone = document.getElementById('reqPhone').value.trim();
            const payment = document.getElementById('reqPayment').value;

            if (!name || !email) {
                showToast('Please fill in your name and email', true);
                return;
            }

            if (!email.includes('@')) {
                showToast('Please enter a valid email address', true);
                return;
            }

            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';

            fetch('backend/request-magazine-download.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    phone: phone,
                    payment_method: payment
                })
            })
            .then(response => response.json())
            .then(result => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Request';
                
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('requestModal')).hide();
                    showToast('✅ Request submitted! You will receive an email when approved.');
                } else {
                    showToast('❌ Error: ' + (result.error || 'Could not submit request'), true);
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Request';
                showToast('❌ Network error. Please try again.', true);
            });
        }

        // Prevent right-click on the PDF area
        document.addEventListener('contextmenu', function(e) {
            const viewer = document.getElementById('pdfViewer');
            if (viewer && viewer.contains(e.target)) {
                showDownloadMessage();
                e.preventDefault();
                return false;
            }
        });

        // Prevent keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey && (e.key === 's' || e.key === 'p' || e.key === 'S' || e.key === 'P')) ||
                (e.ctrlKey && e.shiftKey && e.key === 'i')) {
                const viewer = document.getElementById('pdfViewer');
                if (viewer && viewer.contains(document.activeElement)) {
                    showDownloadMessage();
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Check if PDF loaded properly
        document.addEventListener('DOMContentLoaded', function() {
            const embed = document.getElementById('pdfEmbed');
            const fallback = document.querySelector('.pdf-error-box');
            
            if (embed) {
                embed.onerror = function() {
                    if (fallback) {
                        fallback.style.display = 'flex';
                        embed.style.display = 'none';
                    }
                };
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>