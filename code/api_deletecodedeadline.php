<?php
include "../config/dbconnect.php";

$response = array();

// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $now = time();

    $sql_delete = "DELETE FROM jh_otp_code WHERE expiry_time < FROM_UNIXTIME($now)";

    if ($conn->query($sql_delete) === TRUE) {
        $response['success'] = 1;
        $response['message'] = "All deleted.";
    } else {
        $response['success'] = 0;
        $response['message'] = 'All unsuccessful.';
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
// } else {
//     $response['success'] = 0;
//     $response['message'] = 'Method not allowed';
//     header("HTTP/1.1 405 Method Not Allowed");
//     echo json_encode($response, JSON_UNESCAPED_UNICODE);
// }

$conn->close();
