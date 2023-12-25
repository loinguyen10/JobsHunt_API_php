<?php

include "../config/dbconnect.php";

$listDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (
        isset($data['search_word']) && isset($data['minSalary'])
        && isset($data['maxSalary']) && isset($data['type_job'])
        && isset($data['year_experience']) && isset($data['province'])
    ) {

        $search_word = $conn->real_escape_string($data['search_word']);
        $minSalary = $conn->real_escape_string($data['minSalary']);
        $maxSalary = $conn->real_escape_string($data['maxSalary']);
        $type_job = $conn->real_escape_string($data['type_job']);
        $year_experience = $conn->real_escape_string($data['year_experience']);
        $province = $conn->real_escape_string($data['province']);

        $sql = "SELECT A.* FROM jh_job_detail A INNER JOIN jh_company_profile B ON A.companyId = B.uid";

        if (!empty($search_word)) {
            $sql = $sql . " WHERE ( (A.name COLLATE 'utf8_general_ci') LIKE '%$search_word%' OR (B.full_name COLLATE 'utf8_general_ci') LIKE '%$search_word%' OR (A.tag COLLATE 'utf8_general_ci') LIKE '%$search_word%')";
        }

        if ($minSalary != '' && empty($maxSalary)) {
            $sql = $sql . " AND ( A.minSalary >= '$minSalary' OR A.minSalary = '-1') ";
        } else if ($minSalary != '' && $maxSalary != '') {
            $sql = $sql . " AND ((
                (A.minSalary >= '$minSalary' AND A.minSalary <= '$maxSalary') 
                    OR 
                (A.maxSalary <= '$maxSalary' AND A.maxSalary >= '$minSalary')
                ) OR A.minSalary = '-1' AND A.minSalary = '-1'
                )";
        } else if (empty($minSalary) && $maxSalary != '') {
            $sql = $sql . " AND ( A.maxSalary >= '$maxSalary' OR A.maxSalary = '-1') ";
        }

        if ($type_job != '') {
            $sql = $sql . " AND A.typeJob = '$type_job'";
        }

        if ($year_experience != '') {
            $sql = $sql . " AND A.yearExperience = '$year_experience'";
        }
        if ($province != '') {
            $sql = $sql . " AND SUBSTRING_INDEX( A.address, ',', -1) = '$province'";
        }

        $sql = $sql . " ORDER BY A.code desc, A.num_click desc";

        $result = $conn->query($sql);

        $listDetail['job'] = [];

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

            $listDetail['length'] = count($listDetail['job']);
            $listDetail['message'] = 'successful';
        } else {
            $listDetail['message'] = 'no data';
        }

        $listDetail['today'] = date("d/m/Y");

        $listDetail['success'] = 1;
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
