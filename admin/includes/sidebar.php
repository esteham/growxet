<?php if (!isset($page)) { $page = ''; } ?>
<div class="sidebar bg-dark custom-sidebar">
    <!-- 1. Title Section -->
    <div class="sidebar-title text-center py-3 sidebar-section">
        <a href="index.php" class="h4 text-white text-decoration-none mb-0 d-block">GrowNet Admin</a>
    </div>

    <!-- 2. Main Navigation Section -->
    <div class="sidebar-section">
        <ul class="nav flex-column nav-pills p-3">
            <li class="nav-item">
                <a class="nav-link <?= $page == 'dashboard' ? 'active' : '' ?>" href="index.php?page=dashboard">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <?php $usersDropdown = in_array($page, ['allUsers', 'investors', 'founders', 'debtors']); ?>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#usersMenu" role="button" aria-expanded="<?= $usersDropdown ? 'true' : 'false' ?>" aria-controls="usersMenu">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <div class="collapse <?= $usersDropdown ? 'show' : '' ?>" id="usersMenu">
                    <ul class="pl-3">
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'allUsers' ? 'active' : '' ?>" href="index.php?page=allUsers">
                                <i class="fas fa-list"></i> All Users
                            </a>
                        </li>
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'investors' ? 'active' : '' ?>" href="index.php?page=investors">
                                <i class="fas fa-user-tie"></i> Investors
                            </a>
                        </li>
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'founders' ? 'active' : '' ?>" href="index.php?page=founders">
                                <i class="fas fa-user-cog"></i> Founders
                            </a>
                        </li>
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'debtors' ? 'active' : '' ?>" href="index.php?page=debtors">
                                <i class="fas fa-user-slash"></i> Debtors
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <?php $projectsDropdown = in_array($page, ['activeProjects', 'pendingProjects', 'closedProjects', 'viewProject']); ?>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#projectsMenu" role="button" aria-expanded="<?= $projectsDropdown ? 'true' : 'false' ?>" aria-controls="projectsMenu">
                    <i class="fas fa-project-diagram"></i> Manage Projects
                </a>
                <div class="collapse <?= $projectsDropdown ? 'show' : '' ?>" id="projectsMenu">
                    <ul class="pl-3">
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'activeProjects' ? 'active' : '' ?>" href="index.php?page=activeProjects">
                                <i class="fas fa-check-circle"></i> Active Projects
                            </a>
                        </li>
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'pendingProjects' ? 'active' : '' ?>" href="index.php?page=pendingProjects">
                                <i class="fas fa-hourglass-half"></i> Pending Projects
                            </a>
                        </li>
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'closedProjects' ? 'active' : '' ?>" href="index.php?page=closedProjects">
                                <i class="fas fa-times-circle"></i> Closed Projects
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <?php $loansDropdown = in_array($page, ['activeLoans', 'dueLoans', 'requestedLoans', 'paidLoans']); ?>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#loansMenu" role="button" aria-expanded="<?= $loansDropdown ? 'true' : 'false' ?>" aria-controls="loansMenu">
                    <i class="fas fa-hand-holding-usd"></i> Manage Loans
                </a>
                <div class="collapse <?= $loansDropdown ? 'show' : '' ?>" id="loansMenu">
                    <ul class="pl-3">
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'activeLoans' ? 'active' : '' ?>" href="index.php?page=activeLoans">
                                <i class="fas fa-money-check-alt"></i> Active Loans
                            </a>
                        </li>
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'requestedLoans' ? 'active' : '' ?>" href="index.php?page=requestedLoans">
                                <i class="fas fa-file-signature"></i> Requested Loans
                            </a>
                        </li>
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'paidLoans' ? 'active' : '' ?>" href="index.php?page=paidLoans">
                                <i class="fas fa-check-double"></i> Paid Loans
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>

    <!-- 3. Wallet & Reports Section -->
    <div class="sidebar-section">
        <ul class="nav flex-column nav-pills px-3">
            <li class="nav-item">
                <a class="nav-link <?= $page == 'balance' ? 'active' : '' ?>" href="index.php?page=balance">
                    <i class="fas fa-wallet"></i> Admin Wallet
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page == 'report' ? 'active' : '' ?>" href="index.php?page=report">
                    <i class="fas fa-chart-line"></i> Reports
                </a>
            </li>
            <?php $txnDropdown = in_array($page, ['txnDaily', 'txnMonthly', 'txnYearly']); ?>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#txnMenu" role="button" aria-expanded="<?= $txnDropdown ? 'true' : 'false' ?>" aria-controls="txnMenu">
                    <i class="fas fa-history"></i> Transaction History
                </a>
                <div class="collapse <?= $txnDropdown ? 'show' : '' ?>" id="txnMenu">
                    <ul class="pl-3">
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'txnDaily' ? 'active' : '' ?>" href="index.php?page=txnDaily">
                                <i class="fas fa-calendar-day"></i> Daily
                            </a>
                        </li>
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'txnMonthly' ? 'active' : '' ?>" href="index.php?page=txnMonthly">
                                <i class="fas fa-calendar-alt"></i> Monthly
                            </a>
                        </li>
                        <li class="nav-item custom-nav">
                            <a class="nav-link <?= $page == 'txnYearly' ? 'active' : '' ?>" href="index.php?page=txnYearly">
                                <i class="fas fa-calendar"></i> Yearly
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>

    <!-- 4. Settings, Messages & Logout Section -->
    <div class="sidebar-section">
        <ul class="nav flex-column nav-pills px-3 mb-2">
            <li class="nav-item">
                <a class="nav-link <?= $page == 'settings' ? 'active' : '' ?>" href="index.php?page=settings">
                    <i class="fas fa-cogs"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page == 'messages' ? 'active' : '' ?>" href="index.php?page=messages">
                    <i class="fas fa-envelope"></i> Messages/Complaints
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <footer class="bg-transparent site-footer mb-0 text-muted">
        <div class="container pt-2 mt-2 text-justified">
            <span>© <?php echo date("Y"); ?> GrowNet Admin Panel.</span><br>
            <span>All rights reserved.</span>
        </div>
    </footer>
</div>