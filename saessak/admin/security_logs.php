<?php
session_start();
// 로그인이 안 되어 있거나 관리자가 아니면 차단
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'admin') {
    die("<script>alert('접근 권한이 없습니다.'); history.back();</script>");
}
?>
<div class="p-8">
    <h1 class="text-2xl font-bold mb-6">침입 탐지 로그 조회</h1>
    <table class="w-full bg-white border border-gray-200 rounded-lg">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-6 py-3 text-left">시간</th>
                <th class="px-6 py-3 text-left">유형</th>
                <th class="px-6 py-3 text-left">위험도</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b">
                <td class="px-6 py-4">2026-06-01 17:00</td>
                <td class="px-6 py-4">무차별 대입 공격(Brute Force)</td>
                <td class="px-6 py-4"><span class="text-red-600 font-bold">고위험</span></td>
            </tr>
        </tbody>
    </table>
</div>