<?php
// DB 연결 설정
$host = 'localhost';
$user = 'web_user';
$pass = 'password123';
$dbname = 'saessak';

$conn = mysqli_connect($host, $user, $pass, $dbname);

// 연결 오류 확인
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>
