<?php
include "../config/dbconnect.php";

$listDetail = array();
$txtdata = 'data';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $sql = "SELECT * FROM jh_address_district";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $listDetail['success'] = 1;
                $listDetail['message'] = 'successful';
                $listDetail['district'] = [];
                while ($row = $result->fetch_assoc()) {
                    $listDetail["district"][] = $row;
                }
        
                header('Content-Type: application/json');
                echo json_encode([$txtdata => $listDetail],JSON_UNESCAPED_UNICODE);
            } else {
                $listDetail['success'] = 0;
                $listDetail['message'] = 'unsuccessful';
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode([$txtdata => $listDetail]);
            }
    
        
} else {
    $listDetail['success'] = 0;
    $listDetail['message'] = 'Method not allowed';
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([$txtdata => $listDetail]);
}

$conn->close();
?>