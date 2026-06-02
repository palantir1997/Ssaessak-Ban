<?php
session_start();

$login_id = $_POST['login_id'] ?? '';
$password = $_POST['password'] ?? '';

// [환자 발표용 기본 계정]
// DB 연결 여부와 상관없이 patient / 1234 는 환자 로그인 성공 처리합니다.
// 관리자 admin / 1234 계정과는 완전히 분리된 환자 세션만 사용합니다.
if ($login_id === 'patient' && $password === '1234') {
    $_SESSION['patient_id'] = 1;
    $_SESSION['patient_login_id'] = 'patient';
    $_SESSION['patient_name'] = '환자테스트';
    echo "<script>alert('환자테스트님 로그인되었습니다.'); location.href='../index.php';</script>";
    exit();
}

// 그 외 계정은 DB가 켜져 있을 때 patients 테이블에서 확인합니다.
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect('localhost', 'root', '', 'saessak');

if ($conn) {
    mysqli_set_charset($conn, 'utf8mb4');

    $stmt = @mysqli_prepare($conn, 'SELECT id, name, login_id FROM patients WHERE login_id = ? AND password = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $login_id, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['patient_id'] = $row['id'];
            $_SESSION['patient_login_id'] = $row['login_id'];
            $_SESSION['patient_name'] = $row['name'];
            echo "<script>alert('" . addslashes($row['name']) . "님 로그인되었습니다.'); location.href='../index.php';</script>";
            exit();
        }
    }
}

// DB가 꺼져 있어도 에러를 띄우지 않고, 로그인 실패 안내 후 메인으로 복귀합니다.
echo "<script>alert('환자 로그인 실패: ID 또는 비밀번호를 확인해 주세요.'); location.href='../index.php';</script>";
exit();
?>
