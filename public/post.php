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

$data = json_decode(file_get_contents('php://input'), true);

$orderID = $data['orderID'];
$customerID = $data['customerID'];
$order = $data['order'];
$total = $data['total'];
$payment = $data['payment'];
$date = $data['date'];
$time = $data['time'];
$status = $data['status'];

// Insert order into orders table
$sql = "INSERT INTO orders (order_id, customer_id, total, payment_method, order_date, order_time, status) 
        VALUES ('$orderID', '$customerID', $total, '$payment', '$date', '$time', '$status')";

if ($conn->query($sql) !== TRUE) {
    $response = "Error adding order: " . $conn->error;
    $conn->close();
    echo json_encode($response);
    exit();
}


// Insert order details into order_details table
foreach ($order as $product) {
    $productName = $product['name'];
    $productID = $product['id'];
    $quantity = $product['quantity'];
    $productImage = $product['img'];
    $price = $product['price'];
    
    $sql = "INSERT INTO order_details (Order_ID, Product_Name, Product_ID, Quantity, Product_Image, Price) 
        VALUES ('$orderID', '$productName', $productID, $quantity, '$productImage', $price)";

    if ($conn->query($sql) !== TRUE) {
        $response = "Error adding order details: " . $conn->error;
        $conn->close();
        echo json_encode($response);
        exit();
    }
}


$response = "Order added successfully";
$conn->close();
echo json_encode($response);

?>
