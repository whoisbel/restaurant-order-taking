<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// MySQL credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT o.order_id, o.customer_id, o.total, o.payment_method, o.order_date, o.order_time, o.status,
               d.Detail_ID, d.Product_Name, d.Product_ID, d.Quantity, d.Product_Image, d.Price
        FROM orders o
        INNER JOIN order_details d ON o.order_id = d.Order_ID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Convert result set to associative array
    $orders = array();
    while ($row = $result->fetch_assoc()) {
        $order_id = $row['order_id'];
        if (!isset($orders[$order_id])) {
            $orders[$order_id] = array(
                'order_id' => $order_id,
                'customer_id' => $row['customer_id'],
                'total' => $row['total'],
                'payment_method' => $row['payment_method'],
                'order_date' => $row['order_date'],
                'order_time' => $row['order_time'],
                'status' => $row['status'],
                'details' => array()
            );
        }
        $orders[$order_id]['details'][] = array(
            'Detail_ID' => $row['Detail_ID'],
            'Product_Name' => $row['Product_Name'],
            'Product_ID' => $row['Product_ID'],
            'Quantity' => $row['Quantity'],
            'Product_Image' => $row['Product_Image'],
            'Price' => $row['Price']
        );
    }
    echo json_encode(array_values($orders));
} else {
    echo "0 results";
}

$conn->close();
?>
