<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/auth.php';

$GLOBALS['pdo'] = $pdo;

require_once __DIR__ . '/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar: collapsible on small screens, fixed on md+ -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 collapse d-md-block bg-light px-0">
            <?php require __DIR__ . '/sidebar.php'; ?>
        </nav>

        <!-- Main content -->
        <main class="col-12 col-md-9 col-lg-10 px-4 py-4">
            <?php if (isset($pageTitle)) : ?>
                <h2 class="mb-4"><?= htmlspecialchars($pageTitle) ?></h2>
            <?php endif; ?>
            <?= $content ?>
        </main>
    </div>
</div>

<!-- Bootstrap JS bundle (required for menu toggle) -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggleBtn = document.getElementById('darkModeToggle');
    const prefersDark = localStorage.getItem('dark-mode') === 'true';

    if (prefersDark) {
        document.body.classList.add('dark-mode');
        toggleBtn.textContent = '☀️';
    }

    toggleBtn?.addEventListener('click', () => {
        const isDark = document.body.classList.toggle('dark-mode');
        localStorage.setItem('dark-mode', isDark);
        toggleBtn.textContent = isDark ? '☀️' : '🌙';
    });
</script>


