<?php
session_start();

$user_id = $_POST['user_id'] ?? '';
$password = $_POST['password'] ?? '';

// 관리자 로그인은 기존 설정 유지: admin / 1234
if ($user_id === 'admin' && $password === '1234') {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['admin_id'] = $user_id;
    $_SESSION['role'] = 'admin'; // ← 이거 추가!
    header('Location: ../DashBoard.php');
    exit();
}
?>
