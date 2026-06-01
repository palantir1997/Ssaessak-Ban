<?php
session_start();
// 로그인이 안 되어 있거나 관리자가 아니면 차단
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'admin') {
    die("<script>alert('접근 권한이 없습니다.'); history.back();</script>");
}
?>
<div class="p-8">
    <h1 class="text-2xl font-bold mb-6">보안 점검 도구 실행</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 text-center">
            <i data-lucide="shield-alert" class="w-12 h-12 mx-auto text-red-500 mb-4"></i>
            <h3 class="font-bold mb-2">취약점 스캔</h3>
            <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">스캔 시작</button>
        </div>
        </div>
</div>