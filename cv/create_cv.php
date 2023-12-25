<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['cv_url']) && isset($data['user_id']) && isset($data['type']) ) {

            $cv_url = $conn->real_escape_string($data['cv_url']);
			$user_id = $conn->real_escape_string($data['user_id']);
            $type = $conn->real_escape_string($data['type']);
			$create_time = $conn->real_escape_string($data['create_time']);

            $sql = "INSERT INTO jh_user_cv(cv_url, user_id, type,create_time)  
                VALUES ('$cv_url','$user_id','$type','$create_time')";
            

            if ($conn->query($sql) === TRUE) {
                $response['success'] = 1;
                $response['message'] = 'create cv successfully';
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                $response['success'] = 0;
                $response['message'] = 'create cv unsuccessfully + ' . $conn->error;
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
