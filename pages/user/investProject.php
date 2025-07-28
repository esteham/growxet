<?php
require_once '../../config/class.user.php';
session_start();

$user = new USER();

if (!isset($_SESSION['userSession'])) {
    header('Location: ../../index.php?page=relog&msg=signin_required');
    exit();
}

$userId = $_SESSION['userSession'];
$projectId = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
$shares = isset($_POST['shares']) ? (int)$_POST['shares'] : 0;

// Fetch project and founder
$stmt = $user->runQuery("SELECT owner_id, price_per_share FROM projects WHERE id = :pid AND project_status = 'active'");
$stmt->execute([':pid' => $projectId]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    echo "<script>alert('Project not found or not active.');window.location.href='../../index.php?page=dashboard&pages=userproject';</script>";
    exit();
}

$founderId = $project['owner_id'];
$pricePerShare = (float)$project['price_per_share'];
$totalAmount = $shares * $pricePerShare;

// Fetch uniqid for sender (investor) and receiver (founder)
$stmtInvestor = $user->runQuery("SELECT uniqid FROM users WHERE id = :id");
$stmtInvestor->execute([':id' => $userId]);
$investorUniq = $stmtInvestor->fetchColumn();

$stmtFounder = $user->runQuery("SELECT uniqid FROM users WHERE id = :id");
$stmtFounder->execute([':id' => $founderId]);
$founderUniq = $stmtFounder->fetchColumn();

// Call investInProject (which already checks balance and inserts share_orders and deducts balance and inserts transactions)
$result = $user->investInProject($userId, $projectId, $shares);

// Do NOT insert into balance_transactions here! investInProject() already does it.

echo "<script>alert('".htmlspecialchars($result['message'])."');window.location.href='../../index.php?page=dashboard&pages=userproject';</script>";
exit();
?>