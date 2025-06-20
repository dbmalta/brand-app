<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';
require_permission($pdo, 'manage_clients');

$pageTitle = 'Clients';

// Handle search
$search = trim($_GET['search'] ?? '');
$country = trim($_GET['country'] ?? '');

$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(client_name LIKE ? OR contact_first_name LIKE ? OR contact_last_name LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($country !== '') {
    $where[] = 'country = ?';
    $params[] = $country;
}

$sql = "SELECT * FROM mktg_clients";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY client_name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clients = $stmt->fetchAll();

// Dashboard counts
$totalClients = $pdo->query("SELECT COUNT(*) FROM mktg_clients")->fetchColumn();
$totalFiles = $pdo->query("SELECT COUNT(*) FROM mktg_client_files")->fetchColumn();

ob_start();
?>

<!-- Dashboard Widgets -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary shadow-sm p-3">
            <h6>Total Clients</h6>
            <div class="fs-3"><?= $totalClients ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success shadow-sm p-3">
            <h6>Total Branding Files</h6>
            <div class="fs-3"><?= $totalFiles ?></div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search by name or contact">
    </div>
    <div class="col-md-4">
        <input type="text" name="country" value="<?= htmlspecialchars($country) ?>" class="form-control" placeholder="Filter by country">
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-outline-primary">🔍 Filter</button>
        <a href="clients.php" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<!-- Add Button -->
<div class="mb-2">
    <a href="client_form.php" class="btn btn-success">➕ Add New Client</a>
</div>

<!-- Client Table -->
<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>Name</th>
            <th>Contact</th>
            <th>Country</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($clients): ?>
        <?php foreach ($clients as $client): ?>
            <tr>
                <td><?= htmlspecialchars($client['client_name']) ?></td>
                <td>
                    <?= htmlspecialchars($client['contact_first_name'] . ' ' . $client['contact_last_name']) ?><br>
                    <?= htmlspecialchars($client['contact_email']) ?><br>
                    <?= htmlspecialchars($client['contact_phone']) ?>
                </td>
                <td><?= htmlspecialchars($client['country']) ?></td>
                <td>
                    <a href="client_form.php?id=<?= $client['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="client_upload.php?client_id=<?= $client['id'] ?>" class="btn btn-sm btn-primary">Assets</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4">No clients found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<?php $content = ob_get_clean(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clients - BitKode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/dark-mode.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/includes/layout.php'; ?>


    
</body>
</html>
