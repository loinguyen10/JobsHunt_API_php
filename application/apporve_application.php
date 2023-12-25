<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['code']) && isset($data['approve'])) {

        $code = $conn->real_escape_string($data['code']);
        $approve = $conn->real_escape_string($data['approve']);
		$approve_time = date("Y/m/d H:i");

        $sql = "UPDATE jh_job_application SET approve= '$approve', approve_time = '$approve_time' WHERE code = '$code'";


        if ($conn->query($sql) === TRUE) {
            $response['success'] = 1;
            $response['message'] = 'you have approve cv successfully';
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            $response['success'] = 0;
            $response['message'] = 'you have approve cv unsuccessfully + ' . $conn->error;
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
