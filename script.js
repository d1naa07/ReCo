function calculatePrice() {
    const material = document.getElementById("material").value;
    const weightKg = parseFloat(document.getElementById("weight").value);

    if (isNaN(weightKg) || weightKg <= 0) {
        document.getElementById("result").innerText = "Enter valid weight";
        return;
    }

    let pricePerKg = 0;

    if (material === "glass") pricePerKg = 0.3;
    if (material === "paper") pricePerKg = 0.1;
    if (material === "wood") pricePerKg = 0.4;
    if (material === "metal") pricePerKg = 0.5;

    const totalPrice = weightKg * pricePerKg;

    document.getElementById("result").innerText = "$" + totalPrice.toFixed(2);
}