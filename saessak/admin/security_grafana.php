<?php
include 'include/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("<script>alert('접근 권한이 없습니다.'); history.back();</script>");
}

// ============================================================
// 나중에 실제 서버 로그 연동할 때 여기만 교체하면 됩니다!
// 
// 실제 연동 예시:
// $auth_log = shell_exec("sudo grep 'Failed password' /var/log/auth.log | tail -100");
// $apache_log = shell_exec("sudo tail -100 /var/log/apache2/access.log");
// 위 데이터를 파싱해서 아래 $stats, $hourly, $attack_types, $recent_threats 에 넣으면 됩니다.
// ============================================================

// 요약 통계 (목업)
$stats = [
    "today_threats" => 14,
    "pending"       => 3,
    "blocked_ips"   => 27,
    "resolve_rate"  => 98,
];

// 시간대별 공격 횟수 (목업) - 0시~23시
$hourly = [0,0,1,0,0,0,2,3,1,2,4,3,5,2,3,6,4,3,2,1,2,1,0,1];

// 공격 유형 분포 (목업)
$attack_types = [
    ["label" => "브루트포스",  "count" => 6, "color" => "#E24B4A"],
    ["label" => "SQL 인젝션", "count" => 4, "color" => "#EF9F27"],
    ["label" => "포트스캔",   "count" => 3, "color" => "#378ADD"],
    ["label" => "기타",       "count" => 1, "color" => "#1D9E75"],
];

// 실시간 위협 피드 (목업)
$recent_threats = [
    ["time" => "2분 전",   "type" => "브루트포스 공격 감지",  "ip" => "192.168.1.105", "risk" => "고위험", "icon" => "alert-octagon"],
    ["time" => "15분 전",  "type" => "SQL 인젝션 시도",       "ip" => "10.0.0.88",    "risk" => "고위험", "icon" => "database"],
    ["time" => "1시간 전", "type" => "비정상 포트 접근",      "ip" => "172.16.0.23",  "risk" => "중위험", "icon" => "network"],
    ["time" => "2시간 전", "type" => "권한 없는 파일 접근",   "ip" => "192.168.1.77", "risk" => "중위험", "icon" => "file-alert"],
    ["time" => "3시간 전", "type" => "외부 IP 반복 접속",     "ip" => "203.0.113.42", "risk" => "저위험", "icon" => "wifi-off"],
];

function getRiskStyle($risk) {
    switch ($risk) {
        case '고위험': return ['bg' => '#FCEBEB', 'badge_bg' => '#F7C1C1', 'badge_text' => '#791F1F', 'icon' => '#A32D2D'];
        case '중위험': return ['bg' => '#FAEEDA', 'badge_bg' => '#FAC775', 'badge_text' => '#633806', 'icon' => '#854F0B'];
        case '저위험': return ['bg' => '#E6F1FB', 'badge_bg' => '#B5D4F4', 'badge_text' => '#0C447C', 'icon' => '#185FA5'];
        default:       return ['bg' => '#F1EFE8', 'badge_bg' => '#D3D1C7', 'badge_text' => '#2C2C2A', 'icon' => '#5F5E5A'];
    }
}

$max_hourly = max($hourly);
$max_attack = max(array_column($attack_types, 'count'));
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">보안 관제 대시보드</h1>
        <p class="text-sm text-gray-500 mt-1">실시간 위협 현황 모니터링</p>
    </div>
    <button onclick="location.reload()" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 shadow-sm">
        <i data-lucide="refresh-cw" class="w-4 h-4"></i> 새로고침
    </button>
</div>

<!-- 요약 카드 -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 bg-red-100 text-red-600 rounded-lg"><i data-lucide="shield-alert" class="w-6 h-6"></i></div>
        <div>
            <p class="text-xs text-gray-500">오늘 위협</p>
            <p class="text-2xl font-bold text-red-600"><?= $stats['today_threats'] ?>건</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 bg-yellow-100 text-yellow-600 rounded-lg"><i data-lucide="alert-triangle" class="w-6 h-6"></i></div>
        <div>
            <p class="text-xs text-gray-500">처리 대기</p>
            <p class="text-2xl font-bold text-yellow-600"><?= $stats['pending'] ?>건</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 bg-blue-100 text-blue-600 rounded-lg"><i data-lucide="lock" class="w-6 h-6"></i></div>
        <div>
            <p class="text-xs text-gray-500">차단된 IP</p>
            <p class="text-2xl font-bold text-blue-600"><?= $stats['blocked_ips'] ?>개</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 bg-green-100 text-green-600 rounded-lg"><i data-lucide="check-circle" class="w-6 h-6"></i></div>
        <div>
            <p class="text-xs text-gray-500">처리 완료율</p>
            <p class="text-2xl font-bold text-green-600"><?= $stats['resolve_rate'] ?>%</p>
        </div>
    </div>
</div>

<!-- 차트 영역 -->
<div class="grid grid-cols-2 gap-4 mb-6">

    <!-- 시간대별 공격 횟수 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">시간대별 공격 횟수 (오늘)</h2>
        <div class="flex items-end gap-1 h-32">
            <?php foreach ($hourly as $i => $val): ?>
            <?php $height = $max_hourly > 0 ? round(($val / $max_hourly) * 100) : 0; ?>
            <div class="flex-1 flex flex-col items-center gap-1">
                <div style="height: <?= $height ?>%; background: <?= $val == $max_hourly ? '#E24B4A' : '#B5D4F4' ?>; width: 100%; border-radius: 2px 2px 0 0; min-height: 2px;"></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-1">
            <span>00시</span><span>06시</span><span>12시</span><span>18시</span><span>23시</span>
        </div>
    </div>

    <!-- 공격 유형 분포 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">공격 유형 분포</h2>
        <div class="space-y-3">
            <?php foreach ($attack_types as $type): ?>
            <?php $pct = $max_attack > 0 ? round(($type['count'] / $max_attack) * 100) : 0; ?>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-700"><?= $type['label'] ?></span>
                    <span class="font-bold" style="color: <?= $type['color'] ?>"><?= $type['count'] ?>건</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div style="width: <?= $pct ?>%; background: <?= $type['color'] ?>; height: 8px; border-radius: 9999px;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<!-- 실시간 위협 피드 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">실시간 위협 피드</h2>
    </div>
    <div class="divide-y divide-gray-100">
        <?php foreach ($recent_threats as $threat):
            $style = getRiskStyle($threat['risk']);
        ?>
        <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors" style="background: <?= $style['bg'] ?>10;">
            <div class="flex items-center gap-3">
                <i data-lucide="<?= $threat['icon'] ?>" class="w-5 h-5" style="color: <?= $style['icon'] ?>"></i>
                <div>
                    <p class="text-sm font-medium text-gray-800"><?= $threat['type'] ?></p>
                    <p class="text-xs text-gray-500 font-mono"><?= $threat['ip'] ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold border"
                    style="background: <?= $style['badge_bg'] ?>; color: <?= $style['badge_text'] ?>;">
                    <?= $threat['risk'] ?>
                </span>
                <span class="text-xs text-gray-400"><?= $threat['time'] ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'include/footer.php'; ?>