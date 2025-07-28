<?php
require_once '../../config/class.user.php';
session_start();

$user = new USER();

if (!isset($_SESSION['userSession'])) {
    header("Location: ../../index.php?page=relog&msg=signin_required");
    exit();
}

$uid = $_SESSION['userSession'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['user_img'])) {
    $img = $_FILES['user_img'];

    if ($img['error'] === 0 && in_array($img['type'], ['image/jpeg', 'image/png', 'image/jpg'])) {
        $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $uid . '_' . time() . '.' . $ext;
        $destination = '../../assets/profile/' . $filename;

        if (!is_dir('../../assets/profile')) {
            mkdir('../../assets/profile', 0777, true);
        }

        if (move_uploaded_file($img['tmp_name'], $destination)) {
            $relativePath = 'assets/profile/' . $filename;

            $stmt = $user->runQuery("UPDATE users SET user_img = :pic WHERE id = :uid");
            $stmt->execute([':pic' => $relativePath, ':uid' => $uid]);

            header("Location: ../../index.php?page=dashboard&pages=profile&msg=upload_success");
            exit();
        } else {
            echo "Error moving file.";
        }
    } else {
        echo "Invalid image type or upload error.";
    }
} else {
    echo "No file selected.";
}
