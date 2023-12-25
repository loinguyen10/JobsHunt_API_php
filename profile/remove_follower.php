<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['userId']) && isset($data['companyId'])) {

            $companyId = $conn->real_escape_string($data['companyId']);
			$userId = $conn->real_escape_string($data['userId']);

            $sql = "DELETE FROM jh_user_follower WHERE user_id = '$userId' and company_id = '$companyId' ";
            

            if ($conn->query($sql) === TRUE) {
                $response['success'] = 1;
                $response['message'] = 'delete follower of ' . $user_id .' successfully';
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                $response['success'] = 0;
                $response['message'] = 'delete follower unsuccessfully + ' . $conn->error;
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
