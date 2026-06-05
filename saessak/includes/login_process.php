<?php
session_start();

// 데이터베이스 연결
include_once __DIR__ . '/db.php';

$user_id = trim($_POST['user_id'] ?? '');
$password = trim($_POST['password'] ?? '');

$login_success = false;

// 1️⃣ 먼저 데이터베이스의 staff_accounts 테이블에서 검색
if ($conn) {
    $stmt = mysqli_prepare($conn, 'SELECT user_id, name FROM staff_accounts WHERE user_id = ? AND password = ? AND status = "활성" LIMIT 1');
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $user_id, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $login_success = true;
            $_SESSION['admin_id'] = $row['user_id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['login_type'] = 'admin'; // 관리자 구분 플래그
        }
        
        mysqli_stmt_close($stmt);
    }
}

// 2️⃣ DB가 없거나 계정이 없으면 하드코딩된 관리자 계정으로 대체 (테스트용)
if (!$login_success) {
    $test_admin_id = 'admin';
    $test_admin_pw = '1234';
    
    if ($user_id === $test_admin_id && $password === $test_admin_pw) {
        $login_success = true;
        $_SESSION['admin_id'] = $test_admin_id;
        $_SESSION['admin_name'] = '관리자';
        $_SESSION['login_type'] = 'admin';
    }
}

// 3️⃣ 로그인 성공 여부 판단
if ($login_success) {
    echo "<script>
        alert('✅ 관리자 로그인되었습니다.');
        location.href='../dashboard.php';
    </script>";
    exit();
} else {
    echo "<script>
        alert('❌ 아이디 또는 비밀번호가 틀렸습니다.\\n\\n테스트 계정: admin / 1234');
        history.back();
    </script>";
    exit();
}
?>