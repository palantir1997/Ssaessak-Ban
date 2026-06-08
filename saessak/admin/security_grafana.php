<?php
include 'include/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("<script>alert('접근 권한이 없습니다.'); history.back();</script>");
}

$db_host = '172.16.11.210';
$db_user = 'root';
$db_pass = '';
$db_name = 'saessak';
$db_port = 3306;

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
mysqli_set_charset($conn, 'utf8mb4');

// 오늘 위협 건수
$today_threats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM intrusion_logs WHERE DATE(detection_time) = CURDATE()"))['cnt'];

// 처리 대기 건수
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM intrusion_logs WHERE status = '처리대기'"))['cnt'];

// 고위험 건수
$high_risk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM intrusion_logs WHERE risk_level = '고위험'"))['cnt'];

// 처리 완료율
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM intrusion_logs"))['cnt'];
$done  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM intrusion_logs WHERE status = '처리완료'"))['cnt'];
$resolve_rate = $total > 0 ? round(($done / $total) * 100) : 0;

// 최근 위협 피드
$feed_result = mysqli_query($conn, "SELECT * FROM intrusion_logs ORDER BY detection_time DESC LIMIT 10");
$recent_threats = [];
while ($row = mysqli_fetch_assoc($feed_result)) {
    $recent_threats[] = $row;
}

function getRiskStyle($risk) {
    switch ($risk) {
        case '고위험': return ['badge_bg' => '#F7C1C1', 'badge_text' => '#791F1F', 'icon_color' => '#A32D2D', 'icon' => 'alert-octagon'];
        case '중위험': return ['badge_bg' => '#FAC775', 'badge_text' => '#633806', 'icon_color' => '#854F0B', 'icon' => 'alert-triangle'];
        case '저위험': return ['badge_bg' => '#B5D4F4', 'badge_text' => '#0C447C', 'icon_color' => '#185FA5', 'icon' => 'info'];
        default:       return ['badge_bg' => '#D3D1C7', 'badge_text' => '#2C2C2A', 'icon_color' => '#5F5E5A', 'icon' => 'circle'];
    }
}

mysqli_close($conn);
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

<!-- 요약 카드 (실제 DB) -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 bg-red-100 text-red-600 rounded-lg"><i data-lucide="shield-alert" class="w-6 h-6"></i></div>
        <div>
            <p class="text-xs text-gray-500">오늘 위협</p>
            <p class="text-2xl font-bold text-red-600"><?= $today_threats ?>건</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 bg-yellow-100 text-yellow-600 rounded-lg"><i data-lucide="alert-triangle" class="w-6 h-6"></i></div>
        <div>
            <p class="text-xs text-gray-500">처리 대기</p>
            <p class="text-2xl font-bold text-yellow-600"><?= $pending ?>건</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 bg-red-100 text-red-600 rounded-lg"><i data-lucide="flame" class="w-6 h-6"></i></div>
        <div>
            <p class="text-xs text-gray-500">고위험</p>
            <p class="text-2xl font-bold text-red-600"><?= $high_risk ?>건</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 bg-green-100 text-green-600 rounded-lg"><i data-lucide="check-circle" class="w-6 h-6"></i></div>
        <div>
            <p class="text-xs text-gray-500">처리 완료율</p>
            <p class="text-2xl font-bold text-green-600"><?= $resolve_rate ?>%</p>
        </div>
    </div>
</div>

<!-- 그라파나 대시보드 iframe -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800">📊 그라파나 실시간 차트</h2>
        <a href="http://172.16.11.210:3000/d/cfoh3bg41i2v4a/saessak?orgId=1" 
           target="_blank"
           class="text-xs bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
            그라파나 전체 보기 →
        </a>
    </div>
    <iframe 
        src="http://172.16.11.210:3000/d/cfoh3bg41i2v4a/saessak?orgId=1&kiosk=tv&theme=light&refresh=30s" 
        width="100%" 
        height="600px" 
        frameborder="0"
        style="display:block;">
    </iframe>
</div>

<!-- 실시간 위협 피드 (실제 DB) -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">실시간 위협 피드</h2>
    </div>
    <div class="divide-y divide-gray-100">
        <?php if (empty($recent_threats)): ?>
        <div class="px-6 py-8 text-center text-gray-400">탐지된 위협이 없습니다.</div>
        <?php else: foreach ($recent_threats as $threat):
            $style = getRiskStyle($threat['risk_level']);
            $time_diff = (time() - strtotime($threat['detection_time']));
            if ($time_diff < 60) $time_str = $time_diff . '초 전';
            elseif ($time_diff < 3600) $time_str = floor($time_diff/60) . '분 전';
            elseif ($time_diff < 86400) $time_str = floor($time_diff/3600) . '시간 전';
            else $time_str = date('m/d H:i', strtotime($threat['detection_time']));
        ?>
        <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-3">
                <i data-lucide="<?= $style['icon'] ?>" class="w-5 h-5" style="color: <?= $style['icon_color'] ?>"></i>
                <div>
                    <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($threat['attack_type']) ?></p>
                    <p class="text-xs text-gray-500 font-mono"><?= htmlspecialchars($threat['source_ip']) ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold"
                    style="background: <?= $style['badge_bg'] ?>; color: <?= $style['badge_text'] ?>;">
                    <?= htmlspecialchars($threat['risk_level']) ?>
                </span>
                <span class="px-3 py-1 rounded-full text-xs border <?= $threat['status'] === '처리완료' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-yellow-100 text-yellow-700 border-yellow-200' ?>">
                    <?= htmlspecialchars($threat['status']) ?>
                </span>
                <span class="text-xs text-gray-400"><?= $time_str ?></span>
            </div>
        </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<?php include 'include/footer.php'; ?>