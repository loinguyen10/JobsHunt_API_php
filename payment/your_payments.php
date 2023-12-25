<?php

include "../config/dbconnect.php";

$listDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['uid'])) {
        $uid = $conn->real_escape_string($data['uid']);

        $sql = "SELECT * FROM jh_payment_history WHERE userId = '$uid'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $listDetail['success'] = 1;
            $listDetail['message'] = 'successful';
            $listDetail['payment'] = [];
            while ($row = $result->fetch_assoc()) {
                $row['date'] = date("d/m/Y H:i:s", strtotime($row['date']));
                $listDetail["payment"][] = $row;
            }

            header('Content-Type: application/json');
            echo json_encode([$txtdata => $listDetail], JSON_UNESCAPED_UNICODE);
        } else {
            $listDetail['success'] = 0;
            $listDetail['message'] = 'unsuccessful';
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([$txtdata => $listDetail]);
        }
    } else {
        $response['success'] = 0;
        $response['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($response);
    }
} else {
    $listDetail['success'] = 0;
    $listDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([$txtdata => $listDetail]);
}

$conn->close();
