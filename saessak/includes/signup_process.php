<?php
session_start();
include 'db.php';

$name = $_POST['name'];
$login_id = $_POST['login_id'];
$password = $_POST['password'];
$phone = $_POST['phone'];

$sql = "INSERT INTO patients (name, login_id, password, phone) VALUES ('$name', '$login_id', '$password', '$phone')";
if (mysqli_query($conn, $sql)) {
    echo "<script>alert('회원가입 완료!'); window.location.href='../index.php';</script>";
} else {
    echo "<script>alert('이미 사용중인 ID입니다.'); history.back();</script>";
}
?>