<?php
session_start();

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Database connection
$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle login
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        // Check in staff table by EMAIL
        $stmt = $pdo->prepare("SELECT * FROM staff WHERE email = ?");
        $stmt->execute([$email]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($staff) {
            // Verify password
            if (password_verify($password, $staff['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_name'] = $staff['name'];
                $_SESSION['admin_email'] = $staff['email'];
                $_SESSION['admin_role'] = $staff['role'];
                $_SESSION['staff_id'] = $staff['id'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Wine & Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #f5ede6 0%, #e8ddd4 100%);
            font-family: 'Segoe UI', system-ui; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container { 
            max-width: 420px; 
            width: 100%;
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.1); 
        }
        .login-container .logo {
            font-size: 2.5rem;
            color: #722f37;
        }
        .btn-wine { 
            background: #722f37; 
            color: white; 
            border: none; 
            padding: 12px 20px; 
            border-radius: 40px; 
            width: 100%; 
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-wine:hover { 
            background: #5a232a; 
            color: white;
            transform: translateY(-2px);
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e8e0d8;
        }
        .form-control:focus {
            border-color: #722f37;
            box-shadow: 0 0 0 3px rgba(114,47,55,0.1);
        }
        .role-badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .role-admin { background: #dc3545; color: white; }
        .role-manager { background: #ffc107; color: #333; }
        .role-staff { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="text-center mb-4">
                <i class="fas fa-wine-bottle logo"></i>
                <h3 class="mt-2 fw-bold" style="color: #2c1a1a;">Wine & Co.</h3>
                <p class="text-muted small">Management Portal</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="admin@wineco.co.sz" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-wine">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-lock me-1"></i> Secure admin access only
                </small>
                <br>
                <small class="text-muted">
                    <span class="role-badge role-admin">Admin</span>
                    <span class="role-badge role-manager">Manager</span>
                    <span class="role-badge role-staff">Staff</span>
                </small>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>