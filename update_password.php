<?php

include "config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['email']) && isset($data['new_password'])) {

            $email = $conn->real_escape_string($data['email']);
            $new_password = $conn->real_escape_string($data['new_password']);

            $sql = "UPDATE jh_app_user SET password='$new_password' WHERE email='$email'";

            if ($conn->query($sql) === TRUE) {
                $response['success'] = 1;
                $response['message'] = 'update password successfully';
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                $response['success'] = 0;
                $response['message'] = 'update password unsuccessfully + ' . $conn->error;
            }
            echo json_encode($response);
    
        } else {
            $response['success'] = 0;
            $response['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($response);
    }
} else {
    $response['success'] = 0;
    $response['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode($response);
}

$conn->close();
