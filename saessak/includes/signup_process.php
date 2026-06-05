<?php
session_start();
include_once __DIR__ . '/db.php';

$name = trim($_POST['name'] ?? '');
$login_id = trim($_POST['login_id'] ?? '');
$password = trim($_POST['password'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// 유효성 검사
if (empty($name) || empty($login_id) || empty($password) || empty($phone)) {
    echo "<script>
        alert('❌ 모든 필드를 입력해주세요.');
        history.back();
    </script>";
    exit();
}

// 데이터베이스에 회원가입 시도
if ($conn) {
    $stmt = mysqli_prepare($conn, 'INSERT INTO patients (name, login_id, password, phone) VALUES (?, ?, ?, ?)');
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssss', $name, $login_id, $password, $phone);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                alert('✅ 회원가입이 완료되었습니다!\\n로그인해주세요.');
                location.href='../index.php';
            </script>";
            mysqli_stmt_close($stmt);
            exit();
        } else {
            $error = mysqli_stmt_error($stmt);
            echo "<script>
                alert('❌ 회원가입 실패\\n이미 사용 중인 아이디일 수 있습니다.\\n\\n에러: {$error}');
                history.back();
            </script>";
            mysqli_stmt_close($stmt);
            exit();
        }
    }
}

// DB가 없으면 임시 메시지
echo "<script>
    alert('❌ 데이터베이스 연결 실패\\nMySQL 서버를 확인해주세요.');
    history.back();
</script>";
exit();
?>