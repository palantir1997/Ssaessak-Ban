<?php
include 'include/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("<script>alert('접근 권한이 없습니다.'); history.back();</script>");
}
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">보안 점검 도구</h1>
    <p class="text-sm text-gray-500 mt-1">시스템 취약점 점검 및 보안 진단 실행</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col items-center text-center gap-4">
        <div class="p-4 bg-red-100 text-red-600 rounded-xl">
            <i data-lucide="shield-alert" class="w-10 h-10"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">취약점 스캔</h3>
            <p class="text-sm text-gray-500 mt-1">시스템 전체 취약점 자동 탐지</p>
        </div>
        <div class="w-full mt-auto pt-4 border-t border-gray-100">
            <div class="text-xs text-gray-400 mb-3">마지막 실행: 2026-06-01 09:00</div>
            <button onclick="runTool('취약점 스캔')" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition-colors">
                스캔 시작
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col items-center text-center gap-4">
        <div class="p-4 bg-blue-100 text-blue-600 rounded-xl">
            <i data-lucide="network" class="w-10 h-10"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">네트워크 점검</h3>
            <p class="text-sm text-gray-500 mt-1">포트 스캔 및 비정상 트래픽 탐지</p>
        </div>
        <div class="w-full mt-auto pt-4 border-t border-gray-100">
            <div class="text-xs text-gray-400 mb-3">마지막 실행: 2026-05-31 18:30</div>
            <button onclick="runTool('네트워크 점검')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors">
                점검 시작
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col items-center text-center gap-4">
        <div class="p-4 bg-yellow-100 text-yellow-600 rounded-xl">
            <i data-lucide="file-search" class="w-10 h-10"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">로그 분석</h3>
            <p class="text-sm text-gray-500 mt-1">시스템 로그 이상 징후 자동 분석</p>
        </div>
        <div class="w-full mt-auto pt-4 border-t border-gray-100">
            <div class="text-xs text-gray-400 mb-3">마지막 실행: 2026-06-01 06:00</div>
            <button onclick="runTool('로그 분석')" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 rounded-lg transition-colors">
                분석 시작
            </button>
        </div>
    </div>

</div>

<!-- 실행 결과 영역 -->
<div id="result-box" class="hidden mt-6 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800">실행 결과</h2>
        <span id="result-status" class="text-xs px-3 py-1 rounded-full font-bold bg-blue-100 text-blue-700">실행중...</span>
    </div>
    <div class="bg-gray-900 text-green-400 font-mono text-sm rounded-lg p-5 leading-7">
        <p id="result-text"></p>
    </div>
</div>

<script>
function runTool(toolName) {
    const box = document.getElementById('result-box');
    const status = document.getElementById('result-status');
    const text = document.getElementById('result-text');

    box.classList.remove('hidden');
    status.textContent = '실행중...';
    status.className = 'text-xs px-3 py-1 rounded-full font-bold bg-yellow-100 text-yellow-700';
    text.textContent = `[${new Date().toLocaleString()}] ${toolName} 시작...\n스캔 중...`;

    setTimeout(() => {
        status.textContent = '완료';
        status.className = 'text-xs px-3 py-1 rounded-full font-bold bg-green-100 text-green-700';
        text.innerHTML = 
            `[${new Date().toLocaleString()}] ${toolName} 완료<br>` +
            `> 점검 항목: 128개<br>` +
            `> 이상 없음: 125개<br>` +
            `> 주의 필요: 3개<br>` +
            `> 결과를 침입 탐지 로그에서 확인하세요.`;
    }, 2000);
}
</script>

<?php include 'include/footer.php'; ?>