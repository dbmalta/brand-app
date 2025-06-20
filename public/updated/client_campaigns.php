<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';
require_permission($pdo, 'manage_clients');

$pageTitle = 'Client Campaigns';

$totalCampaigns = $pdo->query("SELECT COUNT(*) FROM mktg_client_campaigns")->fetchColumn();
$activeCampaigns = $pdo->query("SELECT COUNT(*) FROM mktg_client_campaigns WHERE end_date IS NULL OR end_date >= CURDATE()")->fetchColumn();

$stmt = $pdo->query("
    SELECT c.*, cl.client_name AS client_name
    FROM mktg_client_campaigns c
    JOIN mktg_clients cl ON c.clientid = cl.id
");
$campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php require __DIR__ . '/includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 collapse d-md-block bg-light px-0">
            <?php require __DIR__ . '/includes/sidebar.php'; ?>
        </nav>

        <!-- Main content -->
        <main class="col-12 col-md-9 col-lg-10 px-4 py-4">
            <h2 class="mb-4"><?= $pageTitle ?></h2>

            <!-- Dashboard Metrics -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-bg-primary shadow-sm p-3">
                        <h6>Total Campaigns</h6>
                        <div class="fs-3"><?= $totalCampaigns ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-success shadow-sm p-3">
                        <h6>Active Campaigns</h6>
                        <div class="fs-3"><?= $activeCampaigns ?></div>
                    </div>
                </div>
            </div>

            <a href="client_campaign_form.php" class="btn btn-primary mb-3">Add New Campaign</a>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($campaigns as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['campaignid']) ?></td>
                            <td><?= htmlspecialchars($c['client_name']) ?></td>
                            <td><?= htmlspecialchars($c['start_date']) ?></td>
                            <td><?= htmlspecialchars($c['end_date']) ?></td>
                            <td><?= htmlspecialchars($c['title']) ?></td>
                            <td>
                                <a href="client_campaign_form.php?id=<?= $c['campaignid'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="client_campaign_delete.php?id=<?= $c['campaignid'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this campaign?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
