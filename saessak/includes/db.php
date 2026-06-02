<?php
session_start();

// DB 연결 아예 안 함! 직접 아이디/비번 확인
$admin_id = "admin";
$admin_pw = "1234"; // 원하시는 비밀번호로 바꾸세요

$user_id = $_POST['user_id'];
$password = $_POST['password'];

if ($user_id === $admin_id && $password === $admin_pw) {
    $_SESSION['user_id'] = $user_id;
    header("Location: ../dashboard.php");
    exit();
} else {
    echo "<script>alert('아이디 또는 비밀번호가 틀렸습니다.'); history.back();</script>";
    exit();
$conn = mysqli_connect("localhost", "root", "", "saessak");
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
=======
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect('localhost', 'root', '', 'saessak');
if (!$conn) {
    echo "<script>alert('DB 연결 실패: XAMPP에서 MySQL을 Start 했는지 확인해 주세요.'); location.href='../index.php';</script>";
    exit();
>>>>>>> 1a517b851c9f13b6f26131227703a76c7e445974
}
mysqli_set_charset($conn, 'utf8mb4');
?>
