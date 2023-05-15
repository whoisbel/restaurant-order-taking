<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: DELETE, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "restobar";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    // Check if the HTTP method is POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the id parameter from the request body
    $id = json_decode(file_get_contents("php://input"), true)['id'];

    // Delete the record with the given id from the 'fooditems' table
    $query = "DELETE FROM fooditems WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        http_response_code(204); // Success, no content
    } else {
        http_response_code(500); // Internal server error
        echo json_encode(['error' => 'Could not delete item']);
    }

    mysqli_close($conn);
    } else {
    http_response_code(405); // Method not allowed
    echo json_encode(['error' => 'Method not allowed']);
    }
?>
