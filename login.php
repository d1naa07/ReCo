<?php

session_start();
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM Users
WHERE email='$email' AND password='$password'";

$result = $conn->query($sql);

if($result->num_rows > 0){

    $_SESSION['email'] = $email;

    header("Location: profile.php");
    exit();

} else {

    echo "Invalid email or password";

}

?>