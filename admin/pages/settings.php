<?php
require_once 'config/class.user.php';
$user = new USER();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$adminId = $_SESSION['userSession'] ?? null;
$msg = "";

// Fetch current admin info
$admin = $user->getUserById($adminId);

if (!$admin) {
    die('<div class="alert alert-danger">Admin user not found in database.</div>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f_name = trim($_POST['f_name'] ?? '');
    $user_email = trim($_POST['user_email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate
    if (empty($f_name) || empty($user_email) || empty($contact)) {
        $msg = '<div class="alert alert-danger">All fields except password are required.</div>';
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<div class="alert alert-danger">Invalid email address.</div>';
    } elseif (!empty($password) && $password !== $confirm_password) {
        $msg = '<div class="alert alert-danger">Passwords do not match.</div>';
    } else {
        try {
            // Update info
            $params = [
                ':f_name' => $f_name,
                ':user_email' => $user_email,
                ':contact' => $contact,
                ':id' => $adminId
            ];
            
            $sql = "UPDATE users SET f_name = :f_name, user_email = :user_email, contact = :contact";
            
            if (!empty($password)) {
                $sql .= ", user_pass = :user_pass";
                $params[':user_pass'] = password_hash($password, PASSWORD_DEFAULT);
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $user->runQuery($sql);
            if ($stmt->execute($params)) {
                $msg = '<div class="alert alert-success">Profile updated successfully.</div>';
                // Refresh admin info
                $admin = $user->getUserById($adminId);
                
                // Update session variables if email or name changed
                $_SESSION['user_email'] = $user_email;
                $_SESSION['user_name'] = $admin['user_name'];
            } else {
                $msg = '<div class="alert alert-danger">Update failed. Please try again.</div>';
            }
        } catch (PDOException $e) {
            $msg = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}
?>

<div class="container mt-4" style="max-width:500px;">
    <h3 class="mb-4">Admin Settings</h3>
    <?= $msg ?>
    <form method="post" autocomplete="off">
        <div class="form-group">
            <label for="f_name">Full Name</label>
            <input type="text" class="form-control" id="f_name" name="f_name" 
                   value="<?= htmlspecialchars($admin['f_name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="user_email">Email</label>
            <input type="email" class="form-control" id="user_email" name="user_email" 
                   value="<?= htmlspecialchars($admin['user_email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="contact">Contact</label>
            <input type="text" class="form-control" id="contact" name="contact" 
                   value="<?= htmlspecialchars($admin['contact']) ?>" required>
        </div>
        <div class="form-group">
            <label for="password">New Password <small class="text-muted">(leave blank to keep unchanged)</small></label>
            <input type="password" class="form-control" id="password" name="password" 
                   autocomplete="new-password" minlength="6">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                   autocomplete="new-password" minlength="6">
        </div>
        <button type="submit" class="btn btn-primary">Update Settings</button>
        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
    </form>
</div>