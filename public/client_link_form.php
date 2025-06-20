<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';
require_permission($pdo, 'manage_clients');

$clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;
$linkId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$clientId) die("Missing client ID");

// Load client name
$stmt = $pdo->prepare("SELECT * FROM mktg_clients WHERE id = ?");
$stmt->execute([$clientId]);
$client = $stmt->fetch();
if (!$client) die("Client not found.");

$pageTitle = $linkId ? 'Edit Link' : 'Add Link';
$errors = [];

$data = [
    'type' => '',
    'label' => '',
    'url' => '',
    'username' => '',
    'password' => '',
    'notes' => ''
];

// Load existing link
if ($linkId && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM mktg_client_links WHERE id = ? AND client_id = ?");
    $stmt->execute([$linkId, $clientId]);
    $existing = $stmt->fetch();
    if (!$existing) die("Link not found.");
    $data = array_merge($data, $existing);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($data) as $field) {
        $data[$field] = trim($_POST[$field] ?? '');
    }

    if (!$data['type']) $errors[] = "Link type is required.";
    if (!$data['label']) $errors[] = "Label is required.";
    if (!$data['url']) $errors[] = "URL is required.";

    if (empty($errors)) {
        if ($linkId) {
            // Update
            $stmt = $pdo->prepare("UPDATE mktg_client_links SET type=?, label=?, url=?, username=?, password=?, notes=?, updated_at=NOW() WHERE id=? AND client_id=?");
            $stmt->execute([
                $data['type'], $data['label'], $data['url'],
                $data['username'], $data['password'], $data['notes'],
                $linkId, $clientId
            ]);
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO mktg_client_links (client_id, type, label, url, username, password, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $clientId, $data['type'], $data['label'], $data['url'],
                $data['username'], $data['password'], $data['notes']
            ]);
        }

        header("Location: client_links.php?client_id=$clientId");
        exit;
    }
}

ob_start();
?>

<div class="mb-3">
    <a href="client_links.php?client_id=<?= $clientId ?>" class="btn btn-secondary">⬅ Back to Links</a>
</div>

<h5>Client: <?= htmlspecialchars($client['client_name']) ?></h5>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <?= implode("<br>", array_map('htmlspecialchars', $errors)) ?>
    </div>
<?php endif; ?>

<form method="POST" class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Type</label>
        <select name="type" class="form-select" required>
            <option value="">Choose...</option>
            <?php
            $types = ['website', 'facebook', 'instagram', 'linkedin', 'twitter', 'tiktok', 'other'];
            foreach ($types as $type) {
                $selected = ($data['type'] === $type) ? 'selected' : '';
                echo "<option value=\"$type\" $selected>" . ucfirst($type) . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Label</label>
        <input type="text" name="label" class="form-control" required value="<?= htmlspecialchars($data['label']) ?>">
    </div>

    <div class="col-12">
        <label class="form-label">URL</label>
        <input type="url" name="url" class="form-control" required value="<?= htmlspecialchars($data['url']) ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Username (optional)</label>
        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']) ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Password / Token (optional)</label>
        <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($data['password']) ?>">
    </div>

    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control"><?= htmlspecialchars($data['notes']) ?></textarea>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary"><?= $linkId ? 'Update' : 'Create' ?> Link</button>
        <a href="client_links.php?client_id=<?= $clientId ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
