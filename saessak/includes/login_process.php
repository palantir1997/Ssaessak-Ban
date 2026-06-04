<?php
session_start();
include_once __DIR__ . '/db.php';

$login_id = trim($_POST['login_id'] ?? '');
$password = trim($_POST['password'] ?? '');

// 환자 웹 전용 테스트 계정. 관리자(admin) 계정과 분리합니다.
$demo_patient_id = 'patient';
$demo_patient_pw = '1234';
$demo_patient_name = '환자테스트';

$login_success = false;
$patient_id = 0;
$patient_name = '';

if ($conn) {
    $stmt = mysqli_prepare($conn, 'SELECT id, name, login_id FROM patients WHERE login_id = ? AND password = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $login_id, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $login_success = true;
            $patient_id = $row['id'];
            $patient_name = $row['name'];
            $login_id = $row['login_id'];
        }
    }
}

// DB가 꺼져 있거나 patients 테이블이 아직 없어도 발표용 환자 계정은 로그인되게 유지합니다.
if (!$login_success && $login_id === $demo_patient_id && $password === $demo_patient_pw) {
    $login_success = true;
    $patient_id = 1;
    $patient_name = $demo_patient_name;
}

if ($login_success) {
    // 환자 계정 전용 세션. admin 세션은 건드리지 않습니다.
    $_SESSION['patient_id'] = $patient_id;
    $_SESSION['patient_login_id'] = $login_id;
    $_SESSION['patient_name'] = $patient_name;
    echo "<script>alert('" . addslashes($patient_name) . "님 로그인되었습니다.'); location.href='../index.php';</script>";
    exit();
}

echo "<script>alert('환자 로그인 실패: ID 또는 비밀번호를 확인해 주세요.'); location.href='../index.php';</script>";
exit();
?>
