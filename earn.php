
<?php
session_start();

if(!isset($_SESSION['email'])){
    header("Location: login.html");
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReCo - Start Earning</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:"Poppins", sans-serif;
        }

        body{
            background: linear-gradient(135deg, #D5FFF5, #A8FFBD);
            min-height:100vh;
            padding:20px;
        }

        .header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:30px;
        }

        .logo{
            font-size:1.8rem;
            font-weight:600;
            color:#003B30;
            text-decoration:none;
        }

        .container{
            display:flex;
            gap:30px;
            flex-wrap:wrap;
        }

        .box{
            background:white;
            padding:20px;
            border-radius:15px;
            box-shadow:0 10px 20px rgba(0,0,0,0.1);
            flex:1;
            min-width:280px;
        }

        h2{
            color:#003B30;
            margin-bottom:15px;
        }

        label{
            display:block;
            margin-top:10px;
            font-weight:500;
        }

        input, select{
            width:100%;
            padding:10px;
            margin-top:5px;
            border-radius:8px;
            border:1px solid #ccc;
        }

        button{
            margin-top:10px;
            width:100%;
            padding:10px;
            border:none;
            border-radius:25px;
            background:#003B30;
            color:white;
            cursor:pointer;
            transition:0.3s;
        }

        button:hover{
            background:#A8FFBD;
            color:#003B30;
        }

        .item{
            background:#f5fff7;
            padding:10px;
            margin-top:10px;
            border-radius:10px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .remove{
    background:#2f2f2f;
    color:#fff;
    border:none;
    border-radius:6px;
    padding:2px 6px;
    cursor:pointer;
    width:22px;
    height:22px;
    font-size:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:0.2s ease;
}

.remove:hover{
    background:#555;
    transform:scale(1.1);
}

        .total{
            margin-top:20px;
            font-size:1.5rem;
            font-weight:600;
            color:#003B30;
        }

        .actions{
            margin-top:10px;
            display:flex;
            gap:10px;
        }

        .sell{
            background:#007a3d;
        }

        .clear{
            background:#444;
        }
    </style>
</head>
<body>

<div class="header">
    <a href="index.html" class="logo">🍀 ReCo</a>
</div>

<div class="container">

    <!-- INPUT SECTION -->
    <div class="box">
        <h2>Sell Materials</h2>

        <label>Material</label>
        <select id="material">
           <option value="1" data-price="2">Wood ($2/kg)</option>
<option value="2" data-price="1">Paper ($1/kg)</option>
<option value="3" data-price="3">Glass ($3/kg)</option>
<option value="4" data-price="5">Metal ($5/kg)</option>
        </select>

        <label>Weight (kg)</label>
        <input type="number" id="kg" placeholder="Enter kg">

        <button onclick="addToCart()">Add to Basket</button>
    </div>

    <!-- CART SECTION -->
    <div class="box">
        <h2>Your Basket</h2>
        <div id="cart"></div>

        <div class="total">Total: $<span id="total">0</span></div>

        <div class="actions">
            <button class="sell" onclick="sellAll()">Sell All</button>
            <button class="clear" onclick="clearCart()">Clear Cart</button>
        </div>
    </div>

</div>

<script>
    let cart = [];

   function addToCart(){
    const materialSelect = document.getElementById("material");
    const kg = parseFloat(document.getElementById("kg").value);

    if(!kg || kg <= 0) return;

    const material_id = parseInt(materialSelect.value);

    cart.push({
        material_id: material_id,
        weight: kg
    });

    updateCart();
}

    function updateCart(){
    const cartDiv = document.getElementById("cart");
    cartDiv.innerHTML = "";

    let total = 0;

    cart.forEach((item, index) => {

        const pricePerKg = 1; // temporary visual only

        const subtotal = item.weight * pricePerKg;
        total += subtotal;

        cartDiv.innerHTML += `
            <div class="item">
                <div>
                    <b>Material ID: ${item.material_id}</b><br>
                    ${item.weight} kg
                </div>
                <button class="remove" onclick="removeItem(${index})">X</button>
            </div>
        `;
    });

    document.getElementById("total").innerText = total.toFixed(2);
}

    function removeItem(index){
        cart.splice(index, 1);
        updateCart();
    }

    function clearCart(){
        cart = [];
        updateCart();
    }

    function sellAll(){

    fetch("sell.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            cart: cart
        })
    })
    .then(res => res.text())
    .then(data => {
    console.log(data);

    // redirect to success page
    window.location.href = "success.php?msg=" + encodeURIComponent(data);
})
    .catch(err => {
        console.error(err);
        alert("Error sending order");
    });

}
</script>

</body>
</html>