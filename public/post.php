<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Receive data from frontend
$data = json_decode(file_get_contents("php://input"));

// Extract data
$order = $data->order;
$total = $data->total;
$payment = $data->payment;
$date = $data->date;
$time = $data->time;
$status = $data->status;
$customerName = $data->customerName;
$customerID = $data->customerID;
$orderID = $data->orderID;
$employeeID = $data->employeeID;
// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restobar";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if employee exists
$sql = "SELECT EmployeeID FROM Employee WHERE EmployeeID = '$employeeID'";
$result = $conn->query($sql);

if (!$result) {
    die("Error: " . $sql . "<br>" . $conn->error);
}

if ($result->num_rows == 0) {
    die("Employee not found");
}

// Insert customer
if (!empty($customerID) && !empty($customerName)) {
    $sql = "INSERT INTO Customers (CustomerID, CustomerName) VALUES ('$customerID', '$customerName')";

    if (!$conn->query($sql)) {
        die("Error: " . $sql . "<br>" . $conn->error);
    }
}

// Insert order
if (!empty($orderID)) {
    $sql = "INSERT INTO Orders (OrderID, EmployeeID, OrderDate, OrderTime, Status) VALUES ('$orderID', '$employeeID', '$date', '$time', '$status')";

    if (!$conn->query($sql)) {
        die("Error: " . $sql . "<br>" . $conn->error);
    }
}

// Insert payment
$sql = "INSERT INTO Payment (PaymentID, CustomerID, PaymentMethod, Total) VALUES ('$orderID', '$customerID', '$payment', $total)";

if (!$conn->query($sql)) {
    die("Error: " . $sql . "<br>" . $conn->error);
}

// Insert or update order details
if (!empty($order)) {
    foreach ($order as $item) {
        $menuItem = $item->name;
        $quantity = $item->quantity;

        $sql = "INSERT INTO OrderDetails (OrderID, CustomerID, MenuItem, Quantity) VALUES ('$orderID', '$customerID', '$menuItem', $quantity)";

        if (!$conn->query($sql)) {
            die("Error: " . $sql . "<br>" . $conn->error);
        }
    }
}

$conn->close();

// Send response
$response = array("message" => "Order added successfully");
echo json_encode($response);
?>
