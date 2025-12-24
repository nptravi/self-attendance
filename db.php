<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);
header('Content-Type: application/json');

global $ENV;
$ENV = [];

// Load environment
if(file_exists(__DIR__ . '/.env')){
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($lines as $line){
        if(strpos(trim($line),'#')===0) continue;
        [$key,$val] = explode('=', $line,2);
        $ENV[trim($key)]=trim($val);        
    }
}

$host = $ENV['DB_HOST'];
$user = $ENV['DB_USER']; 
$pass = $ENV['DB_PASS'];
$dbname = $ENV['DB_NAME'];

// Connect MySQL
$conn = new mysqli($host,$user,$pass);
if($conn->connect_error){
    echo json_encode(['success'=>false,'message'=>'DB connection failed']); exit;
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($dbname);

// Create tables if not exist
$conn->query("CREATE TABLE IF NOT EXISTS users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(255) DEFAULT NULL
)");

$conn->query("CREATE TABLE IF NOT EXISTS attendance(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    code ENUM('A','B','C','G','O','L','TR','HL') DEFAULT NULL,
    UNIQUE(user_id,date),
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
)");
?>
