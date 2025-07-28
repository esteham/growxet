<?php
require_once __DIR__ . '/../config/class.user.php';
$user = new USER();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid project ID.</div>";
    exit;
}

$projectId = (int)$_GET['id'];
$stmt = $user->runQuery("SELECT * FROM projects WHERE id = :id");
$stmt->execute(['id' => $projectId]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    echo "<div class='alert alert-danger'>Project not found.</div>";
    exit;
}

$category = $user->getCategoryById($project['categoryId']);
$owner = $user->getUserById($project['owner_id']);
?>

<div class="container mt-5">
    <h2><?= htmlspecialchars($project['project_name']) ?></h2>
    <div class="row">
        <div class="col-md-5">
            <?php if (!empty($project['project_img'])): ?>
                <img src="assets/projectimg/<?= htmlspecialchars($project['project_img']) ?>" class="img-fluid rounded mb-3" alt="Project Image">
            <?php endif; ?>
        </div>
        <div class="col-md-7">
            <table class="table table-bordered">
                <tr>
                    <th>Category</th>
                    <td><?= htmlspecialchars($category['category_name'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Owner</th>
                    <td>
                        <?= htmlspecialchars($owner['f_name'] ?? 'N/A') ?>
                        <small class="text-muted d-block"><?= htmlspecialchars($owner['uniqid'] ?? '') ?></small>
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?= ucfirst($project['project_status']) ?></td>
                </tr>
                <tr>
                    <th>Shares</th>
                    <td><?= $project['shares'] ?></td>
                </tr>
                <tr>
                    <th>Price per Share</th>
                    <td><?= number_format($project['price_per_share'], 2) ?></td>
                </tr>
                <tr>
                    <th>Total Amount</th>
                    <td><?= number_format($project['total_amount'], 2) ?></td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td><?= date('M d, Y H:i', strtotime($project['created_at'])) ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?= nl2br(htmlspecialchars($project['description'])) ?></td>
                </tr>
                <?php if (!empty($project['rejection_reason'])): ?>
                <tr>
                    <th>Rejection Reason</th>
                    <td class="text-danger"><?= nl2br(htmlspecialchars($project['rejection_reason'])) ?></td>
                </tr>
                <?php endif; ?>
            </table>
            <a href="index.php?page=<?= $project['project_status'] === 'pending' ? 'pendingProjects' : 'activeProjects' ?>" class="btn btn-secondary mt-2">Back to Projects</a>
        </div>
    </div>
</div>