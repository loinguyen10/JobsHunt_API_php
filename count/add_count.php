<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['user_id']) && isset($data['title'])) {

        $user_id = $conn->real_escape_string($data['user_id']);
        $title = $conn->real_escape_string($data['title']);
        $created_time = time();

        $sqlx = "INSERT INTO jh_basic_count(user_id, title, created_time) VALUES ('$user_id', '$title', FROM_UNIXTIME($created_time))";

        if ($conn->query($sqlx) === TRUE) {
            $response['success'] = 1;
            $response['message'] = '+1';
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            $response['success'] = 0;
            $response['message'] = $conn->error;
        }
    } else {
        $response['success'] = 0;
        $response['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
    }
} else {
    $response['success'] = 0;
    $response['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    
}

echo json_encode($response);
$conn->close();
