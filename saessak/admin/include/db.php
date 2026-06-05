<?php
// admin/include/db.php
$db_host = 'localhost'; 
$db_user = 'saessak';
$db_pass = '1234'; // MySQL 비밀번호가 있다면 여기에 입력하세요
$db_name = 'saessak';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>