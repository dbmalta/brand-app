<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';

$pageTitle = 'Dashboard';

// Load counts
$totalClients = $pdo->query("SELECT COUNT(*) FROM mktg_clients")->fetchColumn();
$totalFiles = $pdo->query("SELECT COUNT(*) FROM mktg_client_files")->fetchColumn();
$filesThisWeek = $pdo->query("SELECT COUNT(*) FROM mktg_client_files WHERE uploaded_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
$username = $_SESSION['username'] ?? 'Guest';

// Load recent clients
$recentClients = $pdo->query("SELECT client_name, created_at FROM mktg_clients ORDER BY created_at DESC LIMIT 5")->fetchAll();

ob_start();
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary shadow-sm p-3">
            <h6>Total Clients</h6>
            <div class="fs-3"><?= $totalClients ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success shadow-sm p-3">
            <h6>Total Files</h6>
            <div class="fs-3"><?= $totalFiles ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning shadow-sm p-3">
            <h6>Files This Week</h6>
            <div class="fs-3"><?= $filesThisWeek ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-secondary shadow-sm p-3">
            <h6>Logged in as</h6>
            <div class="fs-5"><?= htmlspecialchars($username) ?></div>
        </div>
    </div>
</div>

<div id="bitkode-chatbot-root"></div>
   <script src="/chatbot-n8n.js"></script>


<!-- Recent Clients Table -->
<div class="card shadow-sm">
    <div class="card-header">
        Recent Clients
    </div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Client Name</th>
                    <th>Date Added</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentClients as $client): ?>
                    <tr>
                        <td><?= htmlspecialchars($client['client_name']) ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($client['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?> - BitKode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/chatbot.css" />
    <link href="/css/chatbot-floating.css" rel="stylesheet">
</head>
<body>
    <?php require __DIR__ . '/includes/layout.php'; ?>

    <div id="bitkode-chatbot-root"></div>
    <script src="/chatbot-n8n-icon.js"></script>

</body>
</html>
