<?php

include 'db.php';
session_start();

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

$check = "SELECT * FROM Users WHERE email='$email'";
$result = $conn->query($check);

if($result->num_rows > 0){

    echo "Email already exists!";

} else {

    $sql = "INSERT INTO Users (name, email, password)
    VALUES ('$name', '$email', '$password')";

    if($conn->query($sql) === TRUE){

        $_SESSION['email'] = $email;

header("Location: profile.php");
exit();
        exit();

    } else {

        echo "Error: " . $conn->error;

    }
}

?>