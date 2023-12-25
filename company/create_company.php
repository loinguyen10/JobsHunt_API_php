<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['uid']) 
        && isset($data['full_name']) && isset($data['email']) 
        && isset($data['phone']) && isset($data['address']) 
        && isset($data['web']) && isset($data['description']) 
        && isset($data['job']) && isset($data['level'])
        && isset($data['avatar_url']) && isset($data['tax_code'])) {

            $uid = $conn->real_escape_string($data['uid']);
            $full_name = $conn->real_escape_string($data['full_name']);
			$avatar_url = $conn->real_escape_string($data['avatar_url']);
            $email = $conn->real_escape_string($data['email']);
            $phone = $conn->real_escape_string($data['phone']);
            $address = $conn->real_escape_string($data['address']);
            $web = $conn->real_escape_string($data['web']);
            $description = $conn->real_escape_string($data['description']);
            $job = $conn->real_escape_string($data['job']);
            $level = $conn->real_escape_string($data['level']);
			$tax_code = $conn->real_escape_string($data['tax_code']);

            $sql1 = "INSERT INTO jh_company_profile(uid, avatar_url, full_name, email, phone, web, description, address, job, level,tax_code,UserID)  
            VALUES ('$uid','$avatar_url','$full_name','$email','$phone','$web','$description','$address','$job','$level','$tax_code','$uid')";
			$sql2 = "UPDATE jh_app_user SET role = 'recruiter' WHERE uid = '$uid'";
            

            if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE) {
                $response['success'] = 1;
                $response['message'] = 'create company successfully';
            } else {
				$conn->query("UPDATE jh_app_user SET role = NULL WHERE uid = '$uid'");
				$conn->query("DELETE FROM jh_company_profile WHERE uid = '$uid'");
                header("HTTP/1.1 500 Internal Server Error");
                $response['success'] = 0;
                $response['message'] = 'create company unsuccessfully + ' . $conn->error;
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
