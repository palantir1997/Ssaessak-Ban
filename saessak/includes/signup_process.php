<?php
session_start();
include_once __DIR__ . '/db.php';

$name = trim($_POST['name'] ?? '');
$login_id = trim($_POST['login_id'] ?? '');
$password = trim($_POST['password'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (empty($name) || empty($login_id) || empty($password) || empty($phone)) {
    echo "<script>alert('모든 필드를 입력해주세요.'); history.back();</script>";
    exit();
}

// ✅ 패스워드 정책 검증 추가
if (strlen($password) < 8) {
    echo "<script>alert('비밀번호는 8자 이상이어야 합니다.'); history.back();</script>";
    exit();
}
if (!preg_match('/[A-Z]/', $password)) {
    echo "<script>alert('비밀번호에 대문자를 포함해야 합니다.'); history.back();</script>";
    exit();
}
if (!preg_match('/[a-z]/', $password)) {
    echo "<script>alert('비밀번호에 소문자를 포함해야 합니다.'); history.back();</script>";
    exit();
}
if (!preg_match('/[0-9]/', $password)) {
    echo "<script>alert('비밀번호에 숫자를 포함해야 합니다.'); history.back();</script>";
    exit();
}
if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':\\|,.<>\/?]/', $password)) {
    echo "<script>alert('비밀번호에 특수문자를 포함해야 합니다.'); history.back();</script>";
    exit();
}

if (!$conn) {
    echo "<script>alert('연결 실패'); history.back();</script>";
    exit();
}

$check = mysqli_prepare($conn, 'SELECT id FROM patients WHERE login_id = ? LIMIT 1');
mysqli_stmt_bind_param($check, 's', $login_id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);
if (mysqli_stmt_num_rows($check) > 0) {
    echo "<script>alert('이미 사용 중인 아이디입니다.'); history.back();</script>";
    mysqli_stmt_close($check);
    exit();
}
mysqli_stmt_close($check);

$stmt = mysqli_prepare($conn, 'INSERT INTO patients (name, login_id, password, phone) VALUES (?, ?, ?, ?)');
if (!$stmt) {
    $err = mysqli_error($conn);
    echo "<script>alert('쿼리 준비 실패: $err'); history.back();</script>";
    exit();
}

mysqli_stmt_bind_param($stmt, 'ssss', $name, $login_id, $password, $phone);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('회원가입 완료!'); location.href='../index.php';</script>";
} else {
    echo "<script>alert('회원 가입 실패!'); history.back();</script>";
}
mysqli_stmt_close($stmt);
?>