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
}  else {
    // 1. 테이블 강제 생성 (로그인이 실패할 때마다 확인)
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45),
        attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    // 2. 기록 시도
    $query = "INSERT INTO login_attempts (ip_address) VALUES ('$user_ip')";
    $result = mysqli_query($conn, $query);
    
    // 3. 만약 실패하면 에러 출력 (범인 잡기)
    if (!$result) {
        die("DB 기록 실패! 에러 내용: " . mysqli_error($conn));
    }

    echo "<script>
        alert('❌ 로그인 실패! (DB에 기록 시도함)');
        history.back();
    </script>";
    exit();
}
?>