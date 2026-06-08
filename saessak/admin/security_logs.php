<?php
include 'include/header.php';

// 데이터베이스 연결
try {

    $db_host = '172.16.11.210';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'saessak';
    $db_port = 3306;

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    if (!$conn) {
        die("<div class='p-6 bg-red-100 text-red-700 rounded-xl m-6'><h1>DB 연결 실패</h1></div>");
    }
    mysqli_set_charset($conn, 'utf8mb4');
} catch (Exception $e) {
    die("에러: " . $e->getMessage());
}

// login_attempts 테이블 생성 (user_id 컬럼 포함)
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45),
        user_id VARCHAR(100),
        attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;
");

// user_id 컬럼이 없는 경우에만 추가 (MySQL 5.x 호환)
$col_check = mysqli_query($conn, "SHOW COLUMNS FROM login_attempts LIKE 'user_id'");
if (mysqli_num_rows($col_check) === 0) {
    mysqli_query($conn, "ALTER TABLE login_attempts ADD COLUMN user_id VARCHAR(100) AFTER ip_address");
}

// 5회 이상 실패한 IP+계정을 intrusion_logs에 자동 등록 (10분 기준)
mysqli_query($conn, "
    INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status)
    SELECT NOW(), 'Brute Force Attack (로그인 실패)', ip_address, 'Korea', user_id, '고위험', '처리대기'
    FROM login_attempts
    WHERE attempt_time > NOW() - INTERVAL 10 MINUTE
    GROUP BY ip_address, user_id
    HAVING COUNT(*) >= 5
    AND CONCAT(IFNULL(ip_address,''), IFNULL(user_id,'')) NOT IN (
        SELECT CONCAT(IFNULL(source_ip,''), IFNULL(user_id,''))
        FROM intrusion_logs
        WHERE detection_time > NOW() - INTERVAL 10 MINUTE
    )
");

// 관리자 위치 정보 추적
$admin_ip = ($_SERVER['REMOTE_ADDR'] === '::1' || $_SERVER['REMOTE_ADDR'] === '127.0.0.1') ? '175.114.1.1' : $_SERVER['REMOTE_ADDR'];
$ip_data  = @file_get_contents("http://ip-api.com/json/{$admin_ip}");
$ip_info  = json_decode($ip_data, true);
$location = ($ip_info && $ip_info['status'] == 'success') ? "{$ip_info['country']} - {$ip_info['city']}" : "위치 정보 없음";

// 위험도별 통계
$stats      = ['고위험' => 0, '중위험' => 0, '저위험' => 0];
$stat_query = mysqli_query($conn, "SELECT risk_level, COUNT(*) as cnt FROM intrusion_logs GROUP BY risk_level");
while ($row = mysqli_fetch_assoc($stat_query)) {
    $stats[$row['risk_level']] = $row['cnt'];
}

// 침입 로그 목록 (최근 100건)
$result = mysqli_query($conn, "SELECT * FROM intrusion_logs ORDER BY detection_time DESC LIMIT 100");
$logs   = [];
while ($row = mysqli_fetch_assoc($result)) {
    $logs[] = $row;
}

// 현재 접속 IP의 실패 횟수 및 계정 목록
$my_ip = $_SERVER['REMOTE_ADDR'];
if ($my_ip === '::1') $my_ip = '127.0.0.1';
$stmt   = mysqli_prepare($conn, "SELECT user_id, COUNT(*) as cnt FROM login_attempts WHERE ip_address = ? GROUP BY user_id ORDER BY cnt DESC");
mysqli_stmt_bind_param($stmt, 's', $my_ip);
mysqli_stmt_execute($stmt);
$ip_fail_result = mysqli_stmt_get_result($stmt);
$ip_fail_rows   = [];
$ip_total_fail  = 0;
while ($row = mysqli_fetch_assoc($ip_fail_result)) {
    $ip_fail_rows[] = $row;
    $ip_total_fail += $row['cnt'];
}
?>

<!-- 상단 실시간 배너 -->
<div class="mb-6 flex flex-wrap gap-4 items-center bg-gray-900 p-4 rounded-xl text-white">
    <div class="animate-pulse w-3 h-3 bg-red-500 rounded-full flex-shrink-0"></div>
    <span class="font-bold text-sm">실시간 보안 감시 중</span>
    <span class="text-gray-400 text-sm">접속 IP: <span class="text-white font-mono"><?php echo htmlspecialchars($my_ip); ?></span></span>
    <span class="text-gray-400 text-sm">누적 실패:
        <span class="text-red-400 font-bold text-lg"><?php echo $ip_total_fail; ?>회</span>
    </span>
    <?php if ($ip_total_fail >= 5): ?>
        <span class="text-xs bg-red-600 px-3 py-1 rounded-full font-bold">⚠ 차단 권고 기준 도달</span>
    <?php endif; ?>

    <!-- 이 IP로 시도된 계정 목록 -->
    <?php if (!empty($ip_fail_rows)): ?>
    <div class="ml-auto flex flex-wrap gap-2">
        <?php foreach ($ip_fail_rows as $fr): ?>
        <span class="text-xs bg-gray-700 border border-gray-600 px-2 py-1 rounded-full">
            <span class="text-yellow-300 font-mono"><?php echo htmlspecialchars($fr['user_id'] ?: '알수없음'); ?></span>
            <span class="text-gray-400 ml-1"><?php echo $fr['cnt']; ?>회</span>
        </span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- 정보 카드 2개 -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow text-gray-200">
        <h3 class="font-bold text-blue-400 mb-3 flex items-center gap-2 text-sm uppercase tracking-wider">
            <i data-lucide="shield-check" class="w-4 h-4"></i> 실시간 보안 관제 센터 (SOC)
        </h3>
        <p class="text-sm text-gray-400">현재 접속 계정</p>
        <p class="text-white font-bold text-lg"><?php echo htmlspecialchars($_SESSION['user_id'] ?? '-'); ?></p>
        <p class="text-xs text-gray-500 mt-1">보안 레벨: Lv.5</p>
    </div>
    <div class="bg-gray-800 p-6 rounded-xl border border-blue-500/30 shadow">
        <p class="text-blue-300 text-xs font-bold uppercase tracking-wider mb-2">관리자 위치 추적</p>
        <p class="text-white text-lg font-mono"><?php echo htmlspecialchars($location); ?></p>
        <div class="h-1.5 w-full bg-gray-700 rounded-full mt-3 overflow-hidden">
            <div class="h-full bg-blue-500 animate-pulse w-full"></div>
        </div>
    </div>
</div>

<!-- 위험도 통계 카드 -->
<div class="grid grid-cols-3 gap-4 mb-6">
    <?php
    $level_cfg = [
        '고위험' => ['icon' => 'alert-octagon', 'bg' => 'bg-red-50',    'icon_cls' => 'bg-red-100 text-red-600',    'text' => 'text-red-700'],
        '중위험' => ['icon' => 'alert-triangle','bg' => 'bg-yellow-50', 'icon_cls' => 'bg-yellow-100 text-yellow-600','text' => 'text-yellow-700'],
        '저위험' => ['icon' => 'info',           'bg' => 'bg-blue-50',   'icon_cls' => 'bg-blue-100 text-blue-600',   'text' => 'text-blue-700'],
    ];
    foreach ($level_cfg as $level => $cfg): ?>
    <div class="<?php echo $cfg['bg']; ?> p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 <?php echo $cfg['icon_cls']; ?> rounded-lg flex-shrink-0">
            <i data-lucide="<?php echo $cfg['icon']; ?>" class="w-5 h-5"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium"><?php echo $level; ?></p>
            <p class="text-2xl font-bold <?php echo $cfg['text']; ?>"><?php echo $stats[$level] ?? 0; ?></p>
            <p class="text-xs text-gray-400">건</p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- 침입 로그 테이블 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="list" class="w-4 h-4 text-gray-500"></i> 침입 탐지 로그
        </h2>
        <span class="text-xs text-gray-400">최근 100건</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3">탐지 시간</th>
                    <th class="px-5 py-3">공격 유형</th>
                    <th class="px-5 py-3">소스 IP</th>
                    <th class="px-5 py-3">계정 (user_id)</th>
                    <th class="px-5 py-3">국가</th>
                    <th class="px-5 py-3">위험도</th>
                    <th class="px-5 py-3">상태</th>
                    <th class="px-5 py-3">분석</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="8" class="px-5 py-10 text-center text-gray-400">
                        <i data-lucide="shield-check" class="w-8 h-8 mx-auto mb-2 text-green-400"></i>
                        <p>탐지된 침입 기록이 없습니다.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($logs as $log):
                    $risk = $log['risk_level'] ?? '';
                    $badge_cls = match($risk) {
                        '고위험' => 'bg-red-100 text-red-700',
                        '중위험' => 'bg-yellow-100 text-yellow-700',
                        '저위험' => 'bg-blue-100 text-blue-700',
                        default   => 'bg-gray-100 text-gray-600',
                    };
                    $status_cls = ($log['status'] === '처리대기') ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700';
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 font-mono text-xs text-gray-500 whitespace-nowrap"><?php echo htmlspecialchars($log['detection_time']); ?></td>
                    <td class="px-5 py-3 font-semibold text-gray-800"><?php echo htmlspecialchars($log['attack_type']); ?></td>
                    <td class="px-5 py-3 font-mono text-xs text-gray-500"><?php echo htmlspecialchars($log['source_ip']); ?></td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 text-blue-600 font-medium">
                            <i data-lucide="user" class="w-3 h-3"></i>
                            <?php echo htmlspecialchars($log['user_id'] ?: '알수없음'); ?>
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">🌍 <?php echo htmlspecialchars($log['country']); ?></td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-bold <?php echo $badge_cls; ?>"><?php echo htmlspecialchars($risk); ?></span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $status_cls; ?>"><?php echo htmlspecialchars($log['status'] ?? '-'); ?></span>
                    </td>
                    <td class="px-5 py-3">
                        <button onclick="openDetailModal(
                            '<?php echo htmlspecialchars(addslashes($log['attack_type'])); ?>',
                            '<?php echo htmlspecialchars(addslashes($log['source_ip'])); ?>',
                            '<?php echo htmlspecialchars(addslashes($log['country'])); ?>',
                            '<?php echo htmlspecialchars(addslashes($log['user_id'] ?? '알수없음')); ?>'
                        )" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">
                            분석
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 상세 분석 모달 -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="bg-gray-900 px-6 py-4 flex items-center justify-between">
            <h2 class="text-white font-bold flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-yellow-400"></i>
                공격 상세 분석
            </h2>
            <button onclick="document.getElementById('detailModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">공격 유형</p>
                <p id="modalType" class="font-semibold text-gray-800"></p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">소스 IP</p>
                    <p id="modalIp" class="font-mono text-sm text-gray-700"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">발생 국가</p>
                    <p id="modalCountry" class="text-sm text-gray-700"></p>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">시도 계정 (user_id)</p>
                <p id="modalUser" class="font-mono text-sm text-blue-600 font-semibold"></p>
            </div>
            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg text-yellow-800 text-sm">
                ⚠ 차단 권고: 해당 IP를 방화벽 블랙리스트에 추가하십시오.
            </div>
        </div>
        <div class="px-6 pb-6 flex justify-end">
            <button onclick="document.getElementById('detailModal').classList.add('hidden')"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg text-sm transition-colors">
                닫기
            </button>
        </div>
    </div>
</div>

<script>
function openDetailModal(type, ip, country, userId) {
    document.getElementById('modalType').innerText    = type;
    document.getElementById('modalIp').innerText      = ip;
    document.getElementById('modalCountry').innerText = '🌍 ' + country;
    document.getElementById('modalUser').innerText    = userId;
    document.getElementById('detailModal').classList.remove('hidden');
}
</script>

<?php include 'include/footer.php'; ?>