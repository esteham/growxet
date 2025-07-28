<?php
require_once '../../config/class.user.php';
session_start();

$user = new USER();

if (!isset($_SESSION['userSession'])) {
    header("Location: ../../index.php?page=relog&msg=signin_required");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_id'])) {
    $loan_id = $_POST['loan_id'];
    $uid = $_SESSION['userSession'];

    $stmt = $user->runQuery("UPDATE loans SET status = 'cancelled' WHERE id = :id AND user_id = :uid AND status = 'pending'");
    $stmt->execute([':id' => $loan_id, ':uid' => $uid]);
}

header("Location: ../../index.php?page=userloans");
exit();
