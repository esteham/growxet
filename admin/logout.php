<?php
session_start();
require_once 'config/class.user.php';

$user = new USER();
$user->adminLogout();

// Redirect to login page after logout
header('Location: login.php?msg=logged_out');

?>