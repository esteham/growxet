<?php 
require_once __DIR__ . '/../../config/class.user.php';

$user = new USER();

if (!isset($_SESSION['userSession'])) {
    $user->redirect('../../index.php?page=relog&msg=signin_required');
    exit();
}

$uid = $_SESSION['userSession'];
$stmt = $user->runQuery("SELECT * FROM users WHERE id = :uid");
$stmt->execute([':uid' => $uid]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch all locations for dropdown
$locStmt = $user->runQuery("SELECT * FROM location ORDER BY country, city");
$locStmt->execute();
$locations = $locStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f_name = $_POST['f_name'];
    $contact = $_POST['contact'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $location_id = $_POST['location_id'] ?? null;
    $description = $_POST['description'];

    $stmt = $user->runQuery("UPDATE users 
        SET f_name = :f, contact = :c, dob = :d, gender = :g, location_id = :l 
        WHERE id = :uid");
    $stmt->execute([
        ':f' => $f_name,
        ':c' => $contact,
        ':d' => $dob,
        ':g' => $gender,
        ':l' => $location_id,
        ':uid' => $uid
    ]);

    header("Location: /WDPF-64/My%20Project/GrowNet/index.php?page=dashboard&pages=profile&msg=updated");
    exit();
}
?>

<div class="container mt-3">
    <form method="post" class="card p-4 shadow">
        <h4>Edit Profile</h4>
        <div class="row">
            <div class="col-md-6">
                <label>Full Name</label>
                <input type="text" name="f_name" class="form-control" value="<?= htmlspecialchars($row['f_name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label>Contact</label>
                <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($row['contact']) ?>">
            </div>
            <div class="col-md-6 mt-3">
                <label>Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($row['dob']) ?>">
            </div>
            <div class="col-md-6 mt-3">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option <?= ($row['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                    <option <?= ($row['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                    <option <?= ($row['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-md-6 mt-3">
                <label>Location</label>
                <select name="location_id" class="form-control">
                    <option value="">-- Select Location --</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= $loc['id'] ?>" <?= ($row['location_id'] == $loc['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($loc['city']) ?>, <?= htmlspecialchars($loc['country']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mt-3">
                <label>Description</label>
                <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($row['description']) ?>">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success w-25">Save Changes</button>
            <a href="/Web_Dev/Project/GrowNet/index.php?page=dashboard&pages=profile" class="btn btn-secondary w-25">Cancel</a>
        </div>
    </form>
</div>
