<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Welcome';

ob_start();
?>
<p>Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! You are logged in.</p>
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
</head>
<body>
    <?php 
$GLOBALS['pdo'] = $pdo;
require __DIR__ . '/includes/layout.php'; ?>
</body>
</html>
