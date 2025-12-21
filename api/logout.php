<?php
session_start();
require '../db.php';

if(isset($_SESSION['user_id'])){
    $user_id=$_SESSION['user_id'];
    $stmt=$conn->prepare("UPDATE users SET remember_token=NULL WHERE id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
}

setcookie('remember_me','',time()-3600,'/');
session_destroy();

echo json_encode(['success'=>true]);
?>
