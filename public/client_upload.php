<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';
require_permission($pdo, 'manage_clients');

$clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;
if (!$clientId) die("Missing client ID");

// Verify client exists
$stmt = $pdo->prepare("SELECT * FROM mktg_clients WHERE id = ?");
$stmt->execute([$clientId]);
$client = $stmt->fetch();
if (!$client) die("Client not found.");

// ✅ Corrected upload path
$uploadDir = __DIR__ . "/../public/uploads/clients/$clientId";
$publicPath = "uploads/clients/$clientId";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

// Handle file delete
if (isset($_GET['delete'])) {
    $fileId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT * FROM mktg_client_files WHERE id = ? AND client_id = ?");
    $stmt->execute([$fileId, $clientId]);
    $file = $stmt->fetch();

    if ($file) {
        $fullPath = __DIR__ . '/../public/' . $file['filepath'];
        if (file_exists($fullPath)) unlink($fullPath);

        $pdo->prepare("DELETE FROM mktg_client_files WHERE id = ?")->execute([$fileId]);
        header("Location: client_upload.php?client_id=$clientId");
        exit;
    }
}

// Handle file upload
$uploadError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/gif'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    $file = $_FILES['file'];

    if ($file['error'] === 0) {
        $mime = mime_content_type($file['tmp_name']);
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($mime, $allowedTypes) && in_array($fileExtension, $allowedExtensions)) {
            $cleanName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', basename($file['name']));
            $filename = uniqid() . '_' . $cleanName;
            $fullSavePath = "$uploadDir/$filename";

            $moveSuccess = move_uploaded_file($file['tmp_name'], $fullSavePath);

            if ($moveSuccess) {
                $stmt = $pdo->prepare("INSERT INTO mktg_client_files (client_id, filename, filepath, type) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $clientId,
                    $file['name'],
                    "$publicPath/$filename",
                    'branding'
                ]);

                // ✅ Prevent form resubmission on refresh
                header("Location: client_upload.php?client_id=$clientId&uploaded=1");
                exit;
            } else {
                $uploadError = 'Could not move uploaded file. Check permissions and path.';
            }
        } else {
            $uploadError = 'Unsupported file type or extension.';
        }
    } else {
        $uploadError = 'Upload error.';
    }
}

// Fetch all files for client
$stmt = $pdo->prepare("SELECT * FROM mktg_client_files WHERE client_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$clientId]);
$files = $stmt->fetchAll();

$pageTitle = "Upload Branding Assets";

ob_start();
?>

<div class="mb-3">
    <a href="clients.php" class="btn btn-secondary">⬅ Back to Clients</a>
</div>

<h5>Client: <?= htmlspecialchars($client['client_name']) ?></h5>

<?php if (isset($_GET['uploaded'])): ?>
    <div class="alert alert-success">✅ File uploaded successfully.</div>
<?php endif; ?>

<?php if ($uploadError): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($uploadError) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="mb-4">
    <label class="form-label">Upload Image</label>
    <input type="file" name="file" class="form-control mb-2" required>
    <button type="submit" class="btn btn-primary">Upload</button>
</form>

<?php if ($files): ?>
    <div class="row g-3">
        <?php foreach ($files as $f): ?>
            <?php
                $fileUrl = '/' . htmlspecialchars($f['filepath']);
                $ext = strtolower(pathinfo($f['filename'], PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
            ?>
            <div class="col-md-3 text-center">
                <div class="card p-2">
                    <?php if ($isImage): ?>
                        <img src="<?= $fileUrl ?>" class="img-fluid mb-2" alt="<?= htmlspecialchars($f['filename']) ?>">
                    <?php else: ?>
                        <div class="bg-secondary text-white p-3 mb-2 rounded">
                            <strong><?= strtoupper($ext) ?></strong>
                            <div class="small">[Non-image file]</div>
                        </div>
                    <?php endif; ?>
                    <div class="text-truncate"><?= htmlspecialchars($f['filename']) ?></div>
                    <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">View</a>
                    <a href="client_upload.php?client_id=<?= $clientId ?>&delete=<?= $f['id'] ?>" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Delete this file?')">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No files uploaded yet.</p>
<?php endif; ?>

<?php $content = ob_get_clean(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?> - BitKode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/dark-mode.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/includes/layout.php'; ?>



</body>
</html>
