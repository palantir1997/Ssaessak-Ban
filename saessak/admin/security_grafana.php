<?php
session_start();
// 로그인이 안 되어 있거나 관리자가 아니면 차단
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'admin') {
    die("<script>alert('접근 권한이 없습니다.'); history.back();</script>");
}
?>
<div class="p-8">
    <h1 class="text-2xl font-bold mb-6">보안 관제 대시보드</h1>
    <div class="bg-white p-6 rounded-xl border border-gray-200">
        <iframe src="http://grafana-server-url:3000/d/security-dashboard" class="w-full h-[600px] border-0"></iframe>
    </div>
</div>