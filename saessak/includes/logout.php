<?php
session_start();
// 환자 로그인 세션만 제거. 관리자 세션은 건드리지 않음.
unset($_SESSION['patient_id'], $_SESSION['patient_login_id'], $_SESSION['patient_name']);
header('Location: ../index.php');
exit();
?>
