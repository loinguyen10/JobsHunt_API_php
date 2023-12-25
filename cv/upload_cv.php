<?php
$target_path = "../../../cv/";
$check = 1;
$response = array();

//if (file_exists($target_path)) {
//  echo "Sorry, file already exists.";
//  $check = 0;
//	$response['success'] = 0;
//   $response['message'] = 'upload avatar unsuccessfully';
//}

if(isset($_POST['uid'])){
	mkdir("../../../cv/" . $_POST['uid']);
    $target_path = $target_path . $_POST['uid'] . "/" . basename($_FILES['uploadedfile']['name']);
	
	if (!is_dir("../../../cv/" . $_POST['uid'])) {
        // Directory does not exist, create it
        if (!mkdir("../../../cv/" . $_POST['uid'], 0777, true)) {
            // Failed to create directory
            $response['success'] = 0;
            $response['message'] = 'Failed to create directory.';
        }
    }
	
    if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
        $response['success'] = 1;
        $response['message'] = "File uploaded successfully. The file " . basename($_FILES['uploadedfile']['name']) . " of " . $_POST['uid'] . " has been uploaded.";
    } else {
        $response['success'] = 0;
        $response['message'] = 'File upload unsuccessful.';
    }
} else {
    $response['success'] = 0;
    $response['message'] = 'Missing uid parameter.';
}

echo json_encode($response);
?>