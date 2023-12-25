<?php

include "../config/dbconnect.php";

$cvDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['userId'])) {
        $userId = $conn->real_escape_string($data['userId']);

        $sql = "SELECT * FROM jh_user_follower WHERE user_id = '$userId'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $listDetail['success'] = 1;
            $listDetail['message'] = 'successful';
            $listDetail['follower'] = [];

            while ($row = $result->fetch_assoc()) {
                $companyId = $row['company_id'];
                $sqlx = "SELECT * FROM jh_company_profile WHERE `uid` = $companyId";
                $resultx = $conn->query($sqlx);

                if ($resultx->num_rows > 0) {
                    $row['company'] = [];
                    while ($rowx = $resultx->fetch_assoc()) {
						$row['company'] = $rowx;
                    }
                }
                $listDetail["follower"][] = $row;
            }

            header('Content-Type: application/json');
            echo json_encode([$txtdata => $listDetail], JSON_UNESCAPED_UNICODE);
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
