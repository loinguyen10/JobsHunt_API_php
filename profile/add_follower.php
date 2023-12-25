<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['companyId']) && isset($data['userId'])) {

            $company_id = $conn->real_escape_string($data['companyId']);
            $user_id = $conn->real_escape_string($data['userId']);

            $sql = "INSERT INTO jh_user_follower(company_id, user_id) VALUES ('$company_id','$user_id')";
            

            if ($conn->query($sql) === TRUE) {
                $response['success'] = 1;
                $response['message'] = 'add follower of ' . $user_id .' successfully';
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                $response['success'] = 0;
                $response['message'] = 'add follower unsuccessfully + ' . $conn->error;
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
