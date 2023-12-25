<?php

include "../config/dbconnect.php";

$jobDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['uid'])) {
        $uid = $conn->real_escape_string($data['uid']);

        $sql_recommend = "SELECT * FROM jh_user_profile_recommend_setting WHERE uid = '$uid'";
        $result_recommend = $conn->query($sql_recommend);

        $jobDetail['job'] = [];

        if ($result_recommend->num_rows > 0) {
            while ($row_recommend = $result_recommend->fetch_assoc()) {
                //temp array
                $job_recommend = explode(",", $row_recommend['job']);
                $year_recommend = $row_recommend['yearExperience'];
                $province_recommend = explode(",", $row_recommend['workProvince']);
                $min_recommend = $row_recommend['minSalary'];
                $max_recommend = $row_recommend['maxSalary'];
                $currency_recommend = $row_recommend['currency'];

                $now = time();

                $sql_job = "SELECT * FROM jh_job_detail 
							WHERE yearExperience <= '$year_recommend' 
							AND active = '1' 
							AND deadline >= FROM_UNIXTIME($now)
							AND ((
							(minSalary >= '$min_recommend' AND minSalary <= '$max_recommend') 
								OR 
							(maxSalary <= '$max_recommend' AND maxSalary >= '$min_recommend')
							) OR minSalary = '-1' AND minSalary = '-1'
                            )";

                $result_job = $conn->query($sql_job);

                if ($result_job->num_rows > 0) {
                    while ($row_job = $result_job->fetch_assoc()) {
                        //temp array
                        $row_job['deadline'] = date("d/m/Y", strtotime($row_job['deadline']));
                        $companyId = $row_job['companyId'];
                        $sqlx = "SELECT * FROM `jh_company_profile` WHERE `uid` = $companyId";
                        $resultx = $conn->query($sqlx);

                        if ($resultx->num_rows > 0) {
                            $row_job['company'] = [];
                            while ($rowx = $resultx->fetch_assoc()) {
                                //temp array
                                $row_job['company'] = $rowx;
                            }
                        }

                        $idJob = $row_job['code'];
                        $sql_approve = "SELECT * FROM `jh_job_application` WHERE job_id = '$idJob' AND approve = '1'";
                        $result_approve = $conn->query($sql_approve);
                        $numberCV = $result_approve->num_rows;

                        $row_job['remain_people'] = $row_job['numberCandidate'] - $numberCV;

                        if ($row_job['remain_people'] < 0) {
                            $row_job['remain_people'] = 0;
                        }

                        foreach ($job_recommend as $job_title) {
                            $job_title_words = explode(" ", $job_title);
                            $jobAdded = [];

                            foreach ($job_title_words as $word) {
                                if ((str_contains($row_job['tag'], $word) || str_contains($row_job['candidateRequirement'], $word)) && $jobAdded != $row_job) {
                                    foreach ($province_recommend as $province_code) {
                                        if (strcmp($province_code, substr($row_job['address'], strpos($row_job['address'], ",") + 1)) == 0) {
                                            $jobDetail["job"][] = $row_job;
                                            $jobAdded = $row_job;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    header('Content-Type: application/json');
                    $jobDetail['success'] = 1;
                    $jobDetail['message'] = 'successful';
                    $jobDetail['length'] = count($jobDetail["job"]);
                    echo json_encode([$txtdata => $jobDetail], JSON_UNESCAPED_UNICODE);
                } else {
                    $jobDetail['success'] = 0;
                    $jobDetail['message'] = 'no job';
                    $jobDetail['length'] = 0;
                    echo json_encode([$txtdata => $jobDetail]);
                }
            }
        } else {
            $sql_all = "SELECT * FROM jh_job_detail";
            $result_all = $conn->query($sql_all);

            if ($result_all->num_rows > 0) {
                while ($row_all = $result_all->fetch_assoc()) {
                    $sql_home = "SELECT * FROM `jh_user_profile` WHERE `uid` = $uid";
                    $result_home = $conn->query($sql_home);

                    if ($result_home->num_rows > 0) {
                        while ($row_home = $result_home->fetch_assoc()) {
                            //temp array
                            $addHome = $row_home["address"];
                            $addHome2 = substr($addHome, strrpos($addHome, ",") + 1);

                            if (strcmp($addHome2, substr($row_all['address'], strpos($row_all['address'], ",") + 1)) == 0) {
                                $row_all['deadline'] = date("d/m/Y", strtotime($row_job['deadline']));
                                $companyId = $row_all['companyId'];
                                $sqlx = "SELECT * FROM `jh_company_profile` WHERE `uid` = $companyId";
                                $resultx = $conn->query($sqlx);

                                if ($resultx->num_rows > 0) {
                                    $row_all['company'] = [];
                                    while ($rowx = $resultx->fetch_assoc()) {
                                        //temp array
                                        $row_all['company'] = $rowx;
                                    }
                                }

                                $idJob = $row_all['code'];
                                $sql_approve = "SELECT * FROM `jh_job_application` WHERE job_id = '$idJob' AND approve = '1'";
                                $result_approve = $conn->query($sql_approve);
                                $numberCV = $result_approve->num_rows;

                                $row_all['remain_people'] = $row_all['numberCandidate'] - $numberCV;

                                if ($row_all['remain_people'] < 0) {
                                    $row_all['remain_people'] = 0;
                                }

                                $jobDetail["job"][] = $row_all;
                            }
                        }
                    }
                }
            }

            if (count($jobDetail["job"]) > 0) {
                $jobDetail['length'] = count($jobDetail["job"]);
                $jobDetail['success'] = 1;
                $jobDetail['message'] = 'no recommend but still has jobs in your home';
            } else {
                $jobDetail['success'] = 0;
                $jobDetail['message'] = 'no recommend and no job in your home';
            }

            header('Content-Type: application/json');
            echo json_encode([$txtdata => $jobDetail], JSON_UNESCAPED_UNICODE);
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
