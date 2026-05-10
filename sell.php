<?php

session_start();
include 'db.php';

if(!isset($_SESSION['email'])){
    header("Location: login.html");
    exit();
}

$email = $_SESSION['email'];

// get user
$userResult = $conn->query("SELECT * FROM Users WHERE email='$email'");
$user = $userResult->fetch_assoc();

if(!$user){
    die("User not found in DB");
}

$user_id = $user['user_id'];

// get cart JSON
$data = json_decode(file_get_contents("php://input"), true);

// DEBUG 1: check if cart arrives
if(!$data){
    die("NO JSON RECEIVED (frontend problem)");
}

if(!isset($data['cart'])){
    die("CART KEY MISSING");
}

$cart = $data['cart'];

if(empty($cart)){
    die("CART IS EMPTY");
}

$total_price = 0;

// -------------------------
// CALCULATE TOTAL
// -------------------------
foreach($cart as $item){

    $material_id = intval($item['material_id'] ?? 0);
    $weight = floatval($item['weight'] ?? 0);

    // DEBUG 2: check values
    if($material_id == 0){
        die("INVALID MATERIAL ID IN CART");
    }

    $result = $conn->query("
        SELECT price_per_kg 
        FROM Materials 
        WHERE material_id = $material_id
    ");

    $material = $result->fetch_assoc();

    if(!$material){
        die("MATERIAL NOT FOUND IN DB (ID: $material_id)");
    }

    $price_per_kg = floatval($material['price_per_kg']);

    // DEBUG 3: price check
    if($price_per_kg == 0){
        die("PRICE IS 0 FOR MATERIAL ID: $material_id");
    }

    $subtotal = $weight * $price_per_kg;

    $total_price += $subtotal;
}

// -------------------------
// CREATE ORDER
// -------------------------
$conn->query("
    INSERT INTO RecycleOrders (user_id, status, total_price, order_date)
    VALUES ($user_id, 'Pending', $total_price, NOW())
");

$order_id = $conn->insert_id;

// -------------------------
// INSERT ITEMS
// -------------------------
foreach($cart as $item){

    $material_id = intval($item['material_id']);
    $weight = floatval($item['weight']);

    $result = $conn->query("
        SELECT price_per_kg 
        FROM Materials 
        WHERE material_id = $material_id
    ");

    $material = $result->fetch_assoc();

    $price_per_kg = floatval($material['price_per_kg']);
    $subtotal = $weight * $price_per_kg;

    $conn->query("
        INSERT INTO OrderItems (order_id, material_id, weight, subtotal)
        VALUES ($order_id, $material_id, $weight, $subtotal)
    ");
}

$conn->query("
    UPDATE Users
    SET points = points + $total_price
    WHERE user_id = '$user_id'
");

echo "Your recycle order was submitted successfully! Total earned: $" . $total_price . ". Our delivery team will contact you through your phone number to schedule the pickup date and time.";

?>