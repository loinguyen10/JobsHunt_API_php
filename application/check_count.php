<?php

include "../config/dbconnect.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['user_id']) && isset($data['title'])) {

        $user_id = $conn->real_escape_string($data['user_id']);
        $title = $conn->real_escape_string($data['title']);
        $today = date("Y-m-d");

        $sql = "SELECT * FROM jh_basic_count WHERE user_id = '$user_id' AND title = '$title' AND DATE(created_time) = '$today'";
        $result = $conn->query($sql);
        $number = $result->num_rows;
        $check = true;

        if ($title == 'candidate_upload_cv') {
            $sql_checkcv = "SELECT * FROM jh_user_cv WHERE user_id = '$user_id'";
            $result_checkcv = $conn->query($sql_checkcv);
            $number_checkcv = $result_checkcv->num_rows;

            if ($number >= 3) {
                $check = false;
                $response['success'] = 3;
                $response['message'] = 'More than 3-' . $title;
            }

            if ($number_checkcv >= 3) {
                $check = false;
                $response['success'] = 3;
                $response['message'] = 'More than 3-candidate_cv';
            }
        }

        if ($title = 'candidate_apply_job' && $number >= 5) {
            $check = false;
            $response['success'] = 3;
            $response['message'] = 'More than 5-' . $title;
        }

        if (str_contains($title, 'candidate_recommend_job_') && $number >= 3) {
            $check = false;
            $response['success'] = 3;
            $response['message'] = 'More than 5-' . $title;
        }

        if ($title = 'recruiter_post_job' && $number >= 5) {
            $check = false;
            $response['success'] = 3;
            $response['message'] = 'More than 5-' . $title;
        }

        if ($title = 'recruiter_job_appication' && $number >= 10) {
            $check = false;
            $response['success'] = 3;
            $response['message'] = 'More than 10-' . $title;
        }

        if (str_contains($title, 'recruiter_edit_job_') && $number >= 3) {
            $check = false;
            $response['success'] = 3;
            $response['message'] = 'More than 3-' . $title;
        }

        if($check){
            $response['success'] = 1;
            $response['message'] = 'Accept';
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
