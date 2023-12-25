<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['report_sender_id']) && isset($data['reported_persons_id']) && isset($data['title']) && isset($data['description'])) {

            $report_sender_id = $conn->real_escape_string($data['report_sender_id']);
            $reported_persons_id = $conn->real_escape_string($data['reported_persons_id']);
            $title = $conn->real_escape_string($data['title']);
            $description = $conn->real_escape_string($data['description']);

            $sql = "INSERT INTO jh_report(report_sender_id, reported_persons_id, title, description) VALUES ('$report_sender_id', '$reported_persons_id', '$title', '$description')";           

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
