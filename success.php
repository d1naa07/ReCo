<?php
session_start();

if(!isset($_SESSION['email'])){
    header("Location: login.html");
    exit();
}

$msg = $_GET['msg'] ?? "Order completed successfully!";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Success</title>
    <style>
        body{
            background: linear-gradient(135deg,#D5FFF5,#A8FFBD);
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            font-family:Poppins;
        }

        .box{
            background:white;
            padding:40px;
            border-radius:20px;
            text-align:center;
            box-shadow:0 10px 30px rgba(0,0,0,0.2);
            animation: pop 0.5s ease;
        }

        @keyframes pop{
            from{ transform:scale(0.7); opacity:0; }
            to{ transform:scale(1); opacity:1; }
        }

        h1{
            color:#003B30;
        }

        p{
            margin-top:10px;
        }

        a{
            display:inline-block;
            margin-top:20px;
            padding:10px 20px;
            background:#003B30;
            color:white;
            border-radius:20px;
            text-decoration:none;
        }
    </style>
</head>
<body>

<div class="box">
    <h1>✔ Success!</h1>
    <p><?php echo htmlspecialchars($msg); ?></p>

    <a href="index.html">Back to Home</a>
</div>

</body>
</html>