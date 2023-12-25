<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['uid']) && isset($data['display_name']) 
        && isset($data['full_name']) && isset($data['email']) 
        && isset($data['phone']) && isset($data['address']) 
        && isset($data['birthday']) && isset($data['level']) && isset($data['avatar_url']) ) {

            $uid = $conn->real_escape_string($data['uid']);
            $display_name = $conn->real_escape_string($data['display_name']);
            $full_name = $conn->real_escape_string($data['full_name']);
			$avatar_url = $conn->real_escape_string($data['avatar_url']);
            $email = $conn->real_escape_string($data['email']);
            $phone = $conn->real_escape_string($data['phone']);
            $address = $conn->real_escape_string($data['address']);
            $birthday = $conn->real_escape_string($data['birthday']);
            $level = $conn->real_escape_string($data['level']);

            $sql1 = "INSERT INTO jh_user_profile(uid, display_name, full_name, avatar_url, email, phone, address, birthday, level, UserID) VALUES ('$uid','$display_name','$full_name','$avatar_url','$email','$phone','$address','$birthday','$level','$uid')";
		$sql2 = "UPDATE jh_app_user SET role = 'candidate' WHERE uid = '$uid'";

            if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE) {
                $response['success'] = 1;
                $response['message'] = 'create profile successfully';
            } else {
				$conn->query("UPDATE jh_app_user SET role = NULL WHERE uid = '$uid'");
				$conn->query("DELETE FROM jh_user_profile WHERE uid = '$uid'");
                header("HTTP/1.1 500 Internal Server Error");
                $response['success'] = 0;
                $response['message'] = 'create profile unsuccessfully + ' . $conn->error;
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

$conn->close();
