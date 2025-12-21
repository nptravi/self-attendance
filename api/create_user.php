<?php
session_start();
require '../db.php';
$data = json_decode(file_get_contents('php://input'), true);

$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');
$admin_password = trim($data['admin_password'] ?? '');

// Simple admin password check
$ADMIN_PASS = $ENV['ADMIN_PASSWORD'];
if ($admin_password !== $ADMIN_PASS) {
    echo json_encode(['success' => false, 'message' => 'Invalid admin password ']);
    exit;
}

if (!$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'Username and password required']);
    exit;
}

// check exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    echo json_encode(['success' => false, 'message' => 'Username already exists']);
    exit;
}

// insert
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users(username,password) VALUES(?,?)");
$stmt->bind_param("ss", $username, $hash);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Insert failed']);
}
?>