<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/csrf.php';
require __DIR__ . '/includes/auth.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Require permission "manage_config"
require_permission($pdo, 'manage_config');


$pageTitle = 'User Permissions';

$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');

        if ($name === '') $errors[] = "Permission name is required";

    if (empty($errors)) {
        if ($action === 'edit' && $id) {
            $stmt = $pdo->prepare("UPDATE mktg_permissions SET name = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $desc, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO mktg_permissions (name, description) VALUES (?, ?)");
            $stmt->execute([$name, $desc]);
        }
            header("Location: config.php");
            exit;
        }
    }
}

if ($action === 'delete' && $id) {
    $pdo->prepare("DELETE FROM mktg_permissions WHERE id = ?")->execute([$id]);
    header("Location: config.php");
    exit;
}

$editing = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM mktg_permissions WHERE id = ?");
    $stmt->execute([$id]);
    $editing = $stmt->fetch();
}

$rows = $pdo->query("SELECT * FROM mktg_permissions ORDER BY name ASC")->fetchAll();

ob_start();
?>

<div class="mb-4">
    <h5><?= $editing ? "Edit Permission" : "Add Permission" ?></h5>
    <?php if ($errors): ?>
        <div class="alert alert-danger"><?= implode("<br>", array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
        <div class="mb-2">
            <input type="text" name="name" class="form-control" placeholder="Permission name" value="<?= htmlspecialchars($editing['name'] ?? '') ?>" required>
        </div>
        <div class="mb-2">
            <textarea name="description" class="form-control" placeholder="Description"><?= htmlspecialchars($editing['description'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-success"><?= $editing ? "Update" : "Add" ?></button>
        <?php if ($editing): ?>
            <a href="config.php" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width:30%;">Name</th>
            <th>Description</th>
            <th style="width:15%;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td>
                <a href="config.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="config.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this permission?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?> - BitKode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/dark-mode.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/includes/layout.php'; ?>
</body>
</html>
