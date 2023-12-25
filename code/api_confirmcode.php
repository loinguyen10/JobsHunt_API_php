<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['email']) && isset($data['otp_code']) && isset($data['type_code'])) {
        $email = $conn->real_escape_string($data['email']);
        $code = $conn->real_escape_string($data['otp_code']);
        $type_code = $conn->real_escape_string($data['type_code']);
		$time = time();
    
        $sql = "SELECT * FROM jh_otp_code WHERE email = '$email' and code = '$code' and type_code = '$type_code' and expiry_time >= FROM_UNIXTIME($time)";
        $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
			if($type_code == 'RegisterOTP'){
				$sql_user = "UPDATE jh_app_user SET status = 1 WHERE email = '$email'";
				$result_user = $conn->query($sql_user);
            	if ($conn->query($sql_user) === TRUE) {
            		$response['success'] = 1;
            		$response['message'] = 'active account successfully';
    
            		header('Content-Type: application/json');
            		echo json_encode($response,JSON_UNESCAPED_UNICODE);
        		}
			}else if($type_code == 'RePassOTP'){
				$response['success'] = 1;
            	$response['message'] = 'active forget successfully';
    
            	header('Content-Type: application/json');
            	echo json_encode($response,JSON_UNESCAPED_UNICODE);
			}
        } else {
            $response['success'] = 0;
            $response['message'] = 'unsuccessful' . $conn->error;
            header('Content-Type: application/json');
            echo json_encode($response);
        }
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

$conn->close();
