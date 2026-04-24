<!DOCTYPE html>
<html>
<head><title>Two Owls Cafe - Process Order</title></head>
<body>

<?php include 'header.php'; ?>

<?php
$conn = new mysqli("localhost", "ujw7swqsub1wa", "2ej6f+12(ED1", "dbnl7o6hdwkb2c");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$quantities   = isset($_GET['qty'])          ? $_GET['qty']                           : [];
$first        = isset($_GET['first_name'])   ? htmlspecialchars($_GET['first_name'])  : '';
$last         = isset($_GET['last_name'])    ? htmlspecialchars($_GET['last_name'])   : '';
$instructions = isset($_GET['instructions']) ? htmlspecialchars($_GET['instructions']): '';
$pickup_time  = isset($_GET['pickup_time'])  ? htmlspecialchars($_GET['pickup_time']) : '';

$subtotal = 0;
$ordered_items = [];

/* Loop through ordered items and fetch details from db */
foreach ($quantities as $id => $qty) {
    $qty = (int)$qty;
    if ($qty > 0) {
        $stmt = $conn->prepare("SELECT * FROM menu WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $item_total = $item['price'] * $qty;
        $subtotal  += $item_total;
        $ordered_items[] = [
            'name'       => $item['name'],
            'quantity'   => $qty,
            'price'      => $item['price'],
            'item_total' => $item_total
        ];
    }
}

/* Save order to table */
$stmt = $conn->prepare(
    "INSERT INTO orders (first_name, last_name, special_instructions, pickup_time, order_datetime)
     VALUES (?, ?, ?, ?, NOW())"
);
$stmt->bind_param("ssss", $_GET['first_name'], $_GET['last_name'], $_GET['instructions'], $_GET['pickup_time']);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

/* Save each item to order items */
foreach ($ordered_items as $item) {
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $order_id, $item['name'], $item['quantity']);
    $stmt->execute();
    $stmt->close();
}

/* Display ordered items */
foreach ($ordered_items as $item) {
    echo "<p><strong>" . htmlspecialchars($item['name']) . "</strong><br>";
    echo "Quantity: " . $item['quantity'] . "<br>";
    echo "Price: $" . number_format($item['price'], 2) . "<br>";
    echo "Total for item: $" . number_format($item['item_total'], 2) . "</p><hr>";
}

/* Calculate taxs and total */
$tax   = $subtotal * 0.0625;
$total = $subtotal + $tax;

echo "<p>Subtotal: $"    . number_format($subtotal, 2) . "</p>";
echo "<p>Tax (6.25%): $" . number_format($tax, 2)      . "</p>";
echo "<p>Total: $"       . number_format($total, 2)    . "</p>";
echo "<p>Pickup Time: "  . $pickup_time                . "</p>";
echo "<p>Name: $first $last</p>";
echo "<p>Special Instructions: $instructions</p>";

$conn->close();
?>
</body>
</html>