<?php

include "./config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['email']) && isset($data['password'])) {

        $email = $conn->real_escape_string($data['email']);
        $password = $conn->real_escape_string($data['password']);
		$createAt = time();
		
            $sql = "INSERT INTO jh_app_user(email,password,createAt) VALUES ('$email','$password',FROM_UNIXTIME($createAt))";

            $check_sql = "SELECT * FROM jh_app_user WHERE email = '$email'";

            if ($conn->query($check_sql)->num_rows > 0) {
                header("HTTP/1.1 500 Internal Server Error");
                $response['success'] = 0;
                $response['message'] = 'account exist + ' . $conn->error;
            }else {
                if ($conn->query($sql) === TRUE) {
                    $response['success'] = 1;
                    $response['message'] = 'create a new user successfully';
                } else {
                    header("HTTP/1.1 500 Internal Server Error");
                    $response['success'] = 0;
                    $response['message'] = 'create a new user unsuccessfully + ' . $conn->error;
                }
            }
            echo json_encode($response);
    
        } else {
            $userDetail['success'] = 0;
            $userDetail['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($response);
    }
} else {
    $userDetail['success'] = 0;
    $userDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode($response);
}

$conn->close();
