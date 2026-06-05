<?php
session_start();
include_once __DIR__ . '/db.php';

if (!isset($_SESSION['patient_login_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); history.back();</script>";
    exit();
}

$patient_name = trim($_POST['patient_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$dept = trim($_POST['dept'] ?? '');
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');
$memo = trim($_POST['memo'] ?? '');

// 접수번호 자동 생성 (RS-XXXXXXXX)
$reception_no = 'RS-' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);

$stmt = mysqli_prepare($conn, 
    'INSERT INTO receptions (reception_no, patient_name, reception_type, dept_name, target_date, target_time, symptoms_memo, status) 
     VALUES (?, ?, "예약", ?, ?, ?, ?, "대기중")'
);

mysqli_stmt_bind_param($stmt, 'ssssss', $reception_no, $patient_name, $dept, $date, $time, $memo);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>
        alert('✅ 예약이 완료되었습니다!\\n접수번호: $reception_no');
        location.href='../index.php';
    </script>";
} else {
    $err = mysqli_stmt_error($stmt);
    echo "<script>alert('예약 실패: $err'); history.back();</script>";
}
mysqli_stmt_close($stmt);
?>