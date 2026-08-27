<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $categoryData = getCategoryFromScenario($data['scenario']);
    
    if (!empty($data['id'])) {
        $stmt = $pdo->prepare("UPDATE accounting_transactions SET 
            type = ?, scenario = ?, category = ?, category_color = ?, 
            category_text_color = ?, amount = ?, description = ?, 
            reference = ?, payment_method = ?, transaction_date = ?,
            status = 'posted', auto_classified = 1
            WHERE id = ?");
        $stmt->execute([
            $data['type'],
            $data['scenario'],
            $categoryData['category'],
            $categoryData['color'],
            $categoryData['textColor'],
            $data['amount'],
            $data['description'] ?? '',
            $data['reference'] ?? '',
            $data['payment_method'] ?? 'cash',
            $data['transaction_date'] ?? date('Y-m-d'),
            $data['id']
        ]);
        $id = $data['id'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO accounting_transactions 
            (type, scenario, category, category_color, category_text_color, 
             amount, description, reference, payment_method, transaction_date, 
             status, auto_classified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'posted', 1)");
        $stmt->execute([
            $data['type'],
            $data['scenario'],
            $categoryData['category'],
            $categoryData['color'],
            $categoryData['textColor'],
            $data['amount'],
            $data['description'] ?? '',
            $data['reference'] ?? '',
            $data['payment_method'] ?? 'cash',
            $data['transaction_date'] ?? date('Y-m-d')
        ]);
        $id = $pdo->lastInsertId();
    }
    
    echo json_encode(['success' => true, 'id' => $id]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function getCategoryFromScenario($scenario) {
    $categories = [
        'invoice_paid' => ['category' => 'Accounts Receivable', 'color' => '#d4edda', 'textColor' => '#155724'],
        'cash_sale' => ['category' => 'Sales Revenue', 'color' => '#d4edda', 'textColor' => '#155724'],
        'subscription_paid' => ['category' => 'Subscription Revenue', 'color' => '#cce5ff', 'textColor' => '#004085'],
        'gift_sale' => ['category' => 'Gift Revenue', 'color' => '#fff3cd', 'textColor' => '#856404'],
        'other_income' => ['category' => 'Other Income', 'color' => '#d6d8db', 'textColor' => '#383d41'],
        'supplier_invoice' => ['category' => 'Accounts Payable', 'color' => '#f8d7da', 'textColor' => '#721c24'],
        'staff_payment' => ['category' => 'Salaries & Wages', 'color' => '#f8d7da', 'textColor' => '#721c24'],
        'operating_expense' => ['category' => 'Operating Expenses', 'color' => '#f8d7da', 'textColor' => '#721c24'],
        'inventory_purchase' => ['category' => 'Inventory Purchases', 'color' => '#f8d7da', 'textColor' => '#721c24'],
        'delivery_cost' => ['category' => 'Delivery & Shipping', 'color' => '#f8d7da', 'textColor' => '#721c24'],
        'other_expense' => ['category' => 'Other Expenses', 'color' => '#f8d7da', 'textColor' => '#721c24']
    ];
    
    return $categories[$scenario] ?? ['category' => 'Uncategorized', 'color' => '#e0e0e0', 'textColor' => '#333'];
}
?>