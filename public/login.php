<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $data = json_decode(file_get_contents("php://input"));

  $email = $data->email;
  $password = $data->password;

  // Connect to database
  $servername = "localhost";
  $username = "root";
  $db_password = "";
  $dbname = "restobar";

  $conn = new mysqli($servername, $username, $db_password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Prepare and execute query
  $stmt = $conn->prepare("SELECT employeeID, password, salt FROM employee_login WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->bind_result($employeeID, $hashed_password, $salt);

  // Check if a row was returned
  if ($stmt->fetch()) {
    // Check if the password is correct
    if (hash('sha256', $password . $salt) === $hashed_password) {
      // Login successful
      echo json_encode(array("success" => true, "employeeID" => $employeeID, "message" => "Login successful"));
    } else {
      // Login failed
      echo json_encode(array("success" => false, "message" => "Wrong username or password"));
    }
  } else {
    // Login failed
    echo json_encode(array("success" => false, "message" => "Wrong username or password"));
  }

  // Close statement and connection
  $stmt->close();
  $conn->close();
}
?>
