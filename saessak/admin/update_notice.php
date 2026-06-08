<?php
// admin/update_notice.php
include 'include/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'include/db.php';

$no       = mysqli_real_escape_string($conn, $_POST['no']);
$title    = mysqli_real_escape_string($conn, $_POST['title']);
$content  = mysqli_real_escape_string($conn, $_POST['content']);
$category = mysqli_real_escape_string($conn, $_POST['category']);

$query = "UPDATE notices SET title = '$title', content = '$content', category = '$category' WHERE notice_no = '$no'";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('수정되었습니다.'); location.href='notice.php';</script>";
} else {
    echo "수정 실패: " . mysqli_error($conn);
}
?>