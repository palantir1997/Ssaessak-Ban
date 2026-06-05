<?php
<<<<<<< HEAD
// DB 연결 설정
$host = 'localhost';
$user = 'web_user';
$pass = 'password123';
$dbname = 'saessak';

$conn = mysqli_connect($host, $user, $pass, $dbname);

// 연결 오류 확인
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
=======
// 데이터베이스 연결 설정
$db_host = '172.16.11.222';  // 우분투 VM 주소 (또는 localhost)
$db_user = 'root';
$db_pass = '';
$db_name = 'saessak';
$db_port = 3306;

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$conn) {
    die("<div style='padding:20px; background:#fee; color:#c00; border:1px solid #faa; border-radius:5px;'>
        <h3>❌ 데이터베이스 연결 실패</h3>
        <p><strong>에러:</strong> " . mysqli_connect_error() . "</p>
        <p>MySQL 서버가 실행 중인지 확인해주세요.</p>
        <p><code>{$db_host}:{$db_port}</code> 접속 확인 필요</p>
    </div>");
>>>>>>> 9446ed218bcc73f47d1dbba11e1c24aa7abd6690
}

mysqli_set_charset($conn, 'utf8mb4');
?>
