<?php
session_start();
include_once __DIR__ . '/db.php';

// ✅ reCAPTCHA 검증
$recaptcha_secret   = '6LeTth8tAAAAAGJyFTaIUVZ7WtxnqO2onJPvx5n-';
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
if (empty($recaptcha_response)) {
    echo "<script>alert('❌ 로봇 확인을 완료해주세요.'); history.back();</script>";
    exit();
}
// 이걸로 교체
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'secret'   => $recaptcha_secret,
    'response' => $recaptcha_response
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$verify = curl_exec($ch);
curl_close($ch);
$captcha_result = json_decode($verify);
if (!$captcha_result->success) {
    echo "<script>alert('❌ 로봇 확인에 실패했습니다. 다시 시도해주세요.'); history.back();</script>";
    exit();
}

$login_id = trim($_POST['login_id'] ?? '');
$password = trim($_POST['password'] ?? '');
$login_success = false;
$patient_id = 0;
$patient_name = '';

function detect_sqli($value) {
    $patterns = ["'", '"', '--', '#', '/*', '*/', 'OR ', 'AND ', '1=1', 'DROP ', 'SELECT ', 'INSERT ', 'UPDATE ', 'DELETE ', 'UNION ', 'EXEC ', 'xp_'];
    foreach ($patterns as $p) {
        if (stripos($value, $p) !== false) return true;
    }
    return false;
}

$user_ip = $_SERVER['REMOTE_ADDR'];
if ($user_ip === '::1') $user_ip = '127.0.0.1';

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS login_attempts (
        id           INT AUTO_INCREMENT PRIMARY KEY,
        ip_address   VARCHAR(45),
        user_id      VARCHAR(100),
        attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;
");

// 10회 잠금 체크
$lock_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as cnt FROM login_attempts WHERE ip_address = ? AND user_id = ? AND attempt_time > NOW() - INTERVAL 10 MINUTE");
mysqli_stmt_bind_param($lock_stmt, 'ss', $user_ip, $login_id);
mysqli_stmt_execute($lock_stmt);
$lock_result = mysqli_stmt_get_result($lock_stmt);
$lock_count = mysqli_fetch_assoc($lock_result)['cnt'];
mysqli_stmt_close($lock_stmt);

if ($lock_count >= 10) {
    $attack = 'Brute Force Attack (계정 잠금)';
    $risk   = '고위험';
    $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
    mysqli_stmt_bind_param($ins, 'ssss', $attack, $user_ip, $login_id, $risk);
    mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);
    echo "<script>alert('⛔ 로그인 시도 횟수 초과로 계정이 잠금되었습니다.\\n10분 후 다시 시도해주세요.'); history.back();</script>";
    exit();
}

// SQL 인젝션 감지
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
    $_SESSION['last_activity']    = time();
    echo "<script>alert('✅ {$patient_name}님 로그인되었습니다.'); location.href='../index.php';</script>";
    exit();

} else {
    $fail_stmt = mysqli_prepare($conn, "INSERT INTO login_attempts (ip_address, user_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($fail_stmt, 'ss', $user_ip, $login_id);
    mysqli_stmt_execute($fail_stmt);
    mysqli_stmt_close($fail_stmt);

    if (!$user_exists) {
        $attack = '존재하지 않는 계정 접근 시도';
        $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, '중위험', '처리대기')");
        mysqli_stmt_bind_param($ins, 'sss', $attack, $user_ip, $login_id);
        mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);
    }

    $cnt_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as cnt FROM login_attempts WHERE ip_address = ? AND user_id = ? AND attempt_time > NOW() - INTERVAL 10 MINUTE");
    mysqli_stmt_bind_param($cnt_stmt, 'ss', $user_ip, $login_id);
    mysqli_stmt_execute($cnt_stmt);
    $cnt_result = mysqli_stmt_get_result($cnt_stmt);
    $fail_count = mysqli_fetch_assoc($cnt_result)['cnt'];
    mysqli_stmt_close($cnt_stmt);

    if ($fail_count >= 10) {
        $attack = 'Brute Force Attack (계정 잠금)';
        $risk   = '고위험';
        $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
        mysqli_stmt_bind_param($ins, 'ssss', $attack, $user_ip, $login_id, $risk);
        mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);
        echo "<script>alert('⛔ 로그인 시도 횟수 초과로 계정이 잠금되었습니다.\\n10분 후 다시 시도해주세요.'); history.back();</script>";
    } elseif ($fail_count >= 5) {
        $attack = 'Brute Force Attack (로그인 실패)';
        $risk   = '고위험';
        $already = mysqli_query($conn, "SELECT id FROM intrusion_logs WHERE source_ip='$user_ip' AND user_id='$login_id' AND attack_type='$attack' AND detection_time > NOW() - INTERVAL 10 MINUTE LIMIT 1");
        if (mysqli_num_rows($already) === 0) {
            $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
            mysqli_stmt_bind_param($ins, 'ssss', $attack, $user_ip, $login_id, $risk);
            mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);
        }
        echo "<script>alert('❌ 로그인 실패\\nID 또는 비밀번호를 확인해주세요.'); history.back();</script>";
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
        echo "<script>alert('❌ 로그인 실패\\nID 또는 비밀번호를 확인해주세요.'); history.back();</script>";
    } else {
        $attack = '로그인 실패';
        $risk   = '저위험';
        $ins = mysqli_prepare($conn, "INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status) VALUES (NOW(), ?, ?, 'Korea', ?, ?, '처리대기')");
        mysqli_stmt_bind_param($ins, 'ssss', $attack, $user_ip, $login_id, $risk);
        mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);
        echo "<script>alert('❌ 로그인 실패\\nID 또는 비밀번호를 확인해주세요.'); history.back();</script>";
    }
    exit();
}
?>