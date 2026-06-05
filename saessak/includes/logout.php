<?php
session_start();

// 모든 세션 변수 삭제
session_destroy();

echo "<script>
    alert('✅ 로그아웃되었습니다.');
    location.href='../index.php';
</script>";
exit();
?>