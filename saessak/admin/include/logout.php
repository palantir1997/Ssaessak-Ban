<?php
// 1. 기존 세션을 시작합니다.
session_start();

// 2. 세션 변수를 모두 비웁니다.
$_SESSION = array();

// 3. 세션 쿠키를 삭제합니다 (브라우저가 세션 아이디를 잊게 함)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. 세션을 완전히 파괴합니다.
session_destroy();

// 5. 로그인 페이지로 이동시킵니다.
header("Location: ../login.php");
exit();
?>