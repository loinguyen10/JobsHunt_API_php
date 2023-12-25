<?php

include "../config/dbconnect.php";

$jobDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['code'])) {
        $code = $conn->real_escape_string($data['code']);

        $sql = "SELECT * FROM jh_job_detail WHERE code = '$code'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $jobDetail['success'] = 1;
            $jobDetail['message'] = 'successful';
            $jobDetail['job'] = [];

            while ($row = $result->fetch_assoc()) {
                //temp array
                $jobDetail["job"] = $row;
                $jobDetail["job"]['deadline'] = date("d/m/Y", strtotime($jobDetail["job"]['deadline']));

                $idJob = $jobDetail["job"]['code'];
                $sql_approve = "SELECT * FROM `jh_job_application` WHERE job_id = '$idJob' AND approve = '1'";
                $result_approve = $conn->query($sql_approve);
                $numberCV = $result_approve->num_rows;

                $jobDetail["job"]['remain_people'] = $jobDetail["job"]['numberCandidate'] - $numberCV;

                if ($jobDetail["job"]['remain_people'] < 0) {
                    $jobDetail["job"]['remain_people'] = 0;
                }

                $companyId = $jobDetail['job']['companyId'];
                $sqlx = "SELECT * FROM `jh_company_profile` WHERE `uid` = $companyId";
                $resultx = $conn->query($sqlx);

                if ($resultx->num_rows > 0) {
                    $jobDetail['job']['company'] = [];
                    while ($rowx = $resultx->fetch_assoc()) {
                        //temp array
                        $jobDetail['job']['company'] = $rowx;
                    }
                }
            }

            header('Content-Type: application/json');
            echo json_encode([$txtdata => $jobDetail], JSON_UNESCAPED_UNICODE);
        } else {
            $jobDetail['success'] = 0;
            $jobDetail['message'] = 'unsuccessful';
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([$txtdata => $jobDetail]);
        }
    } else {
        $jobDetail['success'] = 0;
        $jobDetail['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
        echo json_encode([$txtdata => $jobDetail]);
    }
} else {
    $jobDetail['success'] = 0;
    $jobDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([$txtdata => $jobDetail]);
}

$conn->close();
