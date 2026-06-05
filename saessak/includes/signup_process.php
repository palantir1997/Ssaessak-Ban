sudo tee /var/www/html/includes/signup_process.php << 'EOF'
<?php
session_start();
include_once __DIR__ . '/db.php';

$name = trim($_POST['name'] ?? '');
$login_id = trim($_POST['login_id'] ?? '');
$password = trim($_POST['password'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (empty($name) || empty($login_id) || empty($password) || empty($phone)) {
    echo "<script>alert('❌ 모든 필드를 입력해주세요.'); history.back();</script>";
    exit();
}

if (!$conn) {
    echo "<script>alert('❌ DB 연결 실패: " . mysqli_connect_error() . "'); history.back();</script>";
    exit();
}

// 중복 ID 체크
$check = mysqli_prepare($conn, 'SELECT id FROM patients WHERE login_id = ? LIMIT 1');
mysqli_stmt_bind_param($check, 's', $login_id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);
if (mysqli_stmt_num_rows($check) > 0) {
    echo "<script>alert('❌ 이미 사용 중인 아이디입니다.'); history.back();</script>";
    mysqli_stmt_close($check);
    exit();
}
mysqli_stmt_close($check);

$stmt = mysqli_prepare($conn, 'INSERT INTO patients (name, login_id, password, phone) VALUES (?, ?, ?, ?)');

if (!$stmt) {
    echo "<script>alert('❌ 쿼리 준비 실패: " . mysqli_error($conn) . "'); history.back();</script>";
    exit();
}

mysqli_stmt_bind_param($stmt, 'ssss', $name, $login_id, $password, $phone);

if (mysqli_stmt_execute($stmt)) {
    $new_id = mysqli_insert_id($conn);
    echo "<script>alert('✅ 회원가입 완료! (DB ID: {$new_id})\\n로그인해주세요.'); location.href='../index.php';</script>";
} else {
    $error = mysqli_stmt_error($stmt);
    $errno = mysqli_stmt_errno($stmt);
    echo "<script>alert('❌ INSERT 실패\\n에러번호: {$errno}\\n에러: {$error}'); history.back();</script>";
}

mysqli_stmt_close($stmt);
?>
EOF