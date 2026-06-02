<?php
$conn = mysqli_connect("localhost", "root", "우분투mysql비번", "saessak");
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}
?>