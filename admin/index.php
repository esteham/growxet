<?php


session_start();
require_once 'config/class.user.php';
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$user = new USER();

// Check user login status and user type
if (
    !$user->is_logged_in() ||
    !isset($_SESSION['user_type']) ||
    !in_array($_SESSION['user_type'], ['admin', 'manager'])
) {
    header('Location: login.php');
    exit;
}
?>

<div class="d-flex" style="min-height:100vh;">
    <?php include 'includes/sidebar.php'; ?>
    <div class="flex-grow-1" style="margin-left:0;">
        <?php include 'includes/header.php'; ?>
        <div class="container-fluid pt-0 mt-0">
            <?php
            $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

            switch ($page) 
            {
                case 'dashboard':
                    include 'pages/dashboard.php';
                    break;
                case 'allUsers':
                    include 'pages/allUsers.php';
                    break;
                case 'investors':
                    include 'pages/investors.php';
                    break;
                case 'founders':
                    include 'pages/founders.php';
                    break;
                case 'debtors':
                    include 'pages/debtors.php';
                    break;
                case 'activeProjects':
                    include 'pages/activeProjects.php';
                    break;
                case 'pendingProjects':
                    include 'pages/pendingProjects.php';
                    break;
                case 'closedProjects':
                    include 'pages/closedProjects.php';
                    break;
                case 'viewProject':
                    include 'pages/viewProject.php';
                    break;
                // Loans
                case 'activeLoans':
                    include 'pages/activeLoans.php';
                    break;
                case 'dueLoans':
                    include 'pages/dueLoans.php';
                    break;
                case 'requestedLoans':
                    include 'pages/requestedLoans.php';
                    break;
                case 'paidLoans':
                    include 'pages/paidLoans.php';
                    break;
                // Wallet & Reports
                case 'balance':
                    include 'pages/balance.php';
                    break;
                case 'report':
                    include 'pages/report.php';
                    break;
                // Transaction History
                case 'txnDaily':
                    include 'pages/txnDaily.php';
                    break;
                case 'txnMonthly':
                    include 'pages/txnMonthly.php';
                    break;
                case 'txnYearly':
                    include 'pages/txnYearly.php';
                    break;
                // Settings, Messages
                case 'settings':
                    include 'pages/settings.php';
                    break;
                case 'messages':
                    include 'pages/messages.php';
                    break;
                case 'viewLoan':
                    include 'pages/viewLoan.php';
                    break;
                default:
                    echo '<h1>Page Not Found</h1>';
            }
            ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
