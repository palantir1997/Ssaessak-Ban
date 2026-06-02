<?php
// 환자 웹 전용 DB 연결 파일
// MySQL이 꺼져 있어도 메인 페이지가 죽지 않도록, 연결 실패 시 $conn=false만 넘깁니다.
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @mysqli_connect('127.0.0.1', 'root', '', 'saessak');
if (!$conn) {
    $conn = @mysqli_connect('localhost', 'root', '', 'saessak');
}

if ($conn) {
    mysqli_set_charset($conn, 'utf8mb4');
} else {
    $DB_CONNECTION_ERROR = mysqli_connect_error();
}
?>
