<?php
session_start();

// Only allow if logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/index.php');
    exit;
}

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) { die("Connection failed"); }

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    
    if (!empty($email) && !empty($newPassword) && strlen($newPassword) >= 6) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE staff SET password = ? WHERE email = ?");
        $stmt->execute([$hashed, $email]);
        $msg = "Password updated successfully for $email";
    } else {
        $msg = "Please fill all fields and use at least 6 characters";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password - Wine & Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card" style="max-width:500px; margin:auto;">
            <div class="card-header bg-wine text-white">
                <h5>Update Staff Password</h5>
            </div>
            <div class="card-body">
                <?php if($msg): ?>
                    <div class="alert alert-info"><?php echo $msg; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>New Password (min 6 chars)</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-wine w-100">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>