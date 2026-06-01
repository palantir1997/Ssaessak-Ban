<?php
session_start();

$user_id = $_POST['user_id'];
$password = $_POST['password'];

// 로그인 성공 시
if ($user_id === "admin" && $password === "1234") {
    $_SESSION['user_id'] = $user_id; // 세션 데이터 저장
    header("Location: ../dashboard.php"); // 대시보드로 이동
    exit();
} else {
    echo "<script>alert('로그인 실패'); history.back();</script>";
    exit();
}
?>