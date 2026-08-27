<?php
// Simple script to generate password hashes
$passwords = [
    'admin' => 'Admin123!',
    'manager' => 'Manager123!',
    'staff' => 'Staff123!'
];

echo "<h3>🔑 Password Hashes</h3>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr style='background: #722f37; color: white;'><th>Role</th><th>Email</th><th>Password</th><th>Hash (Copy this)</th></tr>";

foreach ($passwords as $role => $pass) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $email = $role . '@wineco.co.sz';
    
    echo "<tr>";
    echo "<td><strong>" . ucfirst($role) . "</strong></td>";
    echo "<td>$email</td>";
    echo "<td><code>$pass</code></td>";
    echo "<td><code style='background: #f0f0f0; padding: 5px; display: block; word-break: break-all;'>$hash</code></td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><hr><br>";

echo "<h4>📝 SQL Update Statements (Copy and run in phpMyAdmin):</h4>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px; overflow: auto;'>";

foreach ($passwords as $role => $pass) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $email = $role . '@wineco.co.sz';
    
    echo "UPDATE staff SET password = '$hash' WHERE email = '$email';\n";
}

echo "</pre>";

echo "<br><p style='color: green; font-weight: bold;'>✅ Copy the UPDATE statements above and run them in phpMyAdmin.</p>";
?>