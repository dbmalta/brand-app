<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/includes/auth.php';
require_permission($pdo, 'manage_clients');

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$pageTitle = $id ? 'Edit Campaign' : 'Add Campaign';
$errors = [];

// Get client list for dropdown
$clients = $pdo->query("SELECT id, client_name FROM mktg_clients ORDER BY client_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Define fields
$data = [
    'clientid' => '',
    'start_date' => '',
    'end_date' => '',
    'title' => '',
    'objectives' => ''
];

// Load existing campaign
if ($id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM mktg_client_campaigns WHERE campaignid = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if (!$existing) die('Campaign not found.');
    $data = array_merge($data, $existing);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($data) as $field) {
        $data[$field] = trim($_POST[$field] ?? '');
    }

    if ($data['clientid'] === '' || $data['title'] === '' || $data['start_date'] === '') {
        $errors[] = 'Client, Title, and Start Date are required.';
    }

    if (empty($errors)) {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE mktg_client_campaigns SET clientid = ?, start_date = ?, end_date = ?, title = ?, objectives = ? WHERE campaignid = ?");
            $stmt->execute([
                $data['clientid'],
                $data['start_date'],
                $data['end_date'] ?: null,
                $data['title'],
                $data['objectives'],
                $id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO mktg_client_campaigns (clientid, start_date, end_date, title, objectives) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['clientid'],
                $data['start_date'],
                $data['end_date'] ?: null,
                $data['title'],
                $data['objectives']
            ]);
        }
        header("Location: client_campaigns.php");
        exit;
    }
}
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
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 collapse d-md-block bg-light px-0">
            <?php require __DIR__ . '/includes/sidebar.php'; ?>
        </nav>

        <main class="col-12 col-md-9 col-lg-10 px-4 py-4">
            <h2 class="mb-4"><?= $pageTitle ?></h2>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST">
                <?php if ($id): ?>
                    <input type="hidden" name="id" value="<?= $id ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="clientid" class="form-label">Client</label>
                    <select name="clientid" id="clientid" class="form-select" required>
                        <option value="">-- Select Client --</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>" <?= $client['id'] == $data['clientid'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($client['client_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($data['start_date']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($data['end_date']) ?>">
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Campaign Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($data['title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="objectives" class="form-label">Campaign Objectives</label>
                    <textarea name="objectives" id="objectives" class="form-control" rows="4"><?= htmlspecialchars($data['objectives']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-success">Save</button>
                <a href="client_campaigns.php" class="btn btn-secondary">Cancel</a>
            </form>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
