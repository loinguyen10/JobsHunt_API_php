<?php
include "../config/dbconnect.php";

$userDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT * FROM `jh_user_profile`";
    $result = $conn->query($sql);

    if ($result) {
        $userDetail['success'] = 1;
        $userDetail['message'] = 'successful';
        $userDetail['profile'] = array();

        while ($row = $result->fetch_assoc()) {
            $intpremium = strtotime($row['premium_expiry']);

      
            if ($row['premium_expiry'] != null && $intpremium < time()) {
                $expiry_time = strtotime('+30 days');
                $sql2 = "UPDATE jh_user_profile SET premium_expiry = FROM_UNIXTIME($expiry_time), level = 'Basic' WHERE uid = '{$row['uid']}'";
                $conn->query($sql2);

   
                $row['premium_expiry'] = null;
                $row['level'] = 'Basic';
            } else {
                $row['premium_expiry'] = date("d/m/Y", strtotime($row['premium_expiry']));
            }

            $userDetail["profile"][] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode([$txtdata => $userDetail], JSON_UNESCAPED_UNICODE);
    } else {
        $userDetail['success'] = 0;
        $userDetail['message'] = 'unsuccessful';
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode([$txtdata => $userDetail]);
    }
} else {
    $userDetail['success'] = 0;
    $userDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([$txtdata => $userDetail]);
}

$conn->close();
