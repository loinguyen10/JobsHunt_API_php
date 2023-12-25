<?php
include "../config/dbconnect.php";

use PHPMailer\PHPMailer\PHPMailer;

require "../../../../php/phpmailer/src/PHPMailer.php";
require "../../../../php/phpmailer/src/Exception.php";
require "../../../../php/phpmailer/src/SMTP.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['email']) && isset($data['type_code'])) {

        $email = $conn->real_escape_string($data['email']);
        $type_code = $conn->real_escape_string($data['type_code']);
        $verification_code = generateRandomCode();
        $expiry_time = time() + 600;

        $sql_get_email = "SELECT * FROM jh_app_user WHERE email = '$email'";
        $result_get_email = $conn->query($sql_get_email);

        if ($result_get_email->num_rows > 0) {
            if ($type_code == 'RePassOTP') {
                sendMail($conn,$email,$verification_code,$expiry_time,$type_code);
            } else if ($type_code == 'RegisterOTP') {
                $response['success'] = 2;
                $response['message'] = 'account exist';
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
            } else {
                $response['success'] = 0;
                $response['message'] = 'Wrong type of OTP code';
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        } else {
            if ($type_code == 'RePassOTP') {
                $response['success'] = 0;
                $response['message'] = 'No email found';
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
            } else if ($type_code == 'RegisterOTP') {
                sendMail($conn,$email,$verification_code,$expiry_time,$type_code);
            } else {
                $response['success'] = 0;
                $response['message'] = 'Wrong type of OTP code';
				echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        }
    } else {
        $response['success'] = 0;
        $response['message'] = 'Missing in the request';
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
} else {
    $response['success'] = 0;
    $response['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
	echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

function generateRandomCode()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $code = '';
    for ($i = 0; $i < 2; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    for ($i = 0; $i < 3; $i++) {
        $code .= strtoupper($characters[rand(0, strlen($characters) - 1)]);
    }
    return $code;
}

function sendMail($conn,$email, $code, $expiry_time, $type_code)
{
    $subject = "Your OTP Code for JobsHunt";
    $message = "Your OTP Code is: " . $code;

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->ContentType = 'text/plain';

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'hungooso4413@gmail.com';
    $mail->Password = 'asgy bzrb uvku hakj';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('admin@jobshunt.com', 'JobsHunt Admin');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;

    if ($mail->send()) {
        $sql_insert_code = "INSERT INTO jh_otp_code (code, email, expiry_time, type_code) 
            VALUES ('$code', '$email', FROM_UNIXTIME($expiry_time), '$type_code')";
        if ($conn->query($sql_insert_code) === TRUE) {
            $response['success'] = 1;
            $response['message'] = "Email sent successfully to $email with verification code: $code.";
        } else {
            $response['success'] = 0;
            $response['message'] = "Failed to save verification code and ID in the database: " . $conn->error;
        }

        header('Content-Type: application/json');
    } else {
        $response['success'] = 0;
        $response['message'] = "Failed to send email to $email";
        header("HTTP/1.1 500 Internal Server Error");
    }
	
	echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

$conn->close();
