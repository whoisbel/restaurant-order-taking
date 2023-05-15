<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: PUT");

$host = "localhost";
$user = "root";
$password = "";
$dbname = "restobar";

$conn = mysqli_connect($host, $user, $password, $dbname);

if ($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "SELECT * FROM fooditems";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $foodItems = array();

            while ($row = mysqli_fetch_assoc($result)) {
                $row['availability'] = (bool)$row['availability'];
                $foodItems[] = $row;
            }

            mysqli_free_result($result);
        }

        echo json_encode($foodItems);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $availability = (int)$data['availability'];
        $price = mysqli_real_escape_string($conn, $data['price']);

        $sql = "UPDATE fooditems SET availability=$availability, price='$price' WHERE id='$id'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo json_encode(array('message' => 'Food item updated successfully.'));
        } else {
            echo json_encode(array('message' => 'Error updating food item.'));
        }
    } if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = mysqli_real_escape_string($conn, $_POST['price']);
        $availability = (int)$_POST['availability'];
        $imagePath = 'default.png'; // set default image path
    
        if (isset($_FILES['img'])) {
            // Save image with name "{{ item name.png }}"
            $imagePath = "../src/assets/" . $name . ".png";
            move_uploaded_file($_FILES['img']['tmp_name'], str_replace('/', '\\', $imagePath));
        }
    
        $sql = "INSERT INTO fooditems (name, price, availability, img) VALUES ('$name', '$price', $availability, '$imagePath')";
        $result = mysqli_query($conn, $sql);
    
        if ($result) {
            $id = mysqli_insert_id($conn);
            // Include image path in response
            echo json_encode(array(
                'id' => $id,
                'name' => $name,
                'price' => $price,
                'availability' => (bool)$availability,
                'img' => $imagePath ? $_SERVER['HTTP_HOST'] . '/' . $imagePath : ''
            ));
        } else {
            echo json_encode(array('message' => 'Error adding food item.'));
        }
    }    
    
    

    mysqli_close($conn);
}
