<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed");
}

// Create magazine_settings table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS magazine_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Create magazine_download_requests table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS magazine_download_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(200) NOT NULL,
    customer_email VARCHAR(200) NOT NULL,
    customer_phone VARCHAR(50),
    payment_method VARCHAR(50) DEFAULT 'pending',
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    ip_address VARCHAR(45),
    download_token VARCHAR(100),
    notes TEXT
)");

// Get current settings
$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM magazine_settings");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Get pending requests count
$pendingCount = $pdo->query("SELECT COUNT(*) FROM magazine_download_requests WHERE status = 'pending'")->fetchColumn();
$approvedCount = $pdo->query("SELECT COUNT(*) FROM magazine_download_requests WHERE status = 'approved'")->fetchColumn();
$totalRequests = $pdo->query("SELECT COUNT(*) FROM magazine_download_requests")->fetchColumn();

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_settings') {
            // Handle file uploads
            $pdfPath = $settings['pdf_path'] ?? 'downloads/WineCo_Boutique_Magazine_Professional_Edition.pdf';
            $coverPath = $settings['cover_image'] ?? 'images/magazine-cover.png';
            
            // Upload PDF
            if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
                $pdfFile = $_FILES['pdf_file'];
                $pdfExt = pathinfo($pdfFile['name'], PATHINFO_EXTENSION);
                $pdfName = 'WineCo_Boutique_Magazine.' . $pdfExt;
                $targetPath = '../downloads/' . $pdfName;
                
                if (move_uploaded_file($pdfFile['tmp_name'], $targetPath)) {
                    $pdfPath = 'downloads/' . $pdfName;
                    $stmt = $pdo->prepare("INSERT INTO magazine_settings (setting_key, setting_value) VALUES ('pdf_path', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$pdfPath, $pdfPath]);
                    $message = 'PDF uploaded successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to upload PDF.';
                    $messageType = 'danger';
                }
            }
            
            // Upload Cover Image
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $coverFile = $_FILES['cover_image'];
                $coverExt = pathinfo($coverFile['name'], PATHINFO_EXTENSION);
                $coverName = 'magazine-cover.' . $coverExt;
                $targetPath = '../images/' . $coverName;
                
                if (move_uploaded_file($coverFile['tmp_name'], $targetPath)) {
                    $coverPath = 'images/' . $coverName;
                    $stmt = $pdo->prepare("INSERT INTO magazine_settings (setting_key, setting_value) VALUES ('cover_image', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$coverPath, $coverPath]);
                    $message = 'Cover image uploaded successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to upload cover image.';
                    $messageType = 'danger';
                }
            }
            
            // Update other settings
            if (isset($_POST['download_fee'])) {
                $stmt = $pdo->prepare("INSERT INTO magazine_settings (setting_key, setting_value) VALUES ('download_fee', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$_POST['download_fee'], $_POST['download_fee']]);
                $message = 'Settings updated successfully!';
                $messageType = 'success';
            }
            
            // Refresh settings
            $settings = [];
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM magazine_settings");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }
    }
}

// Handle request actions (approve/reject)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $requestId = intval($_GET['id']);
    $action = $_GET['action'];
    
    // Get customer details first
    $stmt = $pdo->prepare("SELECT * FROM magazine_download_requests WHERE id = ?");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($action === 'approve') {
        // Generate download token if not exists
        if (empty($request['download_token'])) {
            $token = bin2hex(random_bytes(32));
            $updateStmt = $pdo->prepare("UPDATE magazine_download_requests SET download_token = ? WHERE id = ?");
            $updateStmt->execute([$token, $requestId]);
            $request['download_token'] = $token;
        }
        
        $stmt = $pdo->prepare("UPDATE magazine_download_requests SET status = 'approved', approved_at = NOW() WHERE id = ?");
        $stmt->execute([$requestId]);
        $message = '✅ Request approved! Download link generated.';
        $messageType = 'success';
        
        // Try to send email (but don't let it break the approval)
        if ($request && !empty($request['customer_email'])) {
            try {
                $emailFile = '../backend/send-email-simple.php';
                $templateFile = '../backend/email-templates/magazine-approved.php';
                
                if (file_exists($emailFile) && file_exists($templateFile)) {
                    require_once $emailFile;
                    require_once $templateFile;
                    
                    $emailData = [
                        'name' => $request['customer_name'],
                        'email' => $request['customer_email'],
                        'request_id' => $request['id'],
                        'download_token' => $request['download_token'],
                        'fee' => $settings['download_fee'] ?? '45.00'
                    ];
                    
                    $email = getMagazineApprovedEmail($emailData);
                    $emailSent = sendEmailSMTP($request['customer_email'], $request['customer_name'], $email['subject'], $email['message'], true);
                    
                    if ($emailSent) {
                        $message .= ' 📧 Email sent to customer.';
                    } else {
                        $message .= ' ⚠️ Email not sent (check email configuration).';
                    }
                } else {
                    $message .= ' ⚠️ Email system not configured.';
                }
            } catch (Exception $e) {
                $message .= ' ⚠️ Email error, but request was approved.';
            }
        }
        
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE magazine_download_requests SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$requestId]);
        $message = '❌ Request rejected.';
        $messageType = 'warning';
    }
    
    // Refresh counts
    $pendingCount = $pdo->query("SELECT COUNT(*) FROM magazine_download_requests WHERE status = 'pending'")->fetchColumn();
    $approvedCount = $pdo->query("SELECT COUNT(*) FROM magazine_download_requests WHERE status = 'approved'")->fetchColumn();
    
    // Redirect to avoid form resubmission
    header('Location: magazine-manager.php?msg=' . urlencode($message) . '&type=' . $messageType);
    exit;
}

// Handle message from redirect
if (isset($_GET['msg'])) {
    $message = htmlspecialchars(urldecode($_GET['msg']));
    $messageType = $_GET['type'] ?? 'success';
}

// Get all requests
$requests = $pdo->query("SELECT * FROM magazine_download_requests ORDER BY requested_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get current values
$currentPdf = $settings['pdf_path'] ?? 'downloads/WineCo_Boutique_Magazine_Professional_Edition.pdf';
$currentCover = $settings['cover_image'] ?? 'images/magazine-cover.png';
$downloadFee = $settings['download_fee'] ?? '45.00';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazine Manager - Wine & Co. Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --wine: #722f37; --gold: #c9a03d; --green: #1a6b3c; }
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; }
        .sidebar { background: var(--wine); color: white; min-height: 100vh; padding: 20px; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 12px; margin: 5px 0; border-radius: 10px; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.2); }
        .card { border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border: none; margin-bottom: 20px; }
        .stat-card { background: white; border-radius: 15px; padding: 20px; text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: var(--wine); }
        .stat-number.text-warning { color: #fd7e14; }
        .stat-number.text-success { color: var(--green); }
        .btn-wine { background: var(--wine); color: white; border: none; padding: 8px 20px; border-radius: 40px; }
        .btn-wine:hover { background: #5a232a; color: white; }
        .btn-gold { background: var(--gold); color: #1a1a2e; border: none; padding: 8px 20px; border-radius: 40px; font-weight: bold; }
        .btn-gold:hover { background: #b8922f; color: #1a1a2e; }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .preview-img { max-width: 200px; max-height: 150px; border-radius: 10px; border: 1px solid #ddd; padding: 5px; background: white; }
        .upload-area { border: 2px dashed #ddd; border-radius: 15px; padding: 30px; text-align: center; cursor: pointer; transition: 0.3s; background: #fafafa; }
        .upload-area:hover { border-color: var(--wine); background: #fef5f0; }
        .upload-area.dragover { border-color: var(--wine); background: #fef5f0; }
        .token-display { font-family: monospace; font-size: 0.7rem; color: #666; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <div class="text-center mb-4">
                    <i class="fas fa-wine-bottle fa-2x"></i>
                    <h5 class="mt-2">Wine & Co.</h5>
                    <small>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></small>
                </div>
                <hr>
                <a href="dashboard.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
                <a href="dashboard.php?section=wines"><i class="fas fa-wine-glass-alt me-2"></i> Wines</a>
                <a href="dashboard.php?section=orders"><i class="fas fa-shopping-cart me-2"></i> Orders</a>
                <a href="dashboard.php?section=pairings"><i class="fas fa-cheese me-2"></i> Pairings</a>
                <a href="dashboard.php?section=subscriptions"><i class="fas fa-gem me-2"></i> Subscriptions</a>
                <a href="dashboard.php?section=corporate-gifts"><i class="fas fa-gift me-2"></i> Corporate Gifts</a>
                <a href="dashboard.php?section=gift-baskets"><i class="fas fa-basket-shopping me-2"></i> Gift Baskets</a>
                <a href="magazine-manager.php" class="active"><i class="fas fa-book me-2"></i> Magazine</a>
                <hr>
                <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>
            
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-book me-2" style="color:var(--wine);"></i>Magazine Manager</h2>
                    <a href="../view-magazine.php" target="_blank" class="btn btn-gold">
                        <i class="fas fa-eye me-2"></i>View Magazine
                    </a>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number text-warning"><?php echo $pendingCount; ?></div>
                            <div>Pending Requests</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number text-success"><?php echo $approvedCount; ?></div>
                            <div>Approved Downloads</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $totalRequests; ?></div>
                            <div>Total Requests</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-number" style="color:var(--gold);">E<?php echo number_format($downloadFee, 2); ?></div>
                            <div>Download Fee</div>
                        </div>
                    </div>
                </div>
                
                <!-- Settings -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-cog me-2" style="color:var(--wine);"></i>Settings</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update_settings">
                            
                            <div class="row">
                                <!-- PDF Upload -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">PDF Magazine File</label>
                                    <div class="upload-area" id="pdfUploadArea">
                                        <i class="fas fa-file-pdf fa-3x" style="color:var(--wine);"></i>
                                        <p><strong>Click to upload</strong> or drag and drop</p>
                                        <p class="text-muted small">Current: <?php echo basename($currentPdf); ?></p>
                                        <input type="file" id="pdfFile" name="pdf_file" accept=".pdf" style="display:none;">
                                    </div>
                                    <?php if(file_exists('../' . $currentPdf)): ?>
                                    <div class="mt-2">
                                        <a href="../<?php echo $currentPdf; ?>" target="_blank" class="btn btn-sm btn-outline-wine">
                                            <i class="fas fa-eye me-1"></i>View Current PDF
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Cover Image Upload -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Cover Image</label>
                                    <div class="upload-area" id="coverUploadArea">
                                        <i class="fas fa-image fa-3x" style="color:var(--wine);"></i>
                                        <p><strong>Click to upload</strong> or drag and drop</p>
                                        <p class="text-muted small">Current: <?php echo basename($currentCover); ?></p>
                                        <input type="file" id="coverFile" name="cover_image" accept=".jpg,.jpeg,.png,.gif" style="display:none;">
                                    </div>
                                    <?php if(file_exists('../' . $currentCover)): ?>
                                    <div class="mt-2">
                                        <img src="../<?php echo $currentCover; ?>" class="preview-img" alt="Current Cover">
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Download Fee (E)</label>
                                    <input type="number" name="download_fee" class="form-control" step="0.01" value="<?php echo $downloadFee; ?>">
                                </div>
                                <div class="col-md-8">
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <small>
                                            <i class="fas fa-info-circle me-1" style="color:var(--wine);"></i>
                                            <strong>Email:</strong> Approve a request to send download link to customer.
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-wine">
                                    <i class="fas fa-save me-2"></i>Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Download Requests -->
                <div class="card mt-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2" style="color:var(--wine);"></i>Download Requests</h5>
                        <span class="badge bg-warning text-dark"><?php echo $pendingCount; ?> Pending</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Requested</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($requests)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No download requests yet</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach($requests as $req): ?>
                                    <tr>
                                        <td>#<?php echo $req['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($req['customer_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($req['customer_email']); ?></td>
                                        <td><?php echo htmlspecialchars($req['customer_phone'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo ucfirst(str_replace('_', ' ', $req['payment_method'] ?? 'N/A')); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($req['status'] === 'pending'): ?>
                                            <span class="status-badge status-pending">⏳ Pending</span>
                                            <?php elseif($req['status'] === 'approved'): ?>
                                            <span class="status-badge status-approved">✅ Approved</span>
                                            <?php else: ?>
                                            <span class="status-badge status-rejected">❌ Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($req['requested_at'])); ?></td>
                                        <td>
                                            <?php if($req['status'] === 'pending'): ?>
                                            <a href="?action=approve&id=<?php echo $req['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Approve this download request?')">
                                                <i class="fas fa-check"></i> Approve
                                            </a>
                                            <a href="?action=reject&id=<?php echo $req['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject this download request?')">
                                                <i class="fas fa-times"></i> Reject
                                            </a>
                                            <?php elseif($req['status'] === 'approved'): ?>
                                            <span class="text-success"><i class="fas fa-check-circle"></i> Approved</span>
                                            <?php if(!empty($req['download_token'])): ?>
                                            <br>
                                            <span class="token-display">
                                                <i class="fas fa-key me-1"></i>
                                                <?php echo substr($req['download_token'], 0, 16); ?>...
                                            </span>
                                            <br>
                                            <a href="../backend/download-magazine.php?token=<?php echo $req['download_token']; ?>" target="_blank" class="btn btn-sm btn-outline-wine mt-1">
                                                <i class="fas fa-download me-1"></i>Test Link
                                            </a>
                                            <?php endif; ?>
                                            <?php else: ?>
                                            <span class="text-danger"><i class="fas fa-times-circle"></i> Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Drag and drop for PDF upload
        const pdfArea = document.getElementById('pdfUploadArea');
        const pdfInput = document.getElementById('pdfFile');
        if (pdfArea) {
            pdfArea.addEventListener('click', () => pdfInput.click());
            pdfArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                pdfArea.classList.add('dragover');
            });
            pdfArea.addEventListener('dragleave', () => {
                pdfArea.classList.remove('dragover');
            });
            pdfArea.addEventListener('drop', (e) => {
                e.preventDefault();
                pdfArea.classList.remove('dragover');
                if (e.dataTransfer.files.length > 0) {
                    pdfInput.files = e.dataTransfer.files;
                    pdfArea.innerHTML = `
                        <i class="fas fa-file-pdf fa-3x" style="color:var(--green);"></i>
                        <p><strong>${e.dataTransfer.files[0].name}</strong></p>
                        <p class="text-muted small">Click to change file</p>
                    `;
                }
            });
            pdfInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    pdfArea.innerHTML = `
                        <i class="fas fa-file-pdf fa-3x" style="color:var(--green);"></i>
                        <p><strong>${this.files[0].name}</strong></p>
                        <p class="text-muted small">Click to change file</p>
                    `;
                }
            });
        }
        
        // Drag and drop for cover upload
        const coverArea = document.getElementById('coverUploadArea');
        const coverInput = document.getElementById('coverFile');
        if (coverArea) {
            coverArea.addEventListener('click', () => coverInput.click());
            coverArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                coverArea.classList.add('dragover');
            });
            coverArea.addEventListener('dragleave', () => {
                coverArea.classList.remove('dragover');
            });
            coverArea.addEventListener('drop', (e) => {
                e.preventDefault();
                coverArea.classList.remove('dragover');
                if (e.dataTransfer.files.length > 0) {
                    coverInput.files = e.dataTransfer.files;
                    const file = e.dataTransfer.files[0];
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        coverArea.innerHTML = `
                            <img src="${ev.target.result}" style="max-width:150px; max-height:100px; border-radius:10px; margin-bottom:10px;">
                            <p><strong>${file.name}</strong></p>
                            <p class="text-muted small">Click to change image</p>
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            });
            coverInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        coverArea.innerHTML = `
                            <img src="${ev.target.result}" style="max-width:150px; max-height:100px; border-radius:10px; margin-bottom:10px;">
                            <p><strong>${file.name}</strong></p>
                            <p class="text-muted small">Click to change image</p>
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>