<?php
$target_path = "../../../img/company/avatar/";
$check = 1;
$response = array();

$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

//if (file_exists($target_path)) {
//  echo "Sorry, file already exists.";
//  $check = 0;
//	$response['success'] = 0;
//   $response['message'] = 'upload avatar unsuccessfully';
//}

if($check == 1){
	if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
		$response['success'] = 1;
        $response['message'] = "upload avatar successfully - The file ".  basename( $_FILES['uploadedfile']['name']). " has been uploaded";
	} else{
		$response['success'] = 0;
        $response['message'] = 'upload avatar unsuccessfully';
	}
}

echo json_encode($response);
?>