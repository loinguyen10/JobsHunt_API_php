<?php

include "config/dbconnect.php";

$userDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['email']) && isset($data['password'])) {

            $email = $conn->real_escape_string($data['email']);
            $password = $conn->real_escape_string($data['password']);

            $sql = "SELECT uid,email,role,status FROM jh_app_user WHERE email='$email' and password='$password'";
    
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $userDetail['success'] = 1;
                $userDetail['message'] = 'login successfully';
                $userDetail['user'] = [];
                while ($row = $result->fetch_assoc()) {
                    //temp array
                    $userDetail["user"] = $row;
                }
        
                header('Content-Type: application/json');
                echo json_encode([$txtdata => $userDetail]);
            } else {
                $userDetail['success'] = 0;
                $userDetail['message'] = 'login unsuccessfully';
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode([$txtdata => $userDetail]);
            }
    
        } else {
            $userDetail['success'] = 0;
            $userDetail['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
        echo json_encode([$txtdata => $userDetail]);
    }
} else {
    $userDetail['success'] = 0;
    $userDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([$txtdata => $userDetail]);
}

$conn->close();
