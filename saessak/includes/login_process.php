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
    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    // 1. 실패 기록 저장
    mysqli_query($conn, "INSERT INTO login_attempts (ip_address) VALUES ('$user_ip')");
    
    // 2. 최근 5분간 이 IP의 실패 횟수 조회
    $check_query = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM login_attempts 
                                        WHERE ip_address = '$user_ip' 
                                        AND attempt_time > NOW() - INTERVAL 5 MINUTE");
    $attempt_data = mysqli_fetch_assoc($check_query);
    $fail_count = $attempt_data['cnt'];
    
    // 3. 정확히 5회째에만 보안 로그에 1번 기록 (혹은 5회 이상이면 계속 기록)
    if ($fail_count == 5) {
        mysqli_query($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) 
                             VALUES (NOW(), 'Brute Force Attempt (5회 실패)', '$user_ip', 'Korea', '$user_id', '고위험', '처리대기')");
    }

    echo "<script>
        alert('❌ 아이디 또는 비밀번호가 틀렸습니다. (현재 실패 횟수: " . $fail_count . "회)');
        history.back();
    </script>";
    exit();
}
?>