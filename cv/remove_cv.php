<?php
include "../config/dbconnect.php";
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['code'])) {
        $code = $conn->real_escape_string($data['code']);

        $sql_delete = "DELETE FROM jh_user_cv WHERE code = '$code'";

        if ($conn->query($sql_delete) === TRUE) {
            $response['success'] = 1;
            $response['message'] = "CV has been deleted.";
        } else {
            $response['success'] = 0;
            $response['message'] = 'CV removed unsuccessful.';
        }
    } else {
        $response['success'] = 0;
        $response['message'] = 'Missing uid parameter.';
    }
}

echo json_encode($response);
?>