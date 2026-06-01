<?php
session_start();
include 'db.php'; // 위에서 만든 SQLite 연결 파일을 가져옴

$user_id = $_POST['user_id'];
$password = $_POST['password'];

// SQLite 방식으로 조회
$stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['user_id'];
    header("Location: ../dashboard.php");
} else {
    echo "<script>alert('로그인 실패!'); history.back();</script>";
}
?>