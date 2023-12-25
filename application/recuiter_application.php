<?php

include "../config/dbconnect.php";

$listDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['companyId']) && isset($data['approve']) && isset($data['sent_time']) && isset($data['jobId'])) {
        $companyId = $conn->real_escape_string($data['companyId']);
        $jobId = $conn->real_escape_string($data['jobId']);
        $approve = $conn->real_escape_string($data['approve']);
        $sent_time = $conn->real_escape_string($data['sent_time']);

        $sql = "SELECT * FROM jh_job_application WHERE company_id = '$companyId'";

        if (!empty($jobId)) {
            $sql = $sql . " AND job_id = '$jobId'";
        }

        if ($approve != '') {
            if (strtolower($approve) == 'null') {
                $sql = $sql . " AND approve IS NULL";
            } else {
                $sql = $sql . " AND approve = '$approve'";
            }
        }

        if (!empty($sent_time)) {
            $convert = DateTime::createFromFormat('d/m/Y', $sent_time);
            $time = $convert->format('Y-m-d');
            $sql = $sql . " AND (DATE(send_time) = '$time' OR DATE(approve_time) = '$time' OR DATE(interview_time) = '$time')";
        }

        $result = $conn->query($sql);

        $listDetail['application'] = [];

        if ($result->num_rows > 0) {
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
        }

        $listDetail['success'] = 1;
        $listDetail['message'] = 'successful';
        header('Content-Type: application/json');
        echo json_encode([$txtdata => $listDetail], JSON_UNESCAPED_UNICODE);
    } else {
        $listDetail['success'] = 0;
        $listDetail['message'] = 'Missing in the request';
        header("HTTP/1.1 400 Bad Request");
        echo json_encode([$txtdata => $listDetail]);
    }
} else {
    $listDetail['success'] = 0;
    $listDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([$txtdata => $listDetail]);
}

$conn->close();
