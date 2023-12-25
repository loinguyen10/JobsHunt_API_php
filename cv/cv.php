<?php

include "../config/dbconnect.php";

$cvDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['code'])) {
        $code = $conn->real_escape_string($data['code']);
    
        $sql = "SELECT * FROM jh_user_cv WHERE code = '$code'";
        $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
            $cvDetail['success'] = 1;
            $cvDetail['message'] = 'successful';
            $cvDetail['cv'] = [];
    
            while ($row = $result->fetch_assoc()) {
                //temp array
                $cvDetail["cv"] = $row;
				$userId = $cvDetail['cv']['user_id'];
                $sqlx = "SELECT * FROM `jh_user_profile` WHERE `uid` = $userId";
                $resultx = $conn->query($sqlx);
    
                if ($resultx->num_rows > 0) {
                    $cvDetail['cv']['profile'] = [];
                    while ($rowx = $resultx->fetch_assoc()) {
                        //temp array
                        $cvDetail['cv']['profile'] = $rowx;
                    }
                }
            }
    
            header('Content-Type: application/json');
            echo json_encode([$txtdata => $cvDetail],JSON_UNESCAPED_UNICODE);
        } else {
            $cvDetail['success'] = 0;
            $cvDetail['message'] = 'unsuccessful';
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([$txtdata => $cvDetail]);
        }
    } else {
        $cvDetail['success'] = 0;
        $cvDetail['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
        echo json_encode([$txtdata => $cvDetail]);
    }
    
} else {
    $cvDetail['success'] = 0;
    $cvDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([$txtdata => $cvDetail]);
}

$conn->close();
