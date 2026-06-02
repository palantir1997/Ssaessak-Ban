<?php
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect('localhost', 'root', '', 'saessak');
if (!$conn) {
    echo "<script>alert('DB 연결 실패: XAMPP에서 MySQL을 Start 했는지 확인해 주세요.'); history.back();</script>";
    exit();
}
mysqli_set_charset($conn, 'utf8mb4');
?>
