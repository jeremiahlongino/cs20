<!DOCTYPE html>
<html>
<head>
    <title>Two Owls Cafe - Order Confirmation</title>
</head>
<body>

<!-- Cafe Header -->
<?php include 'header.php'; ?>
 
<!-- Form uses GET method, submits to process_order.php -->
<form method="get" action="process_order.php" id="orderForm">
 
<?php

/* Connects to the database */
$conn = new mysqli("localhost", "ujw7swqsub1wa", "2ej6f+12(ED1", "dbnl7o6hdwkb2c");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
/* Fetch all menu items */
$sql = "SELECT * FROM menu";
$result = $conn->query($sql);
 
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='menu-item'>";
        echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        echo "<p>Price: $" . number_format($row['price'], 2) . "</p>";
        echo "<img src='/" . htmlspecialchars($row['image']) . "' width='150'><br>";
        
        /* Want up to ten items, get by item id */
        echo "<div class='quantity'>";
        echo "<label>Quantity:</label>";
        echo "<select name='qty[" . $row['id'] . "]'>";
        for ($i = 0; $i <= 10; $i++) {
            echo "<option value='$i'>$i</option>";
        }
        echo "</select>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p>No menu items found.</p>";
}
$conn->close();
?>
 
<!-- Customer info fields -->
First Name: <input type="text" name="first_name"><br><br>
Last Name: <input type="text" name="last_name"><br><br>
Special Instructions: <textarea name="instructions"></textarea><br><br>
 
<!-- Javascript handles pickup time -->
<input type="hidden" name="pickup_time" id="pickup_time">
 
<input type="submit" value="Submit Order">
 
</form>
 
<script>
document.getElementById("orderForm").onsubmit = function() {
 
    /* At least one item ordered */
    let quantities = document.querySelectorAll("select[name^='qty']");
    let totalQty = 0;
    quantities.forEach(q => { totalQty += parseInt(q.value); });
    if (totalQty === 0) {
        alert("Please order at least one item!");
        return false;
    }
 
    /* First and last name required */
    let first = document.querySelector("[name='first_name']").value.trim();
    let last  = document.querySelector("[name='last_name']").value.trim();
    if (first === "" || last === "") {
        alert("Please enter your first and last name!");
        return false;
    }
 
    /* Calculate pickup time: 20 minutes from now */
    let pickup = new Date();
    pickup.setMinutes(pickup.getMinutes() + 20);
    document.getElementById("pickup_time").value = pickup.toLocaleTimeString();
 
    return true; 
};
</script>
</body>
</html>