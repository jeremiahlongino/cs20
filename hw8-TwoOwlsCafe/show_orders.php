<!DOCTYPE html>
<html>
<head><title>Two Owls Cafe - All Orders</title></head>
<body>

<?php include 'header.php'; ?>

<h2>All Orders</h2>

<?php
$conn = new mysqli("localhost", "ujw7swqsub1wa", "2ej6f+12(ED1", "dbnl7o6hdwkb2c");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$orders = $conn->query("SELECT * FROM orders ORDER BY order_datetime DESC");

if ($orders->num_rows === 0) {
    echo "<p>No orders yet.</p>";
} else {
    while ($order = $orders->fetch_assoc()) {
        /* Display order details */
        echo "<div style='border:1px solid #f0cca0; padding:14px; margin-bottom:16px; border-radius:4px;'>";
        echo "<p><strong>Order #" . $order['id'] . "</strong> &mdash; " . $order['order_datetime'] . "</p>";
        echo "<p>Customer: " . htmlspecialchars($order['first_name']) . " " . htmlspecialchars($order['last_name']) . "</p>";
        echo "<p>Pickup Time: " . htmlspecialchars($order['pickup_time']) . "</p>";
        echo "<p>Special Instructions: " . htmlspecialchars($order['special_instructions']) . "</p>";

        $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->bind_param("i", $order['id']);
        $stmt->execute();
        $items = $stmt->get_result();
        $stmt->close();

        echo "<ul>";

        /* Loop through items for this order */
        while ($item = $items->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($item['item_name']) . " &times;" . $item['quantity'] . "</li>";
        }
        echo "</ul></div>";
    }
}
$conn->close();
?>
</body>
</html>