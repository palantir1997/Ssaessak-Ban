<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
$conn = mysqli_connect("localhost", "root", "", "saessak");
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}
?>