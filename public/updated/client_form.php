<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';
require_permission($pdo, 'manage_clients');

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$pageTitle = $id ? 'Edit Client' : 'Add Client';
$errors = [];

$data = [
    'client_name' => '',
    'address_line1' => '',
    'address_line2' => '',
    'city' => '',
    'postcode' => '',
    'country' => '',
    'contact_first_name' => '',
    'contact_last_name' => '',
    'contact_email' => '',
    'contact_phone' => '',
    'client_profile' => '',
    'branding_colours' => '',
    'branding_voice' => '',
    'branding_instructions' => '',
];

// Load existing data for edit
if ($id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM mktg_clients WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if (!$existing) die('Client not found.');
    $data = array_merge($data, $existing);
}

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($data) as $field) {
        $data[$field] = trim($_POST[$field] ?? '');
    }

    if ($data['client_name'] === '') {
        $errors[] = 'Client name is required.';
    }

    if (empty($errors)) {
        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
            $id = (int)$_POST['id'];
            $set = implode(", ", array_map(fn($k) => "$k = ?", array_keys($data)));
            $stmt = $pdo->prepare("UPDATE mktg_clients SET $set WHERE id = ?");
            $stmt->execute(array_merge(array_values($data), [$id]));
        } else {
            $columns = implode(", ", array_keys($data));
            $placeholders = implode(", ", array_fill(0, count($data), '?'));
            $stmt = $pdo->prepare("INSERT INTO mktg_clients ($columns) VALUES ($placeholders)");
            $stmt->execute(array_values($data));
            $id = $pdo->lastInsertId();

            $uploadDir = __DIR__ . "/uploads/clients/$id";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
        }

        header("Location: clients.php");
        exit;
    }
}

ob_start();
?>

<form method="POST" class="row g-3">
    <?php if ($id): ?>
        <input type="hidden" name="id" value="<?= $id ?>">
    <?php endif; ?>

    <div class="col-md-6">
        <label class="form-label">Client Name</label>
        <input type="text" name="client_name" class="form-control" required value="<?= htmlspecialchars($data['client_name']) ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Country</label>
        <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($data['country']) ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Address Line 1</label>
        <input type="text" name="address_line1" class="form-control" value="<?= htmlspecialchars($data['address_line1']) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Address Line 2</label>
        <input type="text" name="address_line2" class="form-control" value="<?= htmlspecialchars($data['address_line2']) ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">City</label>
        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($data['city']) ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Postcode</label>
        <input type="text" name="postcode" class="form-control" value="<?= htmlspecialchars($data['postcode']) ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Contact First Name</label>
        <input type="text" name="contact_first_name" class="form-control" value="<?= htmlspecialchars($data['contact_first_name']) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Contact Last Name</label>
        <input type="text" name="contact_last_name" class="form-control" value="<?= htmlspecialchars($data['contact_last_name']) ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Contact Email</label>
        <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($data['contact_email']) ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Contact Phone</label>
        <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars($data['contact_phone']) ?>">
    </div>

    <div class="col-12">
        <label class="form-label">Client Profile</label>
        <textarea name="client_profile" class="form-control"><?= htmlspecialchars($data['client_profile']) ?></textarea>
    </div>

    <div class="col-12">
        <label class="form-label">Branding Colours</label>
        <textarea name="branding_colours" class="form-control"><?= htmlspecialchars($data['branding_colours']) ?></textarea>
    </div>

    <div class="col-12">
        <label class="form-label">Voice / Tone</label>
        <textarea name="branding_voice" class="form-control"><?= htmlspecialchars($data['branding_voice']) ?></textarea>
    </div>

    <div class="col-12">
        <label class="form-label">Specific Instructions</label>
        <textarea name="branding_instructions" class="form-control"><?= htmlspecialchars($data['branding_instructions']) ?></textarea>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?= implode("<br>", array_map('htmlspecialchars', $errors)) ?>
        </div>
    <?php endif; ?>

    <div class="col-12">
        <button type="submit" class="btn btn-primary"><?= $id ? 'Update' : 'Create' ?> Client</button>
        <a href="clients.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php if ($id): ?>
    <hr class="my-5">

    <h4>🔗 Links</h4>
    <p><a href="client_links.php?client_id=<?= $id ?>" class="btn btn-outline-primary btn-sm">Manage Links</a></p>

    <?php
    $stmt = $pdo->prepare("SELECT * FROM mktg_client_links WHERE client_id = ? ORDER BY type, label");
    $stmt->execute([$id]);
    $links = $stmt->fetchAll();
    ?>

    <?php if ($links): ?>
        <ul class="list-group">
            <?php foreach ($links as $link): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <?= match($link['type']) {
                            'facebook' => '📘',
                            'instagram' => '📷',
                            'linkedin' => '💼',
                            'tiktok' => '🎵',
                            'twitter' => '🐦',
                            'website' => '🌐',
                            default => '🔗'
                        }; ?>
                        <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank"><?= htmlspecialchars($link['label']) ?></a>
                    </span>
                    <a href="client_link_form.php?client_id=<?= $id ?>&id=<?= $link['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No links available.</p>
    <?php endif; ?>
<?php endif; ?>

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
