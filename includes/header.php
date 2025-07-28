<?php

error_reporting(E_ALL);
require_once 'config/class.user.php';
$user = new USER();
$is_logged_in = $user->is_logged_in();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GrowNet</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" crossorigin="anonymous">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Font Awesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    
</head>
<body>

<div class="bg-light fixed-top custom-header">
    <!-- Header Start -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand text-uppercase font-weight-bold" href="index.php">GrowNet</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <!-- Navbar Items Start -->
                <ul class="navbar-nav mx-auto text-center">
                    <li class="nav-item"><a href="index.php" class="nav-link"><span class="link-text">Home</span></a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="link-text">About Us<i class="fas fa-chevron-down ml-1 dropdown-arrow"></i></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="aboutDropdown">
                            <a class="dropdown-item" href="partners.html">Vision & Mission</a>
                            <a class="dropdown-item" href="testimonial.html">Management</a>
                            <a class="dropdown-item" href="support.html">Advisors</a>
                            <a class="dropdown-item" href="faqs.html">Join Us</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="link-text">Our Services<i class="fas fa-chevron-down ml-1 dropdown-arrow"></i></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="servicesDropdown">
                            <a class="dropdown-item" href="partners.html">What We Do</a>
                            <a class="dropdown-item" href="support.html">Our Process</a>
                        </div>
                    </li>
                    <li class="nav-item"><a href="index.php?page=project" class="nav-link"><span class="link-text">Projects</span></a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="link-text">Pages<i class="fas fa-chevron-down ml-1 dropdown-arrow"></i></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                            <a class="dropdown-item" href="#partners">Partnerships</a>
                            <a class="dropdown-item" href="testimonial.html">Testimonial</a>
                            <a class="dropdown-item" href="support.html">Support Center</a>
                            <a class="dropdown-item" href="faqs.html">FAQs</a>
                        </div>
                    </li>
                    <li class="nav-item"><a href="contact.html" class="nav-link"><span class="link-text">Contact</span></a></li>
                </ul>
                <!-- Navbar Items End -->

                <!-- Sign Out & Sign In/Sign Up -->
                <?php if ($is_logged_in): ?>
                <ul class="navbar-nav text-center" id="signOut">
                    <li class="nav-item">
                        <a href="javascript:void(0)" onclick="logoutUser()" class="btn btn-outline-success custom-btn mr-2">Sign Out</a>
                    </li>
                </ul>
                
<script>
function logoutUser() {
    if (confirm('Are you sure you want to logout?')) {
        // Create a form to submit the logout request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'logout';
        input.value = 'true';
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
                <?php else: ?>
                <ul class="navbar-nav text-center" id="signIn">
                    <li class="nav-item"><a href="index.php?page=relog" class="btn btn-outline-success custom-btn mr-2">Sign In / Sign Up</a></li>
                </ul>
                <?php endif; ?>
                
            </div>
        </div>
    </nav>

    <!-- Search Bar Under Navbar -->
    <div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-2"></div>
        <!-- Search Bar (centered) -->
        <div class="col-md-8">
            <form class="form-inline d-flex justify-content-center mb-2">
                <input class="form-control mr-2 w-75" type="search" placeholder="Search Projects" aria-label="Search">
                <button class="btn btn-success" type="submit">Search</button>
            </form>
        </div>

        <!-- Icons on the right -->
        <div class="col-md-2 text-right">
        <?php if ($is_logged_in): ?>
            <a href="#notifications" class="text-dark mr-2">
            <i class="fa fa-bell" style="font-size: 20px;"></i>
            </a>
            <a href="index.php?page=dashboard" class="text-dark">
            <i class="fa fa-user-circle" style="font-size: 20px;"></i>
            </a>
        <?php endif; ?>
        </div>
    </div>
    </div>


</div>
<!-- Header End -->
