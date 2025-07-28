<?php
require_once __DIR__ . '/../../config/class.user.php';
$user = new USER();

if (session_status() === PHP_SESSION_NONE) session_start();
$uid = $_SESSION['userSession'] ?? null;

if (!$uid) {
    echo '<div class="alert alert-danger">You must be logged in to request a loan.</div>';
    exit;
}

// Fetch active projects for this founder
$stmt = $user->runQuery("SELECT id, uniqid, project_name FROM projects WHERE owner_id = :uid AND project_status = 'active'");
$stmt->execute([':uid' => $uid]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_uniqid = $_POST['project_id'] ?? '';
    $loan_amount = floatval($_POST['loan_amount'] ?? 0);
    $purpose = trim($_POST['purpose'] ?? '');

    // File upload handling
    $allowed = ['pdf'];
    $upload_dir = __DIR__ . '/../../assets/doc/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $tin_file = $_FILES['tin_certificate'] ?? null;
    $business_file = $_FILES['business_id_certificate'] ?? null;
    $nid_file = $_FILES['national_id'] ?? null;

    function upload_pdf($file, $prefix, $upload_dir, $uid) {
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') return false;
            $filename = $prefix . '_uid' . $uid . '_' . time() . '.pdf';
            $dest = $upload_dir . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                return $filename;
            }
        }
        return false;
    }

    $tin_path = upload_pdf($tin_file, 'tin', $upload_dir, $uid);
    $business_path = upload_pdf($business_file, 'business', $upload_dir, $uid);
    $nid_path = upload_pdf($nid_file, 'nid', $upload_dir, $uid);

    if (!$project_uniqid || !$loan_amount || !$purpose || !$tin_path || !$business_path || !$nid_path) {
        $msg = '<div class="alert alert-danger">All fields and documents are required (PDF only).</div>';
    } else {
        $stmt = $user->runQuery("INSERT INTO loans (user_id, project_uniqid, loan_amount, purpose, status, created_at, tin_certificate, business_id_certificate, national_id) VALUES (:uid, :project_uniqid, :amount, :purpose, 'pending', NOW(), :tin, :business, :nid)");
        $ok = $stmt->execute([
            ':uid' => $uid,
            ':project_uniqid' => $project_uniqid,
            ':amount' => $loan_amount,
            ':purpose' => $purpose,
            ':tin' => $tin_path,
            ':business' => $business_path,
            ':nid' => $nid_path
        ]);
        if ($ok) {
            $msg = '<div class="alert alert-success">Loan request submitted! It will be reviewed by admin.</div>';
        } else {
            $msg = '<div class="alert alert-danger">Failed to submit loan request. Please try again.</div>';
        }
    }
}
?>

<div class="container card p-4 shadow">
    <h4>Request Loan for Project</h4>
    <?= $msg ?>
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <label for="project_id">Select Project</label>
                <select class="form-control" id="project_id" name="project_id" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($projects as $prj): ?>
                        <option value="<?= htmlspecialchars($prj['uniqid']) ?>">
                            <?= htmlspecialchars($prj['project_name']) ?> (ID: <?= htmlspecialchars($prj['uniqid']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="loan_amount">Loan Amount</label>
                <input type="number" class="form-control" id="loan_amount" name="loan_amount" min="1" step="0.01" required>
            </div>
            <div class="col-md-12 mt-3">
                <label for="purpose">Purpose of Loan</label>
                <textarea class="form-control" id="purpose" name="purpose" rows="3" required></textarea>
            </div>
            <div class="col-md-4 mt-3">
                <label for="tin_certificate">TIN Certificate (PDF)</label>
                <input type="file" class="form-control" id="tin_certificate" name="tin_certificate" accept="application/pdf" required>
            </div>
            <div class="col-md-4 mt-3">
                <label for="business_id_certificate">BIN Certificate (PDF)</label>
                <input type="file" class="form-control" id="business_id_certificate" name="business_id_certificate" accept="application/pdf" required>
            </div>
            <div class="col-md-4 mt-3">
                <label for="national_id">National ID (PDF)</label>
                <input type="file" class="form-control" id="national_id" name="national_id" accept="application/pdf" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-success w-25">Submit Request</button>
            <a href="index.php?page=dashboard&pages=userloans" class="btn btn-secondary w-25">Cancel</a>
        </div>
    </form>
</div>
