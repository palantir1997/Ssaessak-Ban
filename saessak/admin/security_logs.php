<?php
include 'include/header.php';

// 1. 데이터베이스 연결
try {
    $db_host = '172.16.11.222'; 
    $db_user = 'root';
    $db_pass = ''; 
    $db_name = 'saessak';
    $db_port = 3306; 

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    if (!$conn) {
        die("<div class='p-6 bg-red-100 text-red-700 rounded-xl m-6'><h1>🚨 DB 연결 실패</h1></div>");
    }
    mysqli_set_charset($conn, 'utf8mb4');
} catch (Exception $e) {
    die("에러: " . $e->getMessage());
}

// 2. 테이블 체크 및 보안 자동 탐지 로직
if (isset($conn)) {
    // login_attempts 테이블이 없으면 생성
    mysqli_query($conn, "
        CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45),
            attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    // 1분 이내에 3번 이상 실패한 IP를 자동으로 침입 탐지 로그로 이전 (자동 탐지 엔진)
    mysqli_query($conn, "
        INSERT INTO intrusion_logs (detection_time, attack_type, source_ip, country, user_id, risk_level, status)
        SELECT NOW(), 'Brute Force Attack (로그인 실패)', ip_address, 'Korea', 'Unknown', '고위험', '처리대기'
        FROM login_attempts
        WHERE attempt_time > NOW() - INTERVAL 1 MINUTE
        GROUP BY ip_address
        HAVING COUNT(*) >= 3
        AND ip_address NOT IN (SELECT source_ip FROM intrusion_logs WHERE detection_time > NOW() - INTERVAL 1 MINUTE)
    ");
}
// 3. 관리자 위치 정보 추적 (IP API)
$admin_ip = ($_SERVER['REMOTE_ADDR'] === '::1' || $_SERVER['REMOTE_ADDR'] === '127.0.0.1') ? '175.114.1.1' : $_SERVER['REMOTE_ADDR'];
$ip_data = @file_get_contents("http://ip-api.com/json/{$admin_ip}");
$ip_info = json_decode($ip_data, true);
$location = ($ip_info && $ip_info['status'] == 'success') ? "{$ip_info['country']} - {$ip_info['city']}" : "위치 정보 없음";

// 데이터 조회
$stats = ['고위험' => 0, '중위험' => 0, '저위험' => 0];
$stat_query = mysqli_query($conn, "SELECT risk_level, COUNT(*) as cnt FROM intrusion_logs GROUP BY risk_level");
while($row = mysqli_fetch_assoc($stat_query)) { $stats[$row['risk_level']] = $row['cnt']; }

// security_logs.php 상단 데이터 조회 부분
$result = mysqli_query($conn, "SELECT * FROM intrusion_logs ORDER BY detection_time DESC LIMIT 100");
$logs = [];
while($row = mysqli_fetch_assoc($result)){ 
    $logs[] = $row; 
}
?>


<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-lg text-gray-200">
        <h3 class="font-bold text-blue-400 mb-2 flex items-center gap-2">
            <i data-lucide="shield-check"></i> 실시간 보안 관제 센터(SOC)
        </h3>
        <p class="text-sm">현재 접속 계정: <span class="text-white font-bold"><?php echo $_SESSION['user_id']; ?></span></p>
        <p class="text-xs text-gray-400">시스템 보안 레벨: Lv.5</p>
    </div>
    <div class="p-6 bg-gray-800 rounded-xl border border-blue-500/30 shadow-lg">
        <p class="text-blue-300 text-sm font-bold mb-1">📍 관리자 실시간 위치 기반 추적</p>
        <p class="text-white text-lg font-mono"><?php echo $location; ?></p>
        <div class="h-2 w-full bg-gray-700 rounded-full mt-2 overflow-hidden">
            <div class="h-full bg-blue-500 animate-pulse" style="width: 100%"></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">
    <?php foreach(['고위험'=>'alert-octagon', '중위험'=>'alert-triangle', '저위험'=>'info'] as $level => $icon): ?>
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-3 <?php echo ($level=='고위험')?'bg-red-100 text-red-600':(($level=='중위험')?'bg-yellow-100 text-yellow-600':'bg-blue-100 text-blue-600'); ?> rounded-lg">
            <i data-lucide="<?php echo $icon; ?>" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500"><?php echo $level; ?></p>
            <p class="text-xl font-bold"><?php echo $stats[$level] ?? 0; ?>건</p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-gray-50 text-gray-500 text-sm">
            <tr>
                <th class="px-6 py-4">탐지 시간</th>
                <th class="px-6 py-4">공격 유형</th>
                <th class="px-6 py-4">국가</th>
                <th class="px-6 py-4">소스 IP</th>
                <th class="px-6 py-4">계정</th>
                <th class="px-6 py-4">위험도</th>
                <th class="px-6 py-4">분석</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
            <?php foreach ($logs as $log): ?>
            <tr>
                <td class="px-6 py-4"><?php echo $log['detection_time']; ?></td>
                <td class="px-6 py-4 font-bold"><?php echo $log['attack_type']; ?></td>
                <td class="px-6 py-4">🌍 <?php echo $log['country']; ?></td>
                <td class="px-6 py-4 text-gray-500"><?php echo $log['source_ip']; ?></td>
                <td class="px-6 py-4 text-blue-600"><?php echo $log['user_id']; ?></td>
                <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100"><?php echo $log['risk_level']; ?></span></td>
                <td class="px-6 py-4">
                    <button onclick="openDetailModal('<?php echo $log['attack_type']; ?>', '<?php echo $log['source_ip']; ?>', '<?php echo $log['country']; ?>')" 
                            class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">분석</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-1/3 shadow-2xl">
        <h2 class="text-xl font-bold mb-4 text-gray-800" id="modalTitle">공격 상세 분석</h2>
        <div class="space-y-3 text-sm">
            <p id="modalIp" class="text-gray-600"></p>
            <p id="modalCountry" class="text-gray-600"></p>
            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700">
                ⚠️ 차단 권고: 해당 IP를 방화벽 블랙리스트에 추가하십시오.
            </div>
        </div>
        <button onclick="document.getElementById('detailModal').classList.add('hidden')" 
                class="mt-6 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">닫기</button>
    </div>
</div>

<script>
function openDetailModal(type, ip, country) {
    document.getElementById('modalTitle').innerText = "분석: " + type;
    document.getElementById('modalIp').innerText = "출처 IP: " + ip;
    document.getElementById('modalCountry').innerText = "발생 국가: " + country;
    document.getElementById('detailModal').classList.remove('hidden');
}
</script>

<?php include 'include/footer.php'; ?>