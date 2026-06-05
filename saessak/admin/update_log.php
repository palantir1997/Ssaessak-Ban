<?php
include '../include/db_connect.php'; // DB 연결 파일

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_id'])) {
    $log_id = intval($_POST['log_id']);
    mysqli_query($conn, "UPDATE intrusion_logs SET status = '처리완료' WHERE id = $log_id");
    
    echo "<script>alert('조치되었습니다.'); location.href='security_logs.php';</script>";
}
?>