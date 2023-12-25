<?php

include "../config/dbconnect.php";

$listDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM jh_user_cv";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $listDetail['success'] = 1;
        $listDetail['message'] = 'successful';
        $listDetail['cv'] = [];
        while ($row = $result->fetch_assoc()) {
            $userId = $row['user_id'];
                $sqlx = "SELECT * FROM jh_user_profile WHERE `uid` = $userId";
                $resultx = $conn->query($sqlx);
    
                if ($resultx->num_rows > 0) {
                    $row['profile'] = [];
                    while ($rowx = $resultx->fetch_assoc()) {
                        $row['profile'] = $rowx;
                    }
                }
            $listDetail["cv"][] = $row;
        }

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
