<?php
include 'include/db.php';
$no = $_GET['no'];

$query = "DELETE FROM notices WHERE notice_no = '$no'";
if (mysqli_query($conn, $query)) {
    echo "<script>alert('삭제되었습니다.'); location.href='notice.php';</script>";
} else {
    echo "삭제 실패: " . mysqli_error($conn);
}
?>