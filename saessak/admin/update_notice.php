<?php
// admin/update_notice.php
include '../includes/db.php';

$no = $_POST['no'];
$title = mysqli_real_escape_string($conn, $_POST['title']);
$content = mysqli_real_escape_string($conn, $_POST['content']); // 추가

// [수정 포인트] SET 뒤에 content = '$content'를 추가했습니다
$query = "UPDATE notices SET title = '$title', content = '$content' WHERE notice_no = '$no'";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('수정되었습니다.'); location.href='notice.php';</script>";
} else {
    echo "수정 실패: " . mysqli_error($conn);
}
?>