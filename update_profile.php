<?php

session_start();
include 'db.php';

if(!isset($_SESSION['email'])){
    header("Location: login.html");
    exit();
}

$email = $_SESSION['email'];

$name = $_POST['name'];
$phone = $_POST['phone'];
$address = $_POST['address'];

$sql = "UPDATE Users 
SET name='$name',
phone='$phone',
address='$address'
WHERE email='$email'";

$conn->query($sql);

header("Location: profile.php");
exit();

?>