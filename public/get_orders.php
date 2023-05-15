<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restobar";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get orders
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

if ($status == 'all') {
    $sql = "SELECT Orders.OrderID, Orders.OrderDate, Orders.OrderTime, Payment.PaymentMethod, Payment.Total, Customers.CustomerName, Customers.CustomerID, Orders.Status as OrderStatus, OrderDetails.MenuItem, OrderDetails.Quantity
            FROM Orders
            JOIN Payment ON Orders.OrderID = Payment.PaymentID
            JOIN Customers ON Payment.CustomerID = Customers.CustomerID
            JOIN OrderDetails ON Orders.OrderID = OrderDetails.OrderID
            ORDER BY Orders.OrderDate DESC, Orders.OrderTime DESC";
} else {
    $sql = "SELECT Orders.OrderID, Orders.OrderDate, Orders.OrderTime, Payment.PaymentMethod, Payment.Total, Customers.CustomerName, Customers.CustomerID, Orders.Status as OrderStatus, OrderDetails.MenuItem, OrderDetails.Quantity
            FROM Orders
            JOIN Payment ON Orders.OrderID = Payment.PaymentID
            JOIN Customers ON Payment.CustomerID = Customers.CustomerID
            JOIN OrderDetails ON Orders.OrderID = OrderDetails.OrderID
            WHERE Orders.Status = '$status'
            ORDER BY Orders.OrderDate DESC, Orders.OrderTime DESC";
}

$result = $conn->query($sql);

if (!$result) {
    die("Error: " . $sql . "<br>" . $conn->error);
}

// Group orders by ID
$orders = array();

while ($row = $result->fetch_assoc()) {
    $orderID = $row['OrderID'];

    if (!isset($orders[$orderID])) {
        $orders[$orderID] = array(
            'OrderID' => $orderID,
            'OrderDate' => $row['OrderDate'],
            'OrderTime' => $row['OrderTime'],
            'PaymentMethod' => $row['PaymentMethod'],
            'Total' => $row['Total'],
            'CustomerName' => $row['CustomerName'],
            'CustomerID' => $row['CustomerID'],
            'OrderStatus' => $row['OrderStatus'],
            'OrderDetails' => array(),
        );
    }

    $orders[$orderID]['OrderDetails'][] = array(
        'MenuItem' => $row['MenuItem'],
        'Quantity' => $row['Quantity'],
    );
}

// Close database connection
$conn->close();

// Send response
$response = array("orders" => array_values($orders));
echo json_encode($response);
?>