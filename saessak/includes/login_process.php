<?php
session_start();

include_once __DIR__ . '/db.php';

$user_id  = trim($_POST['user_id']  ?? '');
$password = trim($_POST['password'] ?? '');

$login_success = false;

// 1. staff_accounts 테이블에서 로그인 확인
if ($conn) {
    $login_stmt = mysqli_prepare($conn, 'SELECT user_id, name FROM staff_accounts WHERE user_id = ? AND password = ? AND status = "활성" LIMIT 1');

    if ($login_stmt) {
        mysqli_stmt_bind_param($login_stmt, 'ss', $user_id, $password);
        mysqli_stmt_execute($login_stmt);
        $result = mysqli_stmt_get_result($login_stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $login_success = true;
            $_SESSION['admin_id']   = $row['user_id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['login_type'] = 'admin';
        }

        mysqli_stmt_close($login_stmt);
    }
}

// 2. 하드코딩 테스트 계정 (DB 계정 없을 때 대체)
if (!$login_success) {
    if ($user_id === 'admin' && $password === '1234') {
        $login_success = true;
        $_SESSION['admin_id']   = 'admin';
        $_SESSION['admin_name'] = '관리자';
        $_SESSION['login_type'] = 'admin';
    }
}

// 3. 성공 / 실패 처리
if ($login_success) {
    echo "<script>
        alert('✅ 관리자 로그인되었습니다.');
        location.href='../dashboard.php';
    </script>";
    exit();

} else {
    // login_attempts 테이블 생성 (user_id 컬럼 포함)
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

    // IP + 시도한 아이디 함께 기록
    $fail_stmt = mysqli_prepare($conn, "INSERT INTO login_attempts (ip_address, user_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($fail_stmt, 'ss', $user_ip, $user_id);
    $recorded = mysqli_stmt_execute($fail_stmt);
    mysqli_stmt_close($fail_stmt);

    if (!$recorded) {
        die("DB 기록 실패! 에러: " . mysqli_error($conn));
    }

    echo "<script>
        alert('❌ 로그인 실패!');
        history.back();
    </script>";
    exit();
}
?>