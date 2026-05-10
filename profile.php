<?php

session_start();
include 'db.php';

if(!isset($_SESSION['email'])){
    header("Location: login.html");
    exit();
}

$email = $_SESSION['email'];

$sql = "SELECT * FROM Users WHERE email='$email'";
$result = $conn->query($sql);

$user = $result->fetch_assoc();
$user_id = $user['user_id'];
$points = $user['points'];

$level = floor($points / 100) + 1;

$nextLevelPoints = $level * 100;

$currentLevelProgress = $points % 100;

// ✅ FIX: orders query AFTER user_id exists
$ordersQuery = "SELECT * FROM RecycleOrders WHERE user_id='$user_id' ORDER BY order_date DESC";
$ordersResult = $conn->query($ordersQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ReCo - User Dashboard</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:"Poppins", sans-serif;
}

body{
    background:linear-gradient(135deg,#D5FFF5,#A8FFBD);
    min-height:100vh;
}

header{
    background:#003B30;
    padding:15px 20px;
}

.logo{
    color:white;
    text-decoration:none;
    font-size:1.5rem;
    font-weight:600;
}

.profile-header{
    display:flex;
    align-items:center;
    gap:15px;
    padding:20px;
}

.avatar-wrapper{
    position:relative;
    width:70px;
    height:70px;
}

.profile-pic{
    width:70px;
    height:70px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #003B30;
    cursor:pointer;
}

.avatar-wrapper input{
    display:none;
}

.username{
    font-size:1.3rem;
    font-weight:600;
    color:#003B30;
}

.container{
    padding:20px;
}

.dashboard-box{
    display:flex;
    background:white;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
    overflow:hidden;
    min-height:70vh;
}

.sidebar{
    width:220px;
    background:#f4fff7;
    padding:15px;
}

.tab{
    padding:12px;
    margin-bottom:10px;
    cursor:pointer;
    border-radius:10px;
    background:#e9f7ee;
}

.tab.active{
    background:#003B30;
    color:white;
}

.content{
    flex:1;
    padding:20px;
}

.section{
    display:none;
}

.section.active{
    display:block;
}

input{
    width:100%;
    padding:10px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid #ccc;
}

button{
    padding:10px;
    border:none;
    border-radius:20px;
    background:#003B30;
    color:white;
    cursor:pointer;
}

.recycle-item{
    padding:10px;
    background:#f5fff7;
    margin-bottom:10px;
    border-radius:10px;
}
</style>
</head>

<body>

<header>
    <a href="index.html" class="logo">🍀 ReCo Dashboard</a>
</header>

<div class="profile-header">

    <form action="upload_pfp.php" method="POST" enctype="multipart/form-data">

    <label class="avatar-wrapper">

        <img
            class="profile-pic"
            id="preview"
            src="<?php echo !empty($user['profile_picture']) ? $user['profile_picture'] : 'https://via.placeholder.com/70'; ?>"
        >

        <input
            type="file"
            name="pfp"
            accept="image/*"
            onchange="this.form.submit()"
        >

    </label>

</form>

    <div>

    <div class="username">
        <?php echo $user['name']; ?>
    </div>

    <div style="margin-top:6px; width:250px;">

        <div style="
            display:flex;
            justify-content:space-between;
            font-size:13px;
            margin-bottom:4px;
            color:#003B30;
            font-weight:500;
        ">
            <span>Level <?php echo $level; ?></span>
            <span><?php echo $points; ?> XP</span>
        </div>

        <div style="
            width:100%;
            height:12px;
            background:#dfeee4;
            border-radius:20px;
            overflow:hidden;
        ">

            <div style="
                width:<?php echo $currentLevelProgress; ?>%;
                height:100%;
                background:#00b86b;
                border-radius:20px;
                transition:0.4s;
            "></div>

        </div>

    </div>

</div>

</div>

<div class="container">

<div class="dashboard-box">

    <div class="sidebar">
        <div class="tab active" onclick="showTab('profile')">Profile</div>
        <div class="tab" onclick="showTab('recycles')">My Recycles</div>
        <div class="tab" onclick="showTab('settings')">Settings</div>
    </div>

    <div class="content">

        <!-- PROFILE -->
        <div id="profile" class="section active">

            <h2>Personal Info</h2>

            <form action="update_profile.php" method="POST">

                <input type="text" name="name" value="<?php echo $user['name']; ?>">
                <input type="email" name="email" value="<?php echo $user['email']; ?>">
                <input type="text" name="phone" value="<?php echo $user['phone']; ?>">
                <input type="text" name="address" value="<?php echo $user['address']; ?>">

                <button type="submit">Save</button>

            </form>

        </div>

        <!-- RECYCLES -->
        <div id="recycles" class="section">
            <h2>Previous Recycles</h2>

            <?php if($ordersResult && $ordersResult->num_rows > 0) { ?>

                <?php while($order = $ordersResult->fetch_assoc()) { ?>

                    <div class="recycle-item">

                        <h3>Order #<?php echo $order['order_id']; ?></h3>
                        <p>Status: <?php echo $order['status']; ?></p>
                        <p>Total: $<?php echo $order['total_price']; ?></p>
                        <p>Date: <?php echo $order['order_date']; ?></p>

                        <hr>
                        <b>Items:</b><br>

                        <?php
                        $order_id = $order['order_id'];

                        $itemsQuery = "
                            SELECT oi.*, m.material_name
                            FROM OrderItems oi
                            JOIN Materials m ON oi.material_id = m.material_id
                            WHERE oi.order_id = '$order_id'
                        ";

                        $itemsResult = $conn->query($itemsQuery);

                        while($item = $itemsResult->fetch_assoc()) {
                        ?>

                            <p>
                                • <?php echo $item['material_name']; ?>
                                - <?php echo $item['weight']; ?> kg
                                - $<?php echo $item['subtotal']; ?>
                            </p>

                        <?php } ?>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <p>No recycles yet.</p>

            <?php } ?>

        </div>

        <!-- SETTINGS -->
        <div id="settings" class="section">
            <h2>Settings</h2>
            <a href="logout.php"><button>Logout</button></a>
                
               
        </div>

    </div>

</div>

</div>

<script>
function showTab(tab){

    document.querySelectorAll('.section').forEach(s=>{
        s.classList.remove('active');
    });

    document.querySelectorAll('.tab').forEach(t=>{
        t.classList.remove('active');
    });

    document.getElementById(tab).classList.add('active');

    document.querySelector(`[onclick="showTab('${tab}')"]`).classList.add('active');
}

function loadImage(event){
    const image = document.getElementById('preview');
    image.src = URL.createObjectURL(event.target.files[0]);
}
</script>

</body>
</html>