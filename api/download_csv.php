<?php
session_start();
require '../db.php';
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$user_id = $_SESSION['user_id'];
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendance.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Date', 'Code']);

$stmt = $conn->prepare("SELECT date, code FROM attendance WHERE user_id=? ORDER BY date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    fputcsv($out, [$row['date'], $row['code']]);
}

fclose($out);
exit;
?>