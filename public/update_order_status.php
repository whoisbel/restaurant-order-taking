<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Establish a database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for errors in the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the request body and decode the JSON data
$request_body = file_get_contents('php://input');
$data = json_decode($request_body);

// Get the order ID and status from the data
$order_id = $data->order_id ?? null;
$status = $data->status ?? null;

// Debugging statements
echo "Order ID: $order_id<br>";
echo "Status: $status<br>";

// Check if order ID and status are present in the data
if (!$order_id || !$status) {
    die("Invalid request: missing order ID or status");
}

// Update the order status in the database
$sql = "UPDATE orders SET status='$status' WHERE order_id='$order_id'";

if ($conn->query($sql) === TRUE) {
    echo "Order status updated successfully";
} else {
    echo "Error updating order status: " . $conn->error;
}

// Close the database connection
$conn->close();

?>
