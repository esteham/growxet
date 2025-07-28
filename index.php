<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the USER class definition BEFORE any usage
require_once 'config/class.user.php';

// Handle logout request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $DB_con = new USER();
    $DB_con->logout();
    header('Location: index.php');
    exit();
}

include('includes/header.php');

// Pages that don't need login
$public_pages = ['relog', 'layout', 'project']; 

$page = $_GET['page'] ?? 'layout';

if (!in_array($page, $public_pages)) {

    if (!isset($_SESSION['userSession'])) {

        header('Location: index.php?page=relog&msg=signin_required');
        exit();
    }
}



?>


<!-- Main Content -->
<div class="container-fluid my-5 py-2">
<?php

switch($page) {
    case 'project':
        include('pages/project.php');
        break;
    case 'relog':
        include('pages/relog.php');
        break;
    case 'dashboard':
        include('pages/dashboard.php');
        break;
    default:
        include('pages/layout.php');
}


?>



</div>




<!-- Footer -->

<?php include('includes/footer.php'); ?>
