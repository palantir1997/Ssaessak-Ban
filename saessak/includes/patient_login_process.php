<?php
session_start();
include_once __DIR__ . '/db.php';
$login_id = trim($_POST['login_id'] ?? '');
$password = trim($_POST['password'] ?? '');
$login_success = false;
$patient_id = 0;
$patient_name = '';
// 1️⃣ 데이터베이스에서 환자 계정 검색
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
        }
        
        mysqli_stmt_close($stmt);
    }
}
// 2️⃣ DB가 없거나 계정이 없으면 테스트 환자 계정으로 대체
if (!$login_success) {
    $test_patient_id = 'patient';
    $test_patient_pw = '1234';
    $test_patient_name = '환자테스트';
    
    if ($login_id === $test_patient_id && $password === $test_patient_pw) {
        $login_success = true;
        $patient_id = 1;
        $patient_name = $test_patient_name;
    }
}
// 3️⃣ 로그인 성공 여부 판단
if ($login_success) {
    // 환자 세션 설정 (관리자 세션과는 분리)
    $_SESSION['patient_id'] = $patient_id;
    $_SESSION['patient_login_id'] = $login_id;
    $_SESSION['patient_name'] = $patient_name;
    $_SESSION['login_type'] = 'patient'; // 환자 구분 플래그
    
    echo "<script>
        alert('✅ {$patient_name}님 로그인되었습니다.');
        location.href='../index.php';
    </script>";
    exit();
} else {
    echo "<script>
        alert('❌ 로그인 실패\\nID 또는 비밀번호를 확인해주세요.\\n\\n테스트 계정: patient / 1234');
        history.back();
    </script>";
    exit();
}
?>