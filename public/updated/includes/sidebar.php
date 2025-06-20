<?php
$pdo = $GLOBALS['pdo'] ?? null;
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar toggle button for small screens -->
<button class="btn btn-outline-secondary d-md-none mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    ☰ Menu
</button>

<!-- Sidebar wrapper -->
<div class="collapse d-md-block" id="sidebarMenu">
    <div class="bg-light border-end p-3 h-100" style="min-width: 200px;">
        <ul class="nav flex-column">

            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'welcome.php' ? 'active' : '' ?>" href="welcome.php">
                    🏠 Home
                </a>
            </li>

            <?php if ($pdo && userHasPermission($pdo, 'manage_clients')): ?>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'clients.php' ? 'active' : '' ?>" href="clients.php">
                    🧍 Clients
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'clients.php' ? 'active' : '' ?>" href="client_campaigns.php">
                    📣 Campaigns
                </a>
            </li>


            <?php endif; ?>

            <?php if ($pdo && userHasPermission($pdo, 'manage_config')): ?>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'config.php' ? 'active' : '' ?>" href="config.php">
                    ⚙️ Config
                </a>
            </li>
            <?php endif; ?>

            <?php if ($pdo && userHasPermission($pdo, 'manage_users')): ?>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'users.php' ? 'active' : '' ?>" href="users.php">
                    👤 Users
                </a>
            </li>
            <?php endif; ?>

        </ul>
    </div>
</div>
