<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['code']) && isset($data['interview_time'])) {

        $code = $conn->real_escape_string($data['code']);
        $interview_time = $conn->real_escape_string($data['interview_time']);

        $convert = DateTime::createFromFormat('d/m/Y H:i', $interview_time);
        $time = $convert->format('Y-m-d H:i');

        $sql = "UPDATE jh_job_application SET interview_time = '$time' WHERE code = '$code'";


        if ($conn->query($sql) === TRUE) {
            $response['success'] = 1;
            $response['message'] = 'you have update interview time successfully';
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            $response['success'] = 0;
            $response['message'] = 'you have update interview time unsuccessfully + ' . $conn->error;
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
