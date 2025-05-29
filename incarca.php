<?php
include 'config.php';

// HTML drag-and-drop form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Upload PDF Files</title>
        <style>
            #drop-area {
                border: 2px dashed #ccc;
                border-radius: 20px;
                width: 100%;
                max-width: 600px;
                margin: 50px auto;
                padding: 20px;
                text-align: center;
            }
            #fileElem { display: none; }
            .file-list { margin-top: 20px; }
            .success { color: green; }
            .error { color: red; }
        </style>
    </head>
    <body>
        <div id="drop-area">
            <form class="my-form" method="post" enctype="multipart/form-data">
                <p>Drag and drop up to 1000 PDF files here or <label for="fileElem" style="color:blue;cursor:pointer;">browse</label></p>
                <input type="file" id="fileElem" name="files[]" multiple accept="application/pdf">
                <input type="submit" value="Upload">
            </form>
            <div class="file-list" id="fileList"></div>
        </div>
        <script>
            const dropArea = document.getElementById('drop-area');
            const fileElem = document.getElementById('fileElem');
            dropArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropArea.style.background = '#f0f0f0';
            });
            dropArea.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropArea.style.background = '';
            });
            dropArea.addEventListener('drop', (e) => {
                e.preventDefault();
                fileElem.files = e.dataTransfer.files;
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Handle file uploads
$results = array();
$fixed_day = '01'; // Set your fixed day here
$current_month = date('m');
$current_year = date('Y');
$upload_dir = __DIR__ . "/$current_year/$current_month/";

if (!isset($_FILES['files'])) {
    echo 'No files uploaded.';
    exit;
}

foreach ($_FILES['files']['tmp_name'] as $idx => $tmp_name) {
    $original_name = $_FILES['files']['name'][$idx];
    $error = $_FILES['files']['error'][$idx];
    if ($error !== UPLOAD_ERR_OK) {
        $results[] = array(
            'file' => $original_name,
            'status' => 'error',
            'message' => 'Upload error code: ' . $error
        );
        continue;
    }
    // Validate filename and extract CNP
    if (!preg_match('/^(\d{13})_\d{2}_' . $current_month . '_' . $current_year . '\.pdf$/', $original_name, $matches)) {
        $results[] = array(
            'file' => $original_name,
            'status' => 'error',
            'message' => 'Invalid filename format.'
        );
        continue;
    }
    $cnp = $matches[1];
    $new_filename = $cnp . '.pdf'; // Keep only CNP and extension

    // Ensure upload directory exists
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $results[] = array(
                'file' => $original_name,
                'status' => 'error',
                'message' => 'Failed to create upload directory.'
            );
            continue;
        }
    }
    $target_path = $upload_dir . $new_filename;
    // Move and overwrite if exists
    if (!move_uploaded_file($tmp_name, $target_path)) {
        $results[] = array(
            'file' => $original_name,
            'status' => 'error',
            'message' => 'Failed to move uploaded file.'
        );
        continue;
    }
    // Build download URL (relative to web root)
    $relative_url = "$current_year/$current_month/$new_filename";
    // Insert into database (procedural)
    $sql = "INSERT INTO fluturasi (CNP_utilizator, data_incarcare, Fisier) VALUES ('$cnp', CURDATE(), '$relative_url')
            ON DUPLICATE KEY UPDATE data_incarcare=CURDATE(), Fisier='$relative_url'";
    if (mysqli_query($conn, $sql)) {
        $results[] = array(
            'file' => $original_name,
            'status' => 'success',
            'message' => 'Uploaded successfully.'
        );
    } else {
        $results[] = array(
            'file' => $original_name,
            'status' => 'error',
            'message' => 'DB error: ' . mysqli_error($conn)
        );
    }
}
// Show results
foreach ($results as $res) {
    $class = $res['status'] === 'success' ? 'success' : 'error';
    echo "<div class='$class'>{$res['file']}: {$res['message']}</div>";
}
?>