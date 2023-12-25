<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['userId']) && isset($data['companyId']) && isset($data['content']) && isset($data['send'])) {

            $userId = $conn->real_escape_string((string) $data['userId']);
            $companyId = $conn->real_escape_string($data['companyId']);
            $content = $conn->real_escape_string($data['content']);
            $send = $conn->real_escape_string($data['send']);
            $timestamp = date("Y-m-d h:i:sa");
            $sql = "INSERT INTO jh_message(userId, companyId, content, send, timestamp) VALUES ('$userId', '$companyId', '$content', '$send', '$timestamp')";           

            if ($conn->query($sql) === TRUE) {
                $response['success'] = 1;
                $response['message'] = 'report successfully';
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                $response['success'] = 0;
                $response['message'] = 'report unsuccessfully + ' . $conn->error;
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