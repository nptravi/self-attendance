<?php

session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$user_id = $_SESSION['user_id'];

// Modes
$all = isset($_GET['all']);
$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;

if ($all) {
    $stmt = $conn->prepare("SELECT date,code FROM attendance WHERE user_id=? ORDER BY date DESC");
    $stmt->bind_param("i", $user_id);
} elseif ($from && $to) {
    $stmt = $conn->prepare("SELECT date,code FROM attendance WHERE user_id=? AND date BETWEEN ? AND ? ORDER BY date ASC");
    $stmt->bind_param("iss", $user_id, $from, $to);
} else {
    // default last 90 days
    $start = date('Y-m-d', strtotime('-90 days'));
    $stmt = $conn->prepare("SELECT date,code FROM attendance WHERE user_id=? AND date>=? ORDER BY date ASC");
    $stmt->bind_param("is", $user_id, $start);
}

$stmt->execute();
$result = $stmt->get_result();
$attendance = [];
while ($row = $result->fetch_assoc())
    $attendance[$row['date']] = $row['code'];

echo json_encode($attendance);
?>