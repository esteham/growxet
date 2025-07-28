<?php
require_once 'config/class.user.php';

$user = new USER();

if (!isset($_SESSION['userSession'])) {
    $user->redirect('index.php?page=relog&msg=signin_required');
    exit();
}

$uid = $_SESSION['userSession'];
$msg = '';

// Fetch categories
$catStmt = $user->runQuery("SELECT id, category_name FROM category ORDER BY category_name ASC");
$catStmt->execute();
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch project types from enum in DB
$typeStmt = $user->runQuery("SHOW COLUMNS FROM projects LIKE 'project_type'");
$typeStmt->execute();
$typeRow = $typeStmt->fetch(PDO::FETCH_ASSOC);
$typeEnum = $typeRow['Type'];
preg_match("/^enum\((.*)\)$/", $typeEnum, $matches);
$types = [];
if (!empty($matches[1])) {
    foreach (explode(",", $matches[1]) as $value) {
        $types[] = trim($value, " '");
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_name = $_POST['project_name'];
    $description = $_POST['description'];
    $categoryId = $_POST['categoryId'];
    $project_type = $_POST['project_type'];
    $shares = $_POST['shares'];
    $price_per_share = $_POST['price_per_share'];
    $total_amount = $shares * $price_per_share;
    $campaign_start = $_POST['campaign_start'] ?? null;
    $campaign_end = $_POST['campaign_end'] ?? null;

    $uniqid = uniqid('prj_');
    $imageName = '';
    if (isset($_FILES['project_img']) && $_FILES['project_img']['error'] === 0) {
        $ext = pathinfo($_FILES['project_img']['name'], PATHINFO_EXTENSION);
        $imageName = 'project_' . time() . '.' . $ext;
        $uploadPath = 'admin/assets/projectimg/' . $imageName;
        move_uploaded_file($_FILES['project_img']['tmp_name'], $uploadPath);
    }

    $stmt = $user->runQuery("INSERT INTO projects (uniqid, project_name, description, project_img, categoryId, project_type, project_status, owner_id, shares, price_per_share, total_amount, campaign_start, campaign_end)
                             VALUES (:uniqid, :name, :desc, :img, :cat, :ptype, 'pending', :owner, :shares, :pps, :total, :cstart, :cend)");
    $stmt->execute([
        ':uniqid' => $uniqid,
        ':name' => $project_name,
        ':desc' => $description,
        ':img' => $imageName,
        ':cat' => $categoryId,
        ':ptype' => $project_type,
        ':owner' => $uid,
        ':shares' => $shares,
        ':pps' => $price_per_share,
        ':total' => $total_amount,
        ':cstart' => $campaign_start,
        ':cend' => $campaign_end
    ]);
    $msg = '<div class="alert alert-success">Project applied successfully and is pending approval.</div>';
}
?>

<div class="container card p-4 shadow">
    <h4>Apply New Project</h4>
    <?= $msg ?>
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <label>Project Name</label>
                <input type="text" name="project_name" class="form-control" required value="">
            </div>
            <div class="col-md-6">
                <label>Category</label>
                <select name="categoryId" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>">
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mt-3">
                <label>Project Type</label>
                <select name="project_type" class="form-control" required>
                    <option value="">-- Select Type --</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>"><?= ucfirst(str_replace('-', ' ', $type)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12 mt-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4" required></textarea>
            </div>
            <div class="col-md-6 mt-3">
                <label>Number of Shares</label>
                <input type="number" name="shares" class="form-control" required value="">
            </div>
            <div class="col-md-6 mt-3">
                <label>Price per Share</label>
                <input type="number" name="price_per_share" step="0.01" class="form-control" required value="">
            </div>
            <div class="col-md-6 mt-3">
                <label>Campaign Start Date</label>
                <input type="date" name="campaign_start" class="form-control" required>
            </div>
            <div class="col-md-6 mt-3">
                <label>Campaign End Date</label>
                <input type="date" name="campaign_end" class="form-control" required>
            </div>
            <div class="col-md-12 mt-3">
                <label>Project Image</label>
                <input type="file" name="project_img" class="form-control" accept="image/*" required>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success w-25">Submit Project</button>
            <a href="index.php?page=dashboard&pages=userproject" class="btn btn-secondary w-25">Cancel</a>
        </div>
    </form>
</div>
