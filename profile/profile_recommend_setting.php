<?php

include "../config/dbconnect.php";

$dataDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['uid'])) {
        $uid = $conn->real_escape_string($data['uid']);
    
        $sql = "SELECT * FROM jh_user_profile_recommend_setting WHERE uid = '$uid'";
        $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
            $dataDetail['success'] = 1;
            $dataDetail['message'] = 'successful';
            $dataDetail['setting'] = [];
    
            while ($row = $result->fetch_assoc()) {
                //temp array
                $dataDetail["setting"] = $row;
                // $educationId = $dataDetail["setting"]["educationId"];
                // $sqlx = "SELECT * FROM `jh_education_list` WHERE `id` IN ($educationId)";
                // $resultx = $conn->query($sqlx);
    
                // if ($resultx->num_rows > 0) {
                //     $dataDetail['setting']["education"] = [];
                //     while ($rowx = $resultx->fetch_assoc()) {
                //         //temp array
                //         $dataDetail['setting']["education"][] = $rowx;
                //     }
                // }
            }
    
            header('Content-Type: application/json');
            echo json_encode([$txtdata => $dataDetail],JSON_UNESCAPED_UNICODE);
        } else {
            $dataDetail['success'] = 0;
            $dataDetail['message'] = 'unsuccessful';
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([$txtdata => $dataDetail]);
        }
    } else {
        $dataDetail['success'] = 0;
        $dataDetail['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
        echo json_encode([$txtdata => $dataDetail]);
    }
    
} else {
    $dataDetail['success'] = 0;
    $dataDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([$txtdata => $dataDetail]);
}

$conn->close();
