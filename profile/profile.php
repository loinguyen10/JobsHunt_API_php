<?php

include "../config/dbconnect.php";

$userDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['uid'])) {
        $uid = $conn->real_escape_string($data['uid']);

        $sql = "SELECT * FROM `jh_user_profile` WHERE `uid` = $uid";
        $result = $conn->query($sql);

        $time = time();
        $check = false;

        if ($result->num_rows > 0) {
            $userDetail['success'] = 1;
            $userDetail['message'] = 'successful';
            $userDetail['profile'] = [];

            while ($row = $result->fetch_assoc()) {
				$intpremium = strtotime($row['premium_expiry']);
				
                if($row['premium_expiry'] != null){
                    if ($intpremium <= $time) {
                        $sql2 = "UPDATE jh_user_profile SET premium_expiry = null, level = 'Basic' WHERE uid = '$userId'";
    
                        while (!$check) {
                            if ($conn->query($sql2) === TRUE) {
                                $check = true;
                                $row['premium_expiry'] = null;
                                $row['level'] = 'Basic';
                            }
                        }
                    } else {
                        $row['premium_expiry'] = date("d/m/Y", strtotime($row['premium_expiry']));
                    }
                }

                $userDetail["profile"] = $row;
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
        $userDetail['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
        echo json_encode([$txtdata => $userDetail]);
    }
} else {
    $userDetail['success'] = 0;
    $userDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([$txtdata => $userDetail]);
}

$conn->close();
