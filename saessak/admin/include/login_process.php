<?php
session_start();

$user_id = $_POST['user_id'];
$password = $_POST['password'];

if ($user_id === "admin" && $password === "1234") {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = 'admin'; // ← 이 한 줄만 추가
    header("Location: ../dashboard.php");
    exit();
} else {
    echo "<script>alert('로그인 실패'); history.back();</script>";
    exit();
}
?>