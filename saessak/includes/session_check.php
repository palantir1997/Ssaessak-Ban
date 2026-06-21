<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout = 1800; // 테스트용 (운영시: 1800)

// 로그인한 사람만 체크 (비로그인 방문자는 통과)
if (isset($_SESSION['login_type'])) {
    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $timeout) {
        session_destroy();
        echo "<script>alert('⏰ 세션이 만료되었습니다.\\n보안을 위해 자동 로그아웃 되었습니다.\\n다시 로그인해주세요.'); location.href='../index.php';</script>";
        exit();
    }
    $_SESSION['last_activity'] = time();
}
?>