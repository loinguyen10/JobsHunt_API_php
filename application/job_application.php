<?php

include "../config/dbconnect.php";

$cvDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['jobId'])) {
        $jobId = $conn->real_escape_string($data['jobId']);

        $sql = "SELECT * FROM jh_job_application WHERE job_id = '$jobId'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $listDetail['success'] = 1;
            $listDetail['message'] = 'successful';
            $listDetail['application'] = [];

            while ($row = $result->fetch_assoc()) {
                $jobId = $row['job_id'];
                $candidateId = $row['candidate_id'];
                $sqlx = "SELECT * FROM jh_job_detail WHERE `code` = $jobId";
                $sqla = "SELECT * FROM jh_user_profile WHERE `uid` = $candidateId";
                $resultx = $conn->query($sqlx);
                $resulta = $conn->query($sqla);

                if ($resultx->num_rows > 0) {
                    $row['job'] = [];
                    while ($rowx = $resultx->fetch_assoc()) {
                        $companyId = $rowx['companyId'];
                        $sqly = "SELECT * FROM jh_company_profile WHERE `uid` = $companyId";
                        $resulty = $conn->query($sqly);

                        if ($resulty->num_rows > 0) {
                            $rowx['company'] = [];
                            while ($rowy = $resulty->fetch_assoc()) {
								$rowx['company'] = $rowy;
                            }
                        }

                        $idJob = $rowx['code'];
                        $sql_approve = "SELECT * FROM `jh_job_application` WHERE job_id = '$idJob' AND approve = '1'";
                        $result_approve = $conn->query($sql_approve);
                        $numberCV = $result_approve->num_rows;

                        $rowx['remain_people'] = $rowx['numberCandidate'] - $numberCV;

                        if ($rowx['remain_people'] < 0) {
                            $rowx['remain_people'] = 0;
                        }
                        
                        $rowx['deadline'] = date("d/m/Y", strtotime($rowx['deadline']));
						$row['job'] = $rowx;
                    }
                }

                if ($resulta->num_rows > 0) {
                    $row['candidate'] = [];
                    while ($rowx = $resulta->fetch_assoc()) {
						$row['candidate'] = $rowx;
                    }
                }

                $row['send_time'] = date("d/m/Y H:i", strtotime($row['send_time']));

                $listDetail["application"][] = $row;
            }

            header('Content-Type: application/json');
            echo json_encode([$txtdata => $listDetail], JSON_UNESCAPED_UNICODE);
        } else {
            $cvDetail['success'] = 0;
            $cvDetail['message'] = 'unsuccessful';
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([$txtdata => $cvDetail]);
        }
    } else {
        $cvDetail['success'] = 0;
        $cvDetail['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
        echo json_encode([$txtdata => $cvDetail]);
    }
} else {
    $cvDetail['success'] = 0;
    $cvDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([$txtdata => $cvDetail]);
}

$conn->close();
