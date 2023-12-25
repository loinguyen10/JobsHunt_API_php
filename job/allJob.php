<?php

include "../config/dbconnect.php";

$listDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM jh_job_detail";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $listDetail['success'] = 1;
        $listDetail['message'] = 'successful';
        $listDetail['job'] = [];
        while ($row = $result->fetch_assoc()) {
            $companyId = $row['companyId'];
            $sqlx = "SELECT * FROM jh_company_profile WHERE `uid` = $companyId";
            $resultx = $conn->query($sqlx);

            if ($resultx->num_rows > 0) {
                $row['company'] = [];

                while ($rowx = $resultx->fetch_assoc()) {
                    $row['company'] = $rowx;
                }
            }

            $idJob = $row['code'];
            $sql_approve = "SELECT * FROM `jh_job_application` WHERE job_id = '$idJob' AND approve = '1'";
            $result_approve = $conn->query($sql_approve);
            $numberCV = $result_approve->num_rows;

            $row['remain_people'] = $row['numberCandidate'] - $numberCV;

            if ($row['remain_people'] < 0) {
                $row['remain_people'] = 0;
            }

            $row['deadline'] = date("d/m/Y", strtotime($row['deadline']));
            $listDetail["job"][] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode([$txtdata => $listDetail], JSON_UNESCAPED_UNICODE);
    } else {
        $listDetail['success'] = 0;
        $listDetail['message'] = 'unsuccessful';
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode([$txtdata => $listDetail]);
    }
} else {
    $listDetail['success'] = 0;
    $listDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([$txtdata => $listDetail]);
}

$conn->close();
