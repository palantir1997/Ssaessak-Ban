<?php
session_start();
include_once __DIR__ . '/db.php';

$login_id = trim($_POST['login_id'] ?? '');
$password = trim($_POST['password'] ?? '');
$login_success = false;
$patient_id = 0;
$patient_name = '';

if ($conn) {
    $stmt = mysqli_prepare($conn, 'SELECT id, name FROM patients WHERE login_id = ? AND password = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $login_id, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $login_success = true;
            $patient_id = $row['id'];
            $patient_name = $row['name'];
        }
        mysqli_stmt_close($stmt);
    }
}

if ($login_success) {
    $_SESSION['patient_id']       = $patient_id;
    $_SESSION['patient_login_id'] = $login_id;
    $_SESSION['patient_name']     = $patient_name;
    $_SESSION['login_type']       = 'patient';
    echo "<script>alert('✅ {$patient_name}님 로그인되었습니다.'); location.href='../index.php';</script>";
    exit();

} else {
    // login_attempts 테이블 생성
    mysqli_query($conn, "
        CREATE TABLE IF NOT EXISTS login_attempts (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            ip_address   VARCHAR(45),
            user_id      VARCHAR(100),
            attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    $user_ip = $_SERVER['REMOTE_ADDR'];
    if ($user_ip === '::1') $user_ip = '127.0.0.1';

    // 실패한 IP + 시도한 아이디 기록
    $fail_stmt = mysqli_prepare($conn, "INSERT INTO login_attempts (ip_address, user_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($fail_stmt, 'ss', $user_ip, $login_id);
    mysqli_stmt_execute($fail_stmt);
    mysqli_stmt_close($fail_stmt);

    echo "<script>alert('❌ 로그인 실패\\nID 또는 비밀번호를 확인해주세요.'); history.back();</script>";
    exit();
}
?>