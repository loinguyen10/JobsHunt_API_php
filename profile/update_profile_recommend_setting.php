<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['uid']) && isset($data['gender']) 
    && isset($data['job']) && isset($data['educationId'])
    && isset($data['yearExperience']) && isset($data['workProvince'])
    && isset($data['minSalary']) && isset($data['maxSalary']) && isset($data['currency'])) {

        $uid = $conn->real_escape_string($data['uid']);
        $gender = $conn->real_escape_string($data['gender']);
        $job = $conn->real_escape_string($data['job']);
        $educationId = $conn->real_escape_string($data['educationId']);
        $yearExperience = $conn->real_escape_string($data['yearExperience']);
        $workProvince = $conn->real_escape_string($data['workProvince']);
        $minSalary = $conn->real_escape_string($data['minSalary']);
        $maxSalary = $conn->real_escape_string($data['maxSalary']);
        $currency = $conn->real_escape_string($data['currency']);

            $sql = "UPDATE jh_user_profile_recommend_setting SET gender = '$gender', job = '$job', educationId = '$educationId', yearExperience = '$yearExperience', workProvince = '$workProvince', minSalary = '$minSalary', maxSalary = '$maxSalary', currency = '$currency' WHERE uid = '$uid'";
            

            if ($conn->query($sql) === TRUE) {
                $response['success'] = 1;
                $response['message'] = 'update setting successfully';
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                $response['success'] = 0;
                $response['message'] = 'update setting unsuccessfully + ' . $conn->error;
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
