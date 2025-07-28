<?php
session_start();
error_reporting(E_ALL);

$allowed_pages = ['profile', 'userproject', 'userloans', 'userdonations'];
$section = $_GET['pages'] ?? 'profile';
$action = $_GET['action'] ?? null;

// User type check for Loans tab
require_once 'config/class.user.php';
$userObj = new USER();
if (!isset($_SESSION['userSession'])) {
    $userObj->redirect('index.php?page=relog&msg=signin_required');
    exit();
}

$uid = $_SESSION['userSession'];
$showLoansTab = false;
if ($uid) {
    $stmt = $userObj->runQuery("SELECT ut.type_name FROM user_type_map um JOIN user_types ut ON um.type_id = ut.id WHERE um.user_id = :uid");
    $stmt->execute([':uid' => $uid]);
    $types = array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN));

    if ((in_array('user', $types) && count($types) === 1) || in_array('founder', $types) || in_array('debtor', $types)) {
        $showLoansTab = true;
    }
}
?>

<!-- Dashboard Tabs UI -->
<div class="container mt-5 pt-3">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?= $section === 'profile' ? 'active' : '' ?>" href="index.php?page=dashboard&pages=profile">Profile</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $section === 'userproject' ? 'active' : '' ?>" href="index.php?page=dashboard&pages=userproject">Projects</a>
        </li>
        <?php if ($showLoansTab): ?>
        <li class="nav-item">
            <a class="nav-link <?= $section === 'userloans' ? 'active' : '' ?>" href="index.php?page=dashboard&pages=userloans">Loans</a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link <?= $section === 'userbalance' ? 'active' : '' ?>" href="index.php?page=dashboard&pages=userbalance">Wallet</a>
        </li>
    </ul>
</div>

<!-- Dashboard Tab Content -->
<div class="container mt-4">
    <?php
    if ($section === 'profile') {
        if ($action === 'edit') {
            include("pages/user/editprofile.php");
        } else {
            include("pages/user/profile.php");
        }
    } 
    elseif ($section === 'userproject') {
        if ($action === 'apply') {
            include("pages/user/applyProject.php");
        } else {
            include("pages/user/userproject.php");
        }
    } 
    elseif ($section === 'userloans') {
        if ($action === 'request') {
            include("pages/user/request_loan.php");
        } 
        elseif ($action === 'viewreport') {
            include("pages/user/viewLoanReport.php");
        }
        else {
            include("pages/user/userloans.php");
        }
    }
    elseif ($section === 'userbalance') {
        include("pages/user/userbalance.php");
    }
    else {
        echo "<div class='alert alert-danger'>Invalid tab selected.</div>";
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>