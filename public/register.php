<?php 
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $data = json_decode(file_get_contents("php://input"));

        $name = $data->name;
        $age = $data->age;
        $address = $data->address;
        $email = $data->email;
        $password = $data->password;
        $employeeID = $data->employeeID;
        $gender = $data->gender;
        $salt = $age . $address;
        $hashed_password = hash('sha256', $password . $salt);
        $servername = "localhost";
        $username = "root";
        $db_password = "";
        $dbname = "restobar";
        $conn = new mysqli($servername, $username, $db_password, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO EMPLOYEE (EmployeeID, EmpName, EmpAddress, EmpAge, EmpGender) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $employeeID, $name, $address, $age, $gender);
        $stmt->execute();
        $stmt->close();

        $stmt2 = $conn->prepare("INSERT INTO EMPLOYEE_LOGIN (Email, Password, EmployeeID, salt) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("ssss", $email, $hashed_password, $employeeID, $salt);
        $stmt2->execute();
        $stmt2->close();

        $conn->close();
        echo json_encode(array("message" => "Employee added successfully."));
    } catch (Exception $e) {
        echo json_encode(array("message" => "Error: " . $e->getMessage()));
    }
}
?>
