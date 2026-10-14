<?php
// Test upload sederhana
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    if (isset($_FILES['test']) && $_FILES['test']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['test'];
        echo "File: {$file['name']}<br>";
        echo "Type: {$file['type']}<br>";
        echo "Tmp: {$file['tmp_name']}<br>";
        echo "Size: {$file['size']}<br>";
        echo "Error: {$file['error']}<br>";
        
        // Test mime_content_type
        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($file['tmp_name']);
            echo "MIME detected: $mime<br>";
        } else {
            echo "mime_content_type NOT available<br>";
        }
        
        $uploadDir = __DIR__ . '/uploads/siswa/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
            echo "Created dir: $uploadDir<br>";
        }
        
        $dest = $uploadDir . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            echo "✅ Upload SUCCESS to: $dest<br>";
        } else {
            echo "❌ Upload FAILED<br>";
            echo "Error: " . error_get_last()['message'] . "<br>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<body>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test">
    <button type="submit">Test Upload</button>
</form>
</body>
</html>