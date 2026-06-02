<?php
$conn = mysqli_connect("localhost", "root", "", "saessak");
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}
?>