<?php
session_start();
require_once 'config/class.user.php';
$user = new USER();

if ($user->is_logged_in()) {
    $user->redirect('index.php');
}

$loginError = '';
if (isset($_POST['btn-login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $result = $user->adminLogin($email, $password);

    if ($result === true) {
        $user->redirect('index.php');
    } elseif ($result === 'inactive') {
        $loginError = "Your account is inactive. Please contact admin.";
    } elseif ($result === 'invalid') {
        $loginError = "Invalid email or password!";
    } elseif ($result === 'not_found') {
        $loginError = "No admin account found with that email.";
    } else {
        $loginError = "Login failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - GrowNet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS (CDN) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark text-white">
    <div class="container d-flex justify-content-center align-items-center h-100">
        <div class="login-container bg-transparent shadow">
            <h3 class="text-center login-title text-white mb-4">Admin Login</h3>
            <?php if ($loginError): ?>
                <div class="alert alert-danger"><?= $loginError ?></div>
            <?php endif; ?>
            <form method="post" autocomplete="off">
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" name="email" class="form-control" id="email" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                </div>
                <button type="submit" name="btn-login" class="btn btn-success btn-block">Login</button>
            </form>
        </div>
    </div>
</body>
</html>