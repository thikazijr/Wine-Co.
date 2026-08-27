<?php
session_start();

// Only allow admin to access this
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/index.php');
    exit;
}

// Only admin can reset passwords
if ($_SESSION['admin_role'] !== 'admin') {
    die('Access denied. Only administrators can reset passwords.');
}

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all staff
    $staff = $pdo->query("SELECT id, name, email, role FROM staff ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    
    // Handle password reset
    if (isset($_POST['reset'])) {
        $staffId = $_POST['staff_id'] ?? 0;
        $newPassword = $_POST['new_password'] ?? '';
        
        if ($staffId && $newPassword && strlen($newPassword) >= 6) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE staff SET password = ? WHERE id = ?");
            $stmt->execute([$hash, $staffId]);
            $message = "✅ Password reset successfully!";
        } else {
            $message = "❌ Please enter a valid password (minimum 6 characters)";
        }
    }
    
} catch(PDOException $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Staff Passwords - Wine & Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5ede6; font-family: 'Segoe UI', system-ui; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
        .btn-wine { background: #722f37; color: white; border-radius: 40px; border: none; padding: 8px 20px; }
        .btn-wine:hover { background: #5a232a; color: white; }
        .role-admin { background: #dc3545; color: white; padding: 2px 12px; border-radius: 12px; font-size: 0.75rem; }
        .role-manager { background: #ffc107; color: #333; padding: 2px 12px; border-radius: 12px; font-size: 0.75rem; }
        .role-staff { background: #6c757d; color: white; padding: 2px 12px; border-radius: 12px; font-size: 0.75rem; }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-key me-2"></i>Reset Staff Passwords</h2>
        <p class="text-muted">Only administrators can reset passwords. This tool is secure and logged.</p>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="table-responsive mt-4">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staff as $member): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($member['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                        <td>
                            <span class="<?php echo 'role-' . $member['role']; ?>">
                                <?php echo strtoupper($member['role']); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="" class="d-flex gap-2">
                                <input type="hidden" name="staff_id" value="<?php echo $member['id']; ?>">
                                <input type="text" name="new_password" class="form-control form-control-sm" style="width: 150px;" placeholder="New password" required>
                                <button type="submit" name="reset" class="btn btn-sm btn-wine" onclick="return confirm('Reset password for <?php echo htmlspecialchars($member['name']); ?>?')">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Tip:</strong> Passwords must be at least 6 characters long.
            <br>
            <small>Default passwords: Admin123!, Manager123!, Staff123!</small>
        </div>
        
        <a href="../admin/dashboard.php?section=staff" class="btn btn-outline-secondary mt-3">
            <i class="fas fa-arrow-left me-2"></i>Back to Staff Management
        </a>
    </div>
</body>
</html>