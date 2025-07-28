<?php
// Database connection
$host = 'localhost';
$db = 'grownet';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Admin user details
$user_name = 'admin';
$f_name = 'Admin User';
$user_email = 'admin@grownet.com';
$user_pass = password_hash('admin123', PASSWORD_DEFAULT); // Set your desired password
$uniqid = uniqid('adm_');
$account_status = 'active';
$is_online = 0;
$contact = '0000000000';

// Insert into users table
$stmt = $conn->prepare("INSERT INTO users (uniqid, user_name, f_name, user_email, user_pass, account_status, is_online, contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssis", $uniqid, $user_name, $f_name, $user_email, $user_pass, $account_status, $is_online, $contact);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;

    // Get admin type_id from user_types
    $type_stmt = $conn->prepare("SELECT id FROM user_types WHERE type_name = 'admin' LIMIT 1");
    $type_stmt->execute();
    $type_stmt->bind_result($type_id);
    $type_stmt->fetch();
    $type_stmt->close();

    // Insert into user_type_map
    if ($type_id) {
        $map_stmt = $conn->prepare("INSERT INTO user_type_map (user_id, type_id) VALUES (?, ?)");
        $map_stmt->bind_param("ii", $user_id, $type_id);
        $map_stmt->execute();
        $map_stmt->close();
    }

    // Success message and redirect to login page after 2 seconds
    echo "<div style='color:green;'>Admin created successfully with email: $user_email and uniqid: $uniqid<br>Redirecting to login page...</div>";
    echo "<script>setTimeout(function(){ window.location.href = 'login.php'; }, 2000);</script>";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();