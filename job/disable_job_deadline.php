<?php

include "../config/dbconnect.php";

$response = array();

$now = date("Y-m-d");


$sql = "UPDATE jh_job_detail SET active='0' WHERE deadline <= '$now' AND active <> '0'";

if ($conn->query($sql) === TRUE) {
    $response['success'] = 1;
    $response['message'] = 'job deadline come successfully';
} else {
    header("HTTP/1.1 500 Internal Server Error");
    $response['success'] = 0;
    $response['message'] = 'job deadline come unsuccessfully + ' . $conn->error;
}
echo json_encode($response);


$conn->close();
