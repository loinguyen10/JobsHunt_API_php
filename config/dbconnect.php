<?php

$server = "localhost";  // thông tin server 
$u = "nhjobxpt_united7";  // tên user
$p = "United.7_JH_HTH"; // mât khẩu
$db = "nhjobxpt_utd7.jobshunt"; // tên database

$conn = new mysqli($server, $u, $p, $db);
mysqli_set_charset($conn,'utf8');


if ($conn->connect_error) {
    header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => 'Database connection failed']);
}

if(!$conn) {
    die("Connection Failed:".mysqli_connect_error());
}

?>