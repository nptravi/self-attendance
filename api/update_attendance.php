<?php
session_start();
require '../db.php';
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}
$ret = [];
$ret["message"]= 'User_id found.';
$ret["success"]=false;
$data = json_decode(file_get_contents("php://input"), true);
//$ret["message"] = $ret["message"]."data=".json_encode($data); 
//echo json_encode($ret);
//exit;
$date = $data["date"] ?? null;
$code = $data["code"] ?? '';
$ret["message"] = $ret["message"]."date:".$_POST["date"]; 
$ret["message"] = $ret["message"]."code:".$_POST["code"]; 
//echo json_encode($ret);
//exit;
if (!$date) {
    $ret["message"] = $ret["message"].' date value is mandatory. Post parameters: '+json_encode($data);
    echo json_encode($ret);
    exit;
}
$ret["message"] = $ret["message"].' Going to write to database';
$user_id = $_SESSION["user_id"];


// If code is empty, delete attendance
if ($code === '') {
    $ret["message"] = $ret["message"].'code is empty. Going to clear record for that grade';
    $stmt = $conn->prepare("DELETE FROM attendance WHERE user_id=? AND date=?");
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $ret["success"]=true;
    $ret["message"]= $ret["message"]." Record deleted successfully";
    echo json_encode($ret);
    exit;
}

// Insert or update
$stmt = $conn->prepare("INSERT INTO attendance(user_id,date,code) VALUES(?,?,?) ON DUPLICATE KEY UPDATE code=?");
$stmt->bind_param("isss", $user_id, $date, $code, $code);
if ($stmt->execute()) {
    $ret["success"]=true;
    $ret["message"]= $ret["message"]." Record inserted/updated successfully";
    echo json_encode($ret); exit;
} else {
    $ret["success"]=false;
    $ret["message"]= $ret["message"]." insert/update failed.";
    echo json_encode($ret);    
}
?>