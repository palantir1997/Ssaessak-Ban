<?php
session_start();
// 관리자 세션만 종료
unset($_SESSION['user_id'], $_SESSION['admin_id']);
header('Location: ../login.php');
exit();
?>
