<?php
session_start();
require __DIR__ . '/../config/db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = isset($_GET['id']) ? 'Edit User' : 'Add User';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$errors = [];

$username = '';
$permissions = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $permissions = $_POST['permissions'] ?? [];

    if ($username === '') {
        $errors[] = 'Username is required.';
    }

    if (!$id && $password === '') {
        $errors[] = 'Password is required for new users.';
    }

    if (empty($errors)) {
        if ($id) {
            // Update user
            if ($password !== '') {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE mktg_users SET username = ?, password = ? WHERE id = ?");
                $stmt->execute([$username, $hashed, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE mktg_users SET username = ? WHERE id = ?");
                $stmt->execute([$username, $id]);
            }

            // Clear and update permissions
            $pdo->prepare("DELETE FROM mktg_user_permissions WHERE user_id = ?")->execute([$id]);
        } else {
            // Create user
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO mktg_users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed]);
            $id = $pdo->lastInsertId();
        }

        // Assign permissions
        foreach ($permissions as $permId) {
            $pdo->prepare("INSERT INTO mktg_user_permissions (user_id, permission_id) VALUES (?, ?)")
                ->execute([$id, $permId]);
        }

        header('Location: users.php');
        exit;
    }
} elseif ($id) {
    // Load user for editing
    $stmt = $pdo->prepare("SELECT * FROM mktg_users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user) {
        $username = $user['username'];
        $stmt = $pdo->prepare("SELECT permission_id FROM mktg_user_permissions WHERE user_id = ?");
        $stmt->execute([$id]);
        $permissions = array_column($stmt->fetchAll(), 'permission_id');
    } else {
        die('User not found.');
    }
}

// Load all available permissions
$allPermissions = $pdo->query("SELECT id, name FROM mktg_permissions ORDER BY name ASC")->fetchAll();

ob_start();
?>

<div class="mb-4">
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($username) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label"><?= $id ? 'Change Password (optional)' : 'Password' ?></label>
            <input type="password" name="password" class="form-control" <?= $id ? '' : 'required' ?>>
        </div>

        <div class="mb-3">
            <label class="form-label">Permissions</label>
            <?php foreach ($allPermissions as $perm): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>"
                        <?= in_array($perm['id'], $permissions) ? 'checked' : '' ?>>
                    <label class="form-check-label"><?= htmlspecialchars($perm['name']) ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary"><?= $id ? 'Update' : 'Create' ?> User</button>
        <a href="users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php $content = ob_get_clean(); ?>

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
