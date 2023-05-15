<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

// Read input data
$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderId'];
$status = $data['status'];

// Update order status in database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restobar";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "UPDATE orders SET status='$status' WHERE OrderID='$orderId'";

if ($conn->query($sql) === TRUE) {
  echo json_encode(array("success" => true));
} else {
  echo json_encode(array("success" => false));
}

$conn->close();
?>
