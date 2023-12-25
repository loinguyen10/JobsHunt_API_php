<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (
        isset($data['money']) && isset($data['status'])
        && isset($data['payment_type']) && isset($data['userId'])
    ) {

        $money = $conn->real_escape_string($data['money']);
        $date = time();
        $status = $conn->real_escape_string($data['status']);
        $payment_type = $conn->real_escape_string($data['payment_type']);
        $userId = $conn->real_escape_string($data['userId']);

        $check = false;

        $expiry_time = time();

        $sql1 = "INSERT INTO `jh_payment_history`(`money`, `date`, `status`, `payment_type`, `userId`) 
                    VALUES ('$money',FROM_UNIXTIME($date),'$status','$payment_type','$userId')";

        if ($conn->query($sql1) === TRUE) {

            if($status == '00'){
                $sql_check = "SELECT * FROM `jh_company_profile` WHERE uid = '$userId'";
                $result_check = $conn->query($sql_check);

                if ($result_check->num_rows > 0) {
                    while ($row = $result_check->fetch_assoc()) {
                        if($row['premium_expiry'] != null){
                            $date = strtotime($row['premium_expiry']);
                            $expiry_time = $date + convertMoneytoSeconds($money);
                        }else{
                            $expiry_time = time() + convertMoneytoSeconds($money);
                        }
                        
                    }
                }
                
                $sql2 = "UPDATE jh_company_profile SET premium_expiry = FROM_UNIXTIME($expiry_time), level = 'Premium' WHERE uid = '$userId'";
    
                while(!$check){
                    if ($conn->query($sql2) === TRUE) {
                        $check = true;
                        $response['success'] = 1;
                        $response['message'] = 'pay successfully';
                        break;
                    }
                }
            }else{
                $response['success'] = 0;
                $response['message'] = 'pay unsuccessfully';
            }
            
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            $response['success'] = 0;
            $response['message'] = 'insert payment unsuccessfully + ' . $conn->error;
        }
        echo json_encode($response);
    } else {
        $response['success'] = 0;
        $response['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($response);
    }
} else {
    $response['success'] = 0;
    $response['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode($response);
}

function convertMoneytoSeconds($money)
{
    switch ($money) {
        case "16000":
            return 2592000;
        case "40000":
            return 7862400;
        case "72000":
            return 15724800;
        case "140000":
            return 31536000;
        default:
            return 0;
    }
}

$conn->close();
