<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['cv_url']) && isset($data['jobId']) && isset($data['candidateId']) && isset($data['companyId'])) {
        $cv_url = $conn->real_escape_string($data['cv_url']);
        $job_id = $conn->real_escape_string($data['jobId']);
        $candidate_id = $conn->real_escape_string($data['candidateId']);
		$company_id = $conn->real_escape_string($data['companyId']);
        $send_time = time();

        $sql = "INSERT INTO jh_job_application(cv_url, job_id,candidate_id, company_id, send_time) 
                VALUES ('$cv_url','$job_id','$candidate_id','$company_id', FROM_UNIXTIME($send_time))";

        $check_sql = "SELECT * FROM jh_job_application WHERE cv_url = '$cv_url' AND job_id = '$job_id' AND company_id = '$company_id' AND candidate_id = '$candidate_id'";

        if ($conn->query($check_sql)->num_rows > 0) {
            header("HTTP/1.1 500 Internal Server Error");
            $response['success'] = 0;
            $response['message'] = 'cv exist + ' . $conn->error;
        } else {
            if ($conn->query($sql) === TRUE) {
                $response['success'] = 1;
                $response['message'] = 'you send cv successfully';
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                $response['success'] = 0;
                $response['message'] = 'you send cv unsuccessfully + ' . $conn->error;
            }
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
