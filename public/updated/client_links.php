<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';
require_permission($pdo, 'manage_clients');

$clientId = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;
if (!$clientId) die("Missing client ID");

// Fetch client info
$stmt = $pdo->prepare("SELECT * FROM mktg_clients WHERE id = ?");
$stmt->execute([$clientId]);
$client = $stmt->fetch();
if (!$client) die("Client not found.");

// Fetch links
$stmt = $pdo->prepare("SELECT * FROM mktg_client_links WHERE client_id = ? ORDER BY type, label");
$stmt->execute([$clientId]);
$links = $stmt->fetchAll();

$pageTitle = "Client Links - " . htmlspecialchars($client['client_name']);
ob_start();
?>

<div class="mb-3">
    <a href="clients.php" class="btn btn-secondary">⬅ Back to Clients</a>
    <a href="client_link_form.php?client_id=<?= $clientId ?>" class="btn btn-primary float-end">➕ Add Link</a>
</div>

<?php if ($links): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($links as $link): ?>
            <?php
                $icon = match(strtolower($link['type'])) {
                    'facebook' => '📘',
                    'instagram' => '📷',
                    'linkedin' => '💼',
                    'twitter' => '🐦',
                    'tiktok' => '🎵',
                    'website' => '🌐',
                    default => '🔗'
                };
            ?>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= $icon ?> <?= htmlspecialchars($link['label']) ?></h5>
                        <p class="card-text">
                            <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank"><?= htmlspecialchars($link['url']) ?></a>
                        </p>

                        <?php if ($link['username'] || $link['password']): ?>
                            <p class="mb-1"><strong>Username:</strong> <?= htmlspecialchars($link['username']) ?: '—' ?></p>
                            <p class="mb-1">
                                <strong>Password:</strong>
                                <span class="password-toggle" style="user-select: none;">
                                    <input type="password" value="<?= htmlspecialchars($link['password']) ?>" class="form-control form-control-sm d-inline-block" style="width:auto; display:inline-block;" readonly>
                                    <button class="btn btn-sm btn-outline-secondary toggle-password">👁️</button>
                                </span>
                            </p>
                        <?php endif; ?>

                        <?php if ($link['notes']): ?>
                            <p class="small text-muted"><?= nl2br(htmlspecialchars($link['notes'])) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="client_link_form.php?client_id=<?= $clientId ?>&id=<?= $link['id'] ?>" class="btn btn-sm btn-outline-primary">✏️ Edit</a>
                        <a href="client_link_delete.php?client_id=<?= $clientId ?>&id=<?= $link['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this link?')">🗑 Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No links added yet.</p>
<?php endif; ?>

<script>
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const input = btn.closest('.password-toggle').querySelector('input');
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.textContent = input.type === 'password' ? '👁️' : '🙈';
        });
    });
</script>

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
