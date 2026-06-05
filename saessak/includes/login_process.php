<?php
session_start();

include_once __DIR__ . '/db.php';

$user_id  = trim($_POST['user_id']  ?? '');
$password = trim($_POST['password'] ?? '');

$login_success = false;

// SQL 인젝션 패턴 감지 함수
function detect_sqli($value) {
    $patterns = ["'", '"', '--', '#', '/*', '*/', 'OR ', 'AND ', '1=1', '1 =1', '1= 1', 'DROP ', 'SELECT ', 'INSERT ', 'UPDATE ', 'DELETE ', 'UNION ', 'EXEC ', 'xp_'];
    foreach ($patterns as $p) {
        if (stripos($value, $p) !== false) return true;
    }
    return false;
}

// SQL 인젝션 즉시 탐지 및 기록
function record_intrusion($conn, $user_ip, $user_id, $attack_type, $risk_level) {
    $col_check = mysqli_query($conn, "SHOW COLUMNS FROM login_attempts LIKE 'user_id'");
    if (mysqli_num_rows($col_check) === 0) {
        mysqli_query($conn, "ALTER TABLE login_attempts ADD COLUMN user_id VARCHAR(100) AFTER ip_address");
    }
    $stmt = mysqli_prepare($conn, "INSERT INTO login_attempts (ip_address, user_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'ss', $user_ip, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt2 = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
    mysqli_stmt_bind_param($stmt2, 'ssss', $attack_type, $user_ip, $user_id, $risk_level);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);
}

$user_ip = $_SERVER['REMOTE_ADDR'];
if ($user_ip === '::1') $user_ip = '127.0.0.1';

// login_attempts 테이블 생성
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS login_attempts (
        id           INT AUTO_INCREMENT PRIMARY KEY,
        ip_address   VARCHAR(45),
        user_id      VARCHAR(100),
        attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;
");

// SQL 인젝션 패턴 감지 → 즉시 고위험 등록 후 종료
if (detect_sqli($user_id) || detect_sqli($password)) {
    record_intrusion($conn, $user_ip, $user_id, 'SQL Injection 시도', '고위험');
    echo "<script>alert('❌ 비정상적인 접근이 감지되었습니다.'); history.back();</script>";
    exit();
}

// 1. staff_accounts 테이블에서 로그인 확인
$user_exists = false;
if ($conn) {
    // 아이디 존재 여부 먼저 확인
    $check_stmt = mysqli_prepare($conn, 'SELECT user_id FROM staff_accounts WHERE user_id = ? LIMIT 1');
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, 's', $user_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        if (mysqli_fetch_assoc($check_result)) $user_exists = true;
        mysqli_stmt_close($check_stmt);
    }

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

// 2. 하드코딩 테스트 계정
if (!$login_success) {
    if ($user_id === 'admin' && $password === '1234') {
        $login_success = true;
        $user_exists   = true;
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
    // 실패 기록
    $fail_stmt = mysqli_prepare($conn, "INSERT INTO login_attempts (ip_address, user_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($fail_stmt, 'ss', $user_ip, $user_id);
    mysqli_stmt_execute($fail_stmt);
    mysqli_stmt_close($fail_stmt);

    // 존재하지 않는 아이디 → 중위험 즉시 등록
    if (!$user_exists) {
        $attack_type = '존재하지 않는 계정 접근 시도';
        $stmt3 = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, '중위험', '처리대기')");
        mysqli_stmt_bind_param($stmt3, 'sss', $attack_type, $user_ip, $user_id);
        mysqli_stmt_execute($stmt3);
        mysqli_stmt_close($stmt3);
    }

    // 실패 횟수 기반 위험도 자동 등록 (10분 기준)
    $cnt_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as cnt FROM login_attempts WHERE ip_address = ? AND user_id = ? AND attempt_time > NOW() - INTERVAL 10 MINUTE");
    mysqli_stmt_bind_param($cnt_stmt, 'ss', $user_ip, $user_id);
    mysqli_stmt_execute($cnt_stmt);
    $cnt_result = mysqli_stmt_get_result($cnt_stmt);
    $fail_count = mysqli_fetch_assoc($cnt_result)['cnt'];
    mysqli_stmt_close($cnt_stmt);

    if ($fail_count >= 5) {
        $attack = 'Brute Force Attack (로그인 실패)';
        $risk   = '고위험';
        $already = mysqli_query($conn, "SELECT id FROM intrusion_logs WHERE source_ip='$user_ip' AND user_id='$user_id' AND attack_type='$attack' AND detection_time > NOW() - INTERVAL 10 MINUTE LIMIT 1");
        if (mysqli_num_rows($already) === 0) {
            $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
            mysqli_stmt_bind_param($ins, 'ssss', $attack, $user_ip, $user_id, $risk);
            mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);
        }
    } elseif ($fail_count >= 3 && $user_exists) {
        $attack = '비정상 로그인 시도 (반복 실패)';
        $risk   = '중위험';
        $already = mysqli_query($conn, "SELECT id FROM intrusion_logs WHERE source_ip='$user_ip' AND user_id='$user_id' AND attack_type='$attack' AND detection_time > NOW() - INTERVAL 10 MINUTE LIMIT 1");
        if (mysqli_num_rows($already) === 0) {
            $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
            mysqli_stmt_bind_param($ins, 'ssss', $attack, $user_ip, $user_id, $risk);
            mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);
        }
    } elseif ($fail_count >= 1 && $user_exists) {
        $attack = '로그인 실패';
        $risk   = '저위험';
        $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
        mysqli_stmt_bind_param($ins, 'ssss', $attack, $user_ip, $user_id, $risk);
        mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);
    }

    echo "<script>
        alert('❌ 로그인 실패!');
        history.back();
    </script>";
    exit();
}
?>