<?php
$target_path = "../../";
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
	echo json_encode($data);
    if (isset($data['code'])) {
		echo "OK";
        $code = $conn->real_escape_string($data['code']);
		echo '2';
        $sql_check = "SELECT * FROM jh_user_cv WHERE code = '$code'";
        $result = $conn->query($sql_check);
		echo "prepare";
		echo $conn;
        if ($result->num_rows > 0) {
			echo "Check";
            while ($row = $result->fetch_assoc()) {
                $file = parse_url($row['cv_url']);
				echo $target_path . $file['path'];
                
				if (unlink($target_path . $file['path'])) {
					echo "Unlink";
					
                    $sql_delete = "DELETE FROM jh_user_cv WHERE code = '$code'";

                    if ($conn->query($sql_delete) === TRUE) {
                        $response['success'] = 1;
                        $response['message'] = "CV has been deleted.";
                    } else {
                        $response['success'] = 0;
                        $response['message'] = 'CV hasnt been deleted.';
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
