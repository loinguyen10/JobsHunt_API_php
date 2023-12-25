<?php

include "../config/dbconnect.php";

$listDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM jh_job_title";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $listDetail['success'] = 1;
        $listDetail['message'] = 'successful';
         $titles = []; // Create an array to store titles
    
    while ($row = $result->fetch_assoc()) {
        // Extract the "title" value from the row and add it to the titles array
        $titles[] = $row['title'];
    }
    
    $listDetail['data']['title'] = $titles;

        header('Content-Type: application/json');
        echo json_encode([$txtdata => $listDetail],JSON_UNESCAPED_UNICODE);
    } else {
        $listDetail['success'] = 0;
        $listDetail['message'] = 'unsuccessful';
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode([$txtdata => $listDetail]);
    }


} else {
$listDetail['success'] = 0;
$listDetail['message'] = 'Method not allowed';
header("HTTP/1.1 405 Method Not Allowed");
echo json_encode([$txtdata => $listDetail]);
}

$conn->close();
