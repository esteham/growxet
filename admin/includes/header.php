<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GrowNet Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-header sticky-top bg-transparent">
    <nav class="navbar navbar-expand-lg navbar-transparent">
        <!-- <a href="index.php" class="navbar-brand">GrowNet Admin Panel</a> -->
        <div class="ml-auto d-flex align-items-center">
            <form class="form-inline mr-3">
                <input type="search" name="" class="form-control form-control-sm mr-sm-2" placeholder="search...">
                <button class="btn btn-sm btn-outline-dark" type="submit"><i class="fas fa-search"></i> Search</button>
            </form>

            <!-- Notification Icon with Dropdown -->
            <div class="dropdown mr-2">
                <a href="#" class="dropdown-toggle text-dark" id="notifDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bell" style="font-size: 20px;"></i>

                </a>
                <div class="dropdown-menu dropdown-menu-right shadow notification-dropdown" aria-labelledby="notifDropdown">
                    <h6 class="dropdown-header">All Notifications</h6>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center text-primary" href="index.php?page=pendingProjects">View All <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>

            <div class="dropdown">
                <a href="#" class="dropdown-toggle text-dark d-flex align-item-center" data-toggle="dropdown">
                    <i class="fa fa-user-circle" style="font-size: 20px;"></i>
                    <?php echo $_SESSION['user_name'] ?? 'Admin'; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="index.php?page=profile" class="dropdown-item"><i class="fas fa-user-circle mr-2"></i> Profile</a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>
</div>
