<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

session_start();

$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create invoices table if not exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS invoices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            invoice_number VARCHAR(50) UNIQUE NOT NULL,
            invoice_date DATE NOT NULL,
            type ENUM('sales', 'purchase', 'receipt', 'payment') NOT NULL,
            party_name VARCHAR(100) NOT NULL,
            amount DECIMAL(15,2) NOT NULL,
            tax DECIMAL(15,2) DEFAULT 0,
            total DECIMAL(15,2) NOT NULL,
            description TEXT,
            posted BOOLEAN DEFAULT 0,
            journal_entry_id INT DEFAULT NULL,
            created_by INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    
    $action = $_GET['action'] ?? '';
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($action === 'create') {
        $invoiceData = $data['data'];
        
        $amount = floatval($invoiceData['amount']);
        $tax = floatval($invoiceData['tax']);
        $total = $amount + ($amount * $tax / 100);
        
        $stmt = $pdo->prepare("INSERT INTO invoices (invoice_number, invoice_date, type, party_name, amount, tax, total, description, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $invoiceData['invoice_number'],
            $invoiceData['invoice_date'],
            $invoiceData['type'],
            $invoiceData['party_name'],
            $amount,
            $tax,
            $total,
            $invoiceData['description'],
            $_SESSION['staff_id'] ?? 1
        ]);
        $invoiceId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'invoice_id' => $invoiceId,
            'total' => $total
        ]);
        
    } elseif ($action === 'post') {
        $invoiceId = intval($data['invoice_id'] ?? 0);
        if (!$invoiceId) {
            echo json_encode(['error' => 'Invoice ID required']);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? AND posted = 0");
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$invoice) {
            echo json_encode(['error' => 'Invoice not found or already posted']);
            exit;
        }
        
        $pdo->beginTransaction();
        
        try {
            $entryDate = $invoice['invoice_date'];
            $description = $invoice['description'] ?: ucfirst($invoice['type']) . ' Invoice ' . $invoice['invoice_number'];
            $party = $invoice['party_name'];
            $amount = floatval($invoice['amount']);
            $tax = floatval($invoice['tax']);
            $total = floatval($invoice['total']);
            
            $lines = [];
            
            switch($invoice['type']) {
                case 'sales':
                    $lines = [
                        ['account_id' => getAccountId($pdo, '1020'), 'debit' => $total, 'credit' => 0, 'desc' => "Invoice to $party"],
                        ['account_id' => getAccountId($pdo, '4000'), 'debit' => 0, 'credit' => $amount, 'desc' => "Sales invoice $description"],
                        ['account_id' => getAccountId($pdo, '2100'), 'debit' => 0, 'credit' => ($amount * $tax / 100), 'desc' => "VAT on sales"]
                    ];
                    break;
                    
                case 'purchase':
                    $lines = [
                        ['account_id' => getAccountId($pdo, '5000'), 'debit' => $amount, 'credit' => 0, 'desc' => "Purchase from $party"],
                        ['account_id' => getAccountId($pdo, '2000'), 'debit' => 0, 'credit' => $total, 'desc' => "Invoice from $party"],
                        ['account_id' => getAccountId($pdo, '2100'), 'debit' => ($amount * $tax / 100), 'credit' => 0, 'desc' => "VAT on purchase"]
                    ];
                    break;
                    
                case 'receipt':
                    $lines = [
                        ['account_id' => getAccountId($pdo, '1000'), 'debit' => $total, 'credit' => 0, 'desc' => "Payment from $party"],
                        ['account_id' => getAccountId($pdo, '1020'), 'debit' => 0, 'credit' => $total, 'desc' => "Receipt from $party"]
                    ];
                    break;
                    
                case 'payment':
                    $lines = [
                        ['account_id' => getAccountId($pdo, '2000'), 'debit' => $total, 'credit' => 0, 'desc' => "Payment to $party"],
                        ['account_id' => getAccountId($pdo, '1000'), 'debit' => 0, 'credit' => $total, 'desc' => "Payment to $party"]
                    ];
                    break;
                    
                default:
                    throw new Exception('Invalid invoice type');
            }
            
            // Create journal entry
            $stmt = $pdo->prepare("INSERT INTO journal_entries (entry_date, reference, description, created_by) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $entryDate,
                $invoice['invoice_number'],
                $description,
                $_SESSION['staff_id'] ?? 1
            ]);
            $entryId = $pdo->lastInsertId();
            
            // Insert journal lines
            foreach ($lines as $line) {
                $stmt = $pdo->prepare("INSERT INTO journal_entry_lines (entry_id, account_id, debit, credit, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $entryId,
                    $line['account_id'],
                    $line['debit'],
                    $line['credit'],
                    $line['desc']
                ]);
            }
            
            // Update invoice
            $stmt = $pdo->prepare("UPDATE invoices SET posted = 1, journal_entry_id = ? WHERE id = ?");
            $stmt->execute([$entryId, $invoiceId]);
            
            // Post the journal entry
            $stmt = $pdo->prepare("UPDATE journal_entries SET is_posted = 1, posted_at = NOW() WHERE id = ?");
            $stmt->execute([$entryId]);
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'journal_entry_id' => $entryId,
                'message' => 'Invoice posted and journal entry created'
            ]);
            
        } catch(Exception $e) {
            $pdo->rollBack();
            echo json_encode(['error' => $e->getMessage()]);
        }
        
    } elseif ($action === 'get') {
        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($invoice && $invoice['journal_entry_id']) {
            $stmt = $pdo->prepare("
                SELECT jel.*, coa.account_code, coa.account_name
                FROM journal_entry_lines jel
                JOIN chart_of_accounts coa ON jel.account_id = coa.id
                WHERE jel.entry_id = ?
            ");
            $stmt->execute([$invoice['journal_entry_id']]);
            $invoice['journal_lines'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode($invoice);
        
    } else {
        echo json_encode(['error' => 'Invalid action']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function getAccountId($pdo, $accountCode) {
    $stmt = $pdo->prepare("SELECT id FROM chart_of_accounts WHERE account_code = ?");
    $stmt->execute([$accountCode]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['id'] ?? 0;
}
?>