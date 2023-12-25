<?php
include "../config/dbconnect.php";

$target_path = "../../../..";
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
	
    if (isset($data['code'])) {
        $code = $conn->real_escape_string($data['code']);

        $sql_check = "SELECT * FROM jh_user_cv WHERE code = '$code'";
        $result = $conn->query($sql_check);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $file = parse_url($row['cv_url']);
                
				if (unlink($target_path . $file['path'])) {
					
                    $sql_delete = "DELETE FROM jh_user_cv WHERE code = '$code'";

                    if ($conn->query($sql_delete) === TRUE) {
                        $response['success'] = 1;
                        $response['message'] = "CV " . $target_path . $file['path'] ." has been deleted.";
                    } else {
                        $response['success'] = 0;
                        $response['message'] = 'CV SQL removed unsuccessful.';
                    }
                } else {
                    $response['success'] = 0;
                    $response['message'] = 'CV hasnt been deleted.';
                }
            }
        }else{
		$response['success'] = 0;
        $response['message'] = 'Missing code parameter.';
		}
    } else {
        $response['success'] = 0;
        $response['message'] = 'Missing code parameter.';
    }
} else {
    $response['success'] = 0;
    $response['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
}

echo json_encode($response);
