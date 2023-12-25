<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['uid']) 
        && isset($data['full_name']) && isset($data['email']) 
        && isset($data['phone']) && isset($data['address']) 
        && isset($data['web']) && isset($data['description']) 
        && isset($data['job']) && isset($data['avatar_url']) && isset($data['tax_code'])) {

            $uid = $conn->real_escape_string($data['uid']);
            $full_name = $conn->real_escape_string($data['full_name']);
			$avatar_url = $conn->real_escape_string($data['avatar_url']);
            $email = $conn->real_escape_string($data['email']);
            $phone = $conn->real_escape_string($data['phone']);
            $address = $conn->real_escape_string($data['address']);
            $web = $conn->real_escape_string($data['web']);
            $description = $conn->real_escape_string($data['description']);
            $job = $conn->real_escape_string($data['job']);
			$tax_code = $conn->real_escape_string($data['tax_code']);

            $sql = "UPDATE jh_company_profile SET avatar_url = '$avatar_url', full_name = '$full_name', email = '$email', phone = '$phone', web = '$web', description = '$description', address = '$address', job = '$job', tax_code = '$tax_code' WHERE uid = '$uid'";
            

            if ($conn->query($sql) === TRUE) {
                $response['success'] = 1;
                $response['message'] = 'update company successfully';
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                $response['success'] = 0;
                $response['message'] = 'update company unsuccessfully + ' . $conn->error;
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
