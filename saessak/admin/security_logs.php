<?php
include 'include/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("<script>alert('접근 권한이 없습니다.'); history.back();</script>");
}

$logs = [
    ["time" => "2026-06-01 17:00", "type" => "무차별 대입 공격 (Brute Force)", "ip" => "192.168.1.105", "risk" => "고위험", "status" => "처리대기"],
    ["time" => "2026-06-01 15:42", "type" => "SQL 인젝션 시도",               "ip" => "10.0.0.88",    "risk" => "고위험", "status" => "처리대기"],
    ["time" => "2026-06-01 13:20", "type" => "비정상 포트 접근",               "ip" => "172.16.0.23",  "risk" => "중위험", "status" => "처리완료"],
    ["time" => "2026-06-01 11:05", "type" => "권한 없는 파일 접근 시도",        "ip" => "192.168.1.77", "risk" => "중위험", "status" => "처리완료"],
    ["time" => "2026-06-01 09:30", "type" => "외부 IP 반복 접속",              "ip" => "203.0.113.42", "risk" => "저위험", "status" => "처리완료"],
    ["time" => "2026-05-31 22:15", "type" => "세션 하이재킹 시도",              "ip" => "192.168.2.11", "risk" => "고위험", "status" => "처리완료"],
];

function getRiskColor($risk) {
    switch ($risk) {
        case '고위험': return 'bg-red-100 text-red-700 border-red-200';
        case '중위험': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case '저위험': return 'bg-blue-100 text-blue-700 border-blue-200';
        default: return 'bg-gray-100 text-gray-600';
    }
}

function getStatusColor($status) {
    return $status === '처리대기'
        ? 'bg-red-50 text-red-600 border-red-200'
        : 'bg-green-50 text-green-600 border-green-200';
}
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">침입 탐지 로그</h1>
        <p class="text-sm text-gray-500 mt-1">시스템 침입 시도 및 보안 이벤트 기록</p>
    </div>
    <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 shadow-sm">
        <i data-lucide="download" class="w-4 h-4"></i> 내보내기
    </button>
</div>

<!-- 요약 카드 -->
<div class="grid grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 bg-red-100 text-red-600 rounded-lg"><i data-lucide="alert-octagon" class="w-6 h-6"></i></div>
        <div>
            <p class="text-xs text-gray-500">고위험</p>
            <p class="text-xl font-bold text-red-600">3건</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 bg-yellow-100 text-yellow-600 rounded-lg"><i data-lucide="alert-triangle" class="w-6 h-6"></i></div>
        <div>
            <p class="text-xs text-gray-500">중위험</p>
            <p class="text-xl font-bold text-yellow-600">2건</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 bg-blue-100 text-blue-600 rounded-lg"><i data-lucide="info" class="w-6 h-6"></i></div>
        <div>
            <p class="text-xs text-gray-500">저위험</p>
            <p class="text-xl font-bold text-blue-600">1건</p>
        </div>
    </div>
</div>

<!-- 로그 테이블 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">전체 로그</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-sm border-b border-gray-200">
                    <th class="px-6 py-4 font-medium">탐지 시간</th>
                    <th class="px-6 py-4 font-medium">공격 유형</th>
                    <th class="px-6 py-4 font-medium">출처 IP</th>
                    <th class="px-6 py-4 font-medium">위험도</th>
                    <th class="px-6 py-4 font-medium">처리 상태</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-700"><?php echo $log['time']; ?></td>
                    <td class="px-6 py-4 text-gray-800"><?php echo $log['type']; ?></td>
                    <td class="px-6 py-4 font-mono text-gray-500"><?php echo $log['ip']; ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo getRiskColor($log['risk']); ?>">
                            <?php echo $log['risk']; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo getStatusColor($log['status']); ?>">
                            <?php echo $log['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>