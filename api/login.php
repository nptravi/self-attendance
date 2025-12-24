<?php
session_start();
require '../db.php';
$data = json_decode(file_get_contents('php://input'), true);

$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');
$remember = $data['remember'] ?? false;

if (!$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'Username and password required']);
    exit;
}

$stmt = $conn->prepare("SELECT id,password,username FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$ret = ['success' => false, 'message' => 'dbquery ran successfully'];

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        // Remember me
        if ($remember) {
            setcookie('remember_me', $token, [
                'expires' => time() + 86400 * 30,
                'path' => '/',
                'httponly' => true, // Prevents JavaScript from reading the cookie
                'samesite' => 'Lax'
            ]);
            setcookie('remember_me', $token, time() + 60 * 60 * 24 * 30, '/'); // 30 days
            $stmt2 = $conn->prepare("UPDATE users SET remember_token=? WHERE id=?");
            $stmt2->bind_param("si", $token, $row['id']);
            $stmt2->execute();
            $ret['message'] = $ret['message'] . 'remeber_token set. ';
        }
        $ret['message'] = $ret['message'] . 'Login Success';
        $ret['success'] = true;
        echo json_encode($ret);
        exit;
    }
}
$ret['message'] = $ret['message'] . 'Invalid credentials';
echo json_encode($ret);
?>