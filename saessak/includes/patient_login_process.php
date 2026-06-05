<?php
session_start();
include_once __DIR__ . '/db.php';

$login_id = trim($_POST['login_id'] ?? '');
$password = trim($_POST['password'] ?? '');
$login_success = false;
$patient_id = 0;
$patient_name = '';

// SQL 인젝션 패턴 감지 함수
function detect_sqli($value) {
    $patterns = ["'", '"', '--', '#', '/*', '*/', 'OR ', 'AND ', '1=1', 'DROP ', 'SELECT ', 'INSERT ', 'UPDATE ', 'DELETE ', 'UNION ', 'EXEC ', 'xp_'];
    foreach ($patterns as $p) {
        if (stripos($value, $p) !== false) return true;
    }
    return false;
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

// SQL 인젝션 즉시 탐지 → 고위험 등록
if (detect_sqli($login_id) || detect_sqli($password)) {
    $fail_stmt = mysqli_prepare($conn, "INSERT INTO login_attempts (ip_address, user_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($fail_stmt, 'ss', $user_ip, $login_id);
    mysqli_stmt_execute($fail_stmt);
    mysqli_stmt_close($fail_stmt);

    $attack = 'SQL Injection 시도';
    $risk   = '고위험';
    $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
    mysqli_stmt_bind_param($ins, 'ssss', $attack, $user_ip, $login_id, $risk);
    mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);

    echo "<script>alert('❌ 비정상적인 접근이 감지되었습니다.'); history.back();</script>";
    exit();
}

// 아이디 존재 여부 확인
$user_exists = false;
if ($conn) {
    $check_stmt = mysqli_prepare($conn, 'SELECT id FROM patients WHERE login_id = ? LIMIT 1');
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, 's', $login_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        if (mysqli_fetch_assoc($check_result)) $user_exists = true;
        mysqli_stmt_close($check_stmt);
    }

    $stmt = mysqli_prepare($conn, 'SELECT id, name FROM patients WHERE login_id = ? AND password = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $login_id, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $login_success = true;
            $patient_id   = $row['id'];
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
    // 실패 기록
    $fail_stmt = mysqli_prepare($conn, "INSERT INTO login_attempts (ip_address, user_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($fail_stmt, 'ss', $user_ip, $login_id);
    mysqli_stmt_execute($fail_stmt);
    mysqli_stmt_close($fail_stmt);

    // 존재하지 않는 아이디 → 중위험
    if (!$user_exists) {
        $attack = '존재하지 않는 계정 접근 시도';
        $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, '중위험', '처리대기')");
        mysqli_stmt_bind_param($ins, 'sss', $attack, $user_ip, $login_id);
        mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);
    }

    // 실패 횟수 기반 위험도
    $cnt_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as cnt FROM login_attempts WHERE ip_address = ? AND user_id = ? AND attempt_time > NOW() - INTERVAL 10 MINUTE");
    mysqli_stmt_bind_param($cnt_stmt, 'ss', $user_ip, $login_id);
    mysqli_stmt_execute($cnt_stmt);
    $cnt_result = mysqli_stmt_get_result($cnt_stmt);
    $fail_count = mysqli_fetch_assoc($cnt_result)['cnt'];
    mysqli_stmt_close($cnt_stmt);

    if ($fail_count >= 5) {
        $attack = 'Brute Force Attack (로그인 실패)';
        $risk   = '고위험';
        $already = mysqli_query($conn, "SELECT id FROM intrusion_logs WHERE source_ip='$user_ip' AND user_id='$login_id' AND attack_type='$attack' AND detection_time > NOW() - INTERVAL 10 MINUTE LIMIT 1");
        if (mysqli_num_rows($already) === 0) {
            $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
            mysqli_stmt_bind_param($ins, 'ssss', $attack, $user_ip, $login_id, $risk);
            mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);
        }
    } elseif ($fail_count >= 3 && $user_exists) {
        $attack = '비정상 로그인 시도 (반복 실패)';
        $risk   = '중위험';
        $already = mysqli_query($conn, "SELECT id FROM intrusion_logs WHERE source_ip='$user_ip' AND user_id='$login_id' AND attack_type='$attack' AND detection_time > NOW() - INTERVAL 10 MINUTE LIMIT 1");
        if (mysqli_num_rows($already) === 0) {
            $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
            mysqli_stmt_bind_param($ins, 'ssss', $attack, $user_ip, $login_id, $risk);
            mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);
        }
    } elseif ($fail_count >= 1 && $user_exists) {
        $attack = '로그인 실패';
        $risk   = '저위험';
        $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
        mysqli_stmt_bind_param($ins, 'ssss', $attack, $user_ip, $login_id, $risk);
        mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);
    }

    echo "<script>alert('❌ 로그인 실패\\nID 또는 비밀번호를 확인해주세요.'); history.back();</script>";
    exit();
}
?>