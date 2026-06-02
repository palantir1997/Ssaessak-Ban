<?php
session_start();
include 'db.php';

$login_id = $_POST['login_id'];
$password = $_POST['password'];

$sql = "SELECT * FROM patients WHERE login_id='$login_id' AND password='$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['user_id'] = $row['login_id'];
    $_SESSION['name'] = $row['name'];
    header("Location: ../index.php");
} else {
    echo "<script>alert('로그인 실패'); history.back();</script>";
}
?>