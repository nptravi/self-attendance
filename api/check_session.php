<?php
session_start();
require '../db.php';

$ret = [];
$ret['loggedIn'] = false;
$ret['message'] = 'started. ';

if (isset($_SESSION['user_id'])) {
    $ret['loggedIn'] = true;
    $ret['userName'] = $_SESSION['username'];
    $ret['message'] = $ret['message'] . 'user already logged in. Continuing the session';
    echo json_encode($ret);
    exit;
}

if (isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE remember_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $ret['loggedIn'] = true;
        $ret['userName'] = $row['username'];
        $ret['message'] = $ret['message'] . 'logged in through remember_me';
        echo json_encode($ret);
        exit;
    }    
}
$ret['message'] = $ret['message'] . 'user not logged in. login form to be loaded';


echo json_encode($ret);
exit;
