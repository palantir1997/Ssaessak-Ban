<?php
// admin/include/db.php
$db_host = '172.16.11.222'; 
$db_user = 'root';
$db_pass = ''; // MySQL 비밀번호가 있다면 여기에 입력하세요
$db_name = 'saessak';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>