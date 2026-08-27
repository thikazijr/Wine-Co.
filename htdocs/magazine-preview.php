<?php
// magazine-preview.php
$host = 'sql306.infinityfree.com';
$dbname = 'if0_42164424_if0_42164424_wineco';
$username = 'if0_42164424';
$password = 'aZ8j5lRv2DjU2';

$pdfPath = 'downloads/WineCo_Boutique_Magazine_Professional_Edition.pdf';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT setting_value FROM magazine_settings WHERE setting_key = 'pdf_path'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $pdfPath = $result['setting_value'];
    }
} catch(PDOException $e) {}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazine Preview</title>
    <style>
        body { margin: 0; padding: 0; background: #f8f4f0; }
        .pdf-container { width: 100%; height: 100vh; display: flex; justify-content: center; align-items: center; background: #f8f4f0; }
        .pdf-container embed { width: 100%; height: 100%; border: none; background: white; }
        .fallback { display: none; text-align: center; padding: 40px; }
        .fallback i { font-size: 4rem; color: #c9a03d; margin-bottom: 20px; }
        .fallback a { color: #722f37; font-weight: bold; }
    </style>
</head>
<body>
    <div class="pdf-container">
        <embed src="<?php echo $pdfPath; ?>#toolbar=1&navpanes=1&scrollbar=1" 
               type="application/pdf"
               width="100%" 
               height="100%">
        <div class="fallback" id="fallback">
            <i class="fas fa-file-pdf"></i>
            <h4>PDF Viewer Not Available</h4>
            <p>Your browser doesn't support PDF viewing.</p>
            <p>You can still <a href="<?php echo $pdfPath; ?>" target="_blank">download the PDF</a> to view it.</p>
        </div>
    </div>
    <script>
        document.querySelector('embed').onerror = function() {
            document.querySelector('.fallback').style.display = 'block';
            this.style.display = 'none';
        };
    </script>
</body>
</html>