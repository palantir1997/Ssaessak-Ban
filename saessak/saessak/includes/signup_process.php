<?php
session_start();
include_once __DIR__ . '/db.php';

$name = trim($_POST['name'] ?? '');
$login_id = trim($_POST['login_id'] ?? '');
$password = trim($_POST['password'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (!$conn) {
    echo "<script>alert('회원가입은 MySQL 실행 후 사용할 수 있습니다. XAMPP에서 MySQL을 Start 해주세요.'); location.href='../index.php';</script>";
    exit();
}

$stmt = mysqli_prepare($conn, 'INSERT INTO patients (name, login_id, password, phone) VALUES (?, ?, ?, ?)');
if (!$stmt) {
    echo "<script>alert('회원가입 실패: patients 테이블을 확인해 주세요.'); location.href='../index.php';</script>";
    exit();
}

mysqli_stmt_bind_param($stmt, 'ssss', $name, $login_id, $password, $phone);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('회원가입 완료! 로그인해 주세요.'); location.href='../index.php';</script>";
    exit();
}

echo "<script>alert('회원가입 실패: 이미 사용 중인 ID일 수 있습니다.'); location.href='../index.php';</script>";
exit();
?>
