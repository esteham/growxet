<?php
require_once 'config/class.user.php';

$user = new USER();

if (!isset($_SESSION['userSession'])) {
    $user->redirect('index.php?page=relog&msg=signin_required');
    exit();
}

$stmt = $user->runQuery("SELECT * FROM users WHERE id = :uid");
$stmt->execute([':uid' => $_SESSION['userSession']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit();
}

// Get location info if exists
$locInfo = 'N/A';
if (!empty($row['location_id'])) {
    $locStmt = $user->runQuery("SELECT city, country FROM location WHERE id = :id");
    $locStmt->execute([':id' => $row['location_id']]);
    $loc = $locStmt->fetch(PDO::FETCH_ASSOC);
    if ($loc) {
        $locInfo = htmlspecialchars($loc['city']) . ', ' . htmlspecialchars($loc['country']);
    }
}

// Get user roles
$roleStmt = $user->runQuery("SELECT ut.type_name FROM user_types ut JOIN user_type_map map ON ut.id = map.type_id WHERE map.user_id = :uid");
$roleStmt->execute([':uid' => $row['id']]);
$userRoles = $roleStmt->fetchAll(PDO::FETCH_COLUMN);

$isFounder = in_array('founder', $userRoles);
$isInvestor = in_array('investor', $userRoles);
$isDebtor = in_array('debtor', $userRoles);
$isLender = in_array('lender', $userRoles);
$isPartner = in_array('partner', $userRoles);

// Founder: active projects
$activeProjects = 0;
if ($isFounder) {
    $stmt = $user->runQuery("SELECT COUNT(*) FROM projects WHERE owner_id = :uid AND project_status = 'active'");
    $stmt->execute([':uid' => $row['id']]);
    $activeProjects = $stmt->fetchColumn();
}

// Investor: invested projects
$investedProjects = 0;
if ($isInvestor) {
    $stmt = $user->runQuery("SELECT COUNT(DISTINCT project_id) FROM share_orders WHERE user_id = :uid");
    $stmt->execute([':uid' => $row['id']]);
    $investedProjects = $stmt->fetchColumn();
}

// Debtor: loan stats
$debtorLoans = ['taken' => 0, 'paid' => 0, 'due' => 0];
if ($isDebtor) {
    $stmt = $user->runQuery("SELECT 
        SUM(amount) AS taken,
        SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) AS paid,
        SUM(CASE WHEN status = 'due' THEN amount ELSE 0 END) AS due
        FROM loans WHERE user_id = :uid");
    $stmt->execute([':uid' => $row['id']]);
    $debtorLoans = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lender: loan stats
$lenderLoans = ['given' => 0, 'returned' => 0];
if ($isLender) {
    $stmt = $user->runQuery("SELECT 
        SUM(amount) AS given,
        SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) AS returned
        FROM loans WHERE lender_id = :uid");
    $stmt->execute([':uid' => $row['id']]);
    $lenderLoans = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="card shadow p-4">
    <div class="row">
        <!-- Profile Picture -->
        <div class="col-md-3 text-center">
            <img src="<?= $row['user_img'] ?? 'assets/img/default.jpg' ?>" class="img-fluid rounded-circle mb-2" width="150" height="150" alt="Profile Picture">
            <form action="pages/user/upload_user_img.php" method="post" enctype="multipart/form-data">
                <input type="file" name="user_img" class="form-control mb-2" accept="image/*" required>
                <button type="submit" class="btn btn-sm btn-primary">Upload Picture</button>
            </form>
        </div>

        <!-- Profile Info -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h3><?= htmlspecialchars($row['f_name']) ?></h3>
                    <p class="text-muted">@<?= htmlspecialchars($row['user_name']) ?></p>
                    <p>📅 Joined on: <?= date('l, d F Y', strtotime($row['reg_date'] ?? $row['created_at'] ?? '')) ?></p>
                </div>
                <div>
                    <a href="index.php?page=dashboard&pages=profile&action=edit" class="btn btn-md btn-outline-secondary mr-2">Edit</a>
                    <a href="#" class="btn btn-md btn-outline-secondary">Print</a>
                </div>
            </div>

            <hr>
            <div>
                <h4 class="text-capitalize text-muted"><i>Personal Details:</i></h4>
            </div>
            <div class="row">
                <div class="col-md-6"><strong>Date of Birth:</strong> <?= htmlspecialchars($row['dob'] ?? 'N/A') ?></div>
                <div class="col-md-6"><strong>Contact No:</strong> <?= htmlspecialchars($row['contact'] ?? 'N/A') ?></div>
                <div class="col-md-6"><strong>Gender:</strong> <?= htmlspecialchars($row['gender'] ?? 'N/A') ?></div>
                <div class="col-md-6"><strong>Email:</strong> <?= htmlspecialchars($row['user_email'] ?? 'N/A') ?></div>
                <div class="col-md-6"><strong>Location:</strong> <?= $locInfo ?></div>
            </div>

            <hr>
            <div>
                <h4 class="text-capitalize text-muted"><i>User Account Details:</i></h4>
            </div>
            <div class="row">
                <div class="col-md-6"><strong>User ID:</strong> <?= htmlspecialchars($row['uniqid'] ?? 'N/A') ?></div>

                <?php if ($isFounder): ?>
                    <div class="col-md-12"><strong>Active Projects Created:</strong> <?= $activeProjects ?></div>
                <?php endif; ?>

                <?php if ($isInvestor): ?>
                    <div class="col-md-12"><strong>Projects Invested In:</strong> <?= $investedProjects ?></div>
                <?php endif; ?>

                <?php if ($isDebtor): ?>
                    <div class="col-md-12"><strong>Loans Taken:</strong> ৳<?= number_format($debtorLoans['taken'], 2) ?></div>
                    <div class="col-md-12"><strong>Loans Paid:</strong> ৳<?= number_format($debtorLoans['paid'], 2) ?></div>
                    <div class="col-md-12"><strong>Loans Due:</strong> ৳<?= number_format($debtorLoans['due'], 2) ?></div>
                <?php endif; ?>

                <?php if ($isLender): ?>
                    <div class="col-md-12"><strong>Loans Given:</strong> ৳<?= number_format($lenderLoans['given'], 2) ?></div>
                    <div class="col-md-12"><strong>Loans Returned:</strong> ৳<?= number_format($lenderLoans['returned'], 2) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
