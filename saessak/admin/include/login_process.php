<?php
session_start();
include 'db.php';

$user_id = $_POST['user_id'];
$password = $_POST['password'];

$sql = "SELECT * FROM admin_users WHERE user_id='$user_id' AND password='$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['role'] = $row['role'];
    header("Location: ../DashBoard.php");
    exit();
} else {
    echo "<script>alert('로그인 실패'); history.back();</script>";
}
?>