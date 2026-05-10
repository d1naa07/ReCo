<?php
session_start();
include 'db.php';

if(!isset($_SESSION['email'])){
    header("Location: login.html");
    exit();
}

$email = $_SESSION['email'];

$userResult = $conn->query("SELECT * FROM Users WHERE email='$email'");
$user = $userResult->fetch_assoc();
$user_id = $user['user_id'];

if(isset($_FILES['pfp'])){

    $file = $_FILES['pfp'];

    $name = time() . "_" . basename($file["name"]);
    $target = "uploads/" . $name;

    if(!is_dir("uploads")){
        mkdir("uploads");
    }

    move_uploaded_file($file["tmp_name"], $target);

    $conn->query("
        UPDATE Users
        SET profile_picture='$target'
        WHERE user_id='$user_id'
    ");
}

header("Location: profile.php");
exit();
?>