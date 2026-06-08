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
if ($conn) mysqli_set_charset($conn, 'utf8mb4');

// ── 각 도구 실행 처리 ──
$tool = $_GET['run'] ?? '';
$result = null;

// 1. 취약한 계정 검사
if ($tool === 'weak_accounts' && $conn) {
    $weak_passwords = ['1234', '0000', 'admin', 'password', '123456', '111111', 'qwerty', '12345', 'abc123', '1111'];
    $placeholders = implode(',', array_fill(0, count($weak_passwords), '?'));
    $types = str_repeat('s', count($weak_passwords));
    $stmt = mysqli_prepare($conn, "SELECT user_id, name, password FROM staff_accounts WHERE password IN ($placeholders)");
    mysqli_stmt_bind_param($stmt, $types, ...$weak_passwords);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
    mysqli_stmt_close($stmt);

    // 비밀번호 4자리 이하도 추가 탐지
    $stmt2 = mysqli_prepare($conn, "SELECT user_id, name, password FROM staff_accounts WHERE LENGTH(password) <= 4 AND password NOT IN ($placeholders)");
    mysqli_stmt_bind_param($stmt2, $types, ...$weak_passwords);
    mysqli_stmt_execute($stmt2);
    $res2 = mysqli_stmt_get_result($stmt2);
    while ($r = mysqli_fetch_assoc($res2)) $rows[] = $r;
    mysqli_stmt_close($stmt2);

    $result = ['type' => 'weak_accounts', 'data' => $rows];
}

// 2. 로그 분석
if ($tool === 'log_analysis' && $conn) {
    $today_total  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM login_attempts WHERE DATE(attempt_time) = CURDATE()"))['cnt'];
    $hour_total   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM login_attempts WHERE attempt_time > NOW() - INTERVAL 1 HOUR"))['cnt'];
    $top_ips      = mysqli_query($conn, "SELECT ip_address, COUNT(*) as cnt FROM login_attempts GROUP BY ip_address ORDER BY cnt DESC LIMIT 5");
    $top_ids      = mysqli_query($conn, "SELECT user_id, COUNT(*) as cnt FROM login_attempts GROUP BY user_id ORDER BY cnt DESC LIMIT 5");
    $top_ip_rows  = [];
    $top_id_rows  = [];
    while ($r = mysqli_fetch_assoc($top_ips)) $top_ip_rows[] = $r;
    while ($r = mysqli_fetch_assoc($top_ids)) $top_id_rows[] = $r;
    $result = ['type' => 'log_analysis', 'today' => $today_total, 'hour' => $hour_total, 'top_ips' => $top_ip_rows, 'top_ids' => $top_id_rows];
}

// 3. DB 테이블 무결성 검사
if ($tool === 'db_integrity' && $conn) {
    $required = [
        'staff_accounts'  => ['user_id', 'password', 'name', 'status'],
        'login_attempts'  => ['id', 'ip_address', 'user_id', 'attempt_time'],
        'intrusion_logs'  => ['id', 'detection_time', 'attack_type', 'source_ip', 'user_id', 'risk_level', 'status'],
        'patients'        => ['id', 'login_id', 'password', 'name'],
        'receptions'      => ['reception_no', 'patient_name', 'status'],
        'medical_equipments' => ['equipment_no', 'equipment_name', 'status'],
    ];
    $check_results = [];
    foreach ($required as $table => $columns) {
        $table_check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
        if (mysqli_num_rows($table_check) === 0) {
            $check_results[] = ['table' => $table, 'status' => 'missing', 'detail' => '테이블 없음'];
            continue;
        }
        $col_res = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");
        $existing = [];
        while ($c = mysqli_fetch_assoc($col_res)) $existing[] = $c['Field'];
        $missing_cols = array_diff($columns, $existing);
        if (empty($missing_cols)) {
            $check_results[] = ['table' => $table, 'status' => 'ok', 'detail' => '정상'];
        } else {
            $check_results[] = ['table' => $table, 'status' => 'warning', 'detail' => '누락 컬럼: ' . implode(', ', $missing_cols)];
        }
    }
    $result = ['type' => 'db_integrity', 'data' => $check_results];
}

// 4. 포트 점검
if ($tool === 'port_scan') {
    $host = '172.16.11.210';
    $ports = [
        80   => 'HTTP',
        443  => 'HTTPS',
        22   => 'SSH',
        3306 => 'MySQL',
        8080 => 'HTTP-Alt',
        21   => 'FTP',
    ];
    $port_results = [];
    foreach ($ports as $port => $name) {
        $conn_check = @fsockopen($host, $port, $errno, $errstr, 0.5); // 타임아웃을 0.5초로 축소
        if ($conn_check) {
            fclose($conn_check);
            $port_results[] = ['port' => $port, 'name' => $name, 'status' => 'open'];
        } else {
            $port_results[] = ['port' => $port, 'name' => $name, 'status' => 'closed'];
        }
    }
    $result = ['type' => 'port_scan', 'data' => $port_results];
}

// 5. SQL Injection 통계
if ($tool === 'sqli_stats' && $conn) {
    $total     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM intrusion_logs WHERE attack_type LIKE '%SQL%'"))['cnt'];
    $today     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM intrusion_logs WHERE attack_type LIKE '%SQL%' AND DATE(detection_time) = CURDATE()"))['cnt'];
    $top_sqli  = mysqli_query($conn, "SELECT source_ip, user_id, COUNT(*) as cnt FROM intrusion_logs WHERE attack_type LIKE '%SQL%' GROUP BY source_ip, user_id ORDER BY cnt DESC LIMIT 5");
    $sqli_rows = [];
    while ($r = mysqli_fetch_assoc($top_sqli)) $sqli_rows[] = $r;
    $recent    = mysqli_query($conn, "SELECT detection_time, source_ip, user_id FROM intrusion_logs WHERE attack_type LIKE '%SQL%' ORDER BY detection_time DESC LIMIT 5");
    $recent_rows = [];
    while ($r = mysqli_fetch_assoc($recent)) $recent_rows[] = $r;
    $result = ['type' => 'sqli_stats', 'total' => $total, 'today' => $today, 'top' => $sqli_rows, 'recent' => $recent_rows];
}
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">보안 점검 도구</h1>
    <p class="text-sm text-gray-500 mt-1">시스템 취약점 점검 및 보안 진단 실행</p>
</div>

<!-- 도구 카드 5개 -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <!-- 취약한 계정 검사 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col items-center text-center gap-4">
        <div class="p-4 bg-red-100 text-red-600 rounded-xl">
            <i data-lucide="user-x" class="w-10 h-10"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">취약한 계정 검사</h3>
            <p class="text-sm text-gray-500 mt-1">단순 비밀번호 사용 계정 탐지</p>
        </div>
        <div class="w-full mt-auto pt-4 border-t border-gray-100">
            <a href="?run=weak_accounts" class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition-colors text-sm">검사 시작</a>
        </div>
    </div>

    <!-- 로그 분석 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col items-center text-center gap-4">
        <div class="p-4 bg-blue-100 text-blue-600 rounded-xl">
            <i data-lucide="file-search" class="w-10 h-10"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">로그 분석</h3>
            <p class="text-sm text-gray-500 mt-1">로그인 실패 패턴 및 이상 징후 분석</p>
        </div>
        <div class="w-full mt-auto pt-4 border-t border-gray-100">
            <a href="?run=log_analysis" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors text-sm">분석 시작</a>
        </div>
    </div>

    <!-- DB 무결성 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col items-center text-center gap-4">
        <div class="p-4 bg-purple-100 text-purple-600 rounded-xl">
            <i data-lucide="database" class="w-10 h-10"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">DB 무결성 검사</h3>
            <p class="text-sm text-gray-500 mt-1">필수 테이블 및 컬럼 존재 여부 확인</p>
        </div>
        <div class="w-full mt-auto pt-4 border-t border-gray-100">
            <a href="?run=db_integrity" class="block w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 rounded-lg transition-colors text-sm">검사 시작</a>
        </div>
    </div>

    <!-- 포트 점검 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col items-center text-center gap-4">
        <div class="p-4 bg-yellow-100 text-yellow-600 rounded-xl">
            <i data-lucide="network" class="w-10 h-10"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">포트 점검</h3>
            <p class="text-sm text-gray-500 mt-1">서버 주요 포트 개방 여부 확인</p>
        </div>
        <div class="w-full mt-auto pt-4 border-t border-gray-100">
            <a href="?run=port_scan" class="block w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 rounded-lg transition-colors text-sm">점검 시작</a>
        </div>
    </div>

    <!-- SQL Injection 통계 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col items-center text-center gap-4">
        <div class="p-4 bg-orange-100 text-orange-600 rounded-xl">
            <i data-lucide="alert-triangle" class="w-10 h-10"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">SQL Injection 통계</h3>
            <p class="text-sm text-gray-500 mt-1">SQLi 시도 횟수 및 출처 IP 분석</p>
        </div>
        <div class="w-full mt-auto pt-4 border-t border-gray-100">
            <a href="?run=sqli_stats" class="block w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 rounded-lg transition-colors text-sm">통계 보기</a>
        </div>
    </div>

</div>

<!-- 결과 영역 -->
<?php if ($result): ?>
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-800">
            <?php
            $titles = [
                'weak_accounts' => '취약한 계정 검사 결과',
                'log_analysis'  => '로그 분석 결과',
                'db_integrity'  => 'DB 무결성 검사 결과',
                'port_scan'     => '포트 점검 결과',
                'sqli_stats'    => 'SQL Injection 통계',
            ];
            echo $titles[$result['type']] ?? '결과';
            ?>
        </h2>
        <span class="text-xs text-gray-400"><?php echo date('Y-m-d H:i:s'); ?></span>
    </div>

    <div class="p-6">

        <?php if ($result['type'] === 'weak_accounts'): ?>
            <?php if (empty($result['data'])): ?>
                <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    <p class="text-green-700 font-semibold">취약한 계정이 발견되지 않았습니다.</p>
                </div>
            <?php else: ?>
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm font-semibold">
                    ⚠ 총 <?php echo count($result['data']); ?>개의 취약한 계정이 발견되었습니다. 즉시 비밀번호를 변경하세요!
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">계정 ID</th>
                            <th class="px-4 py-3 text-left">이름</th>
                            <th class="px-4 py-3 text-left">현재 비밀번호</th>
                            <th class="px-4 py-3 text-left">위험도</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($result['data'] as $row): ?>
                        <tr>
                            <td class="px-4 py-3 font-mono font-bold text-red-600"><?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="px-4 py-3 font-mono bg-red-50 text-red-700"><?php echo htmlspecialchars($row['password']); ?></td>
                            <td class="px-4 py-3"><span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">고위험</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($result['type'] === 'log_analysis'): ?>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-blue-500 font-bold mb-1">오늘 로그인 실패</p>
                    <p class="text-3xl font-black text-blue-700"><?php echo $result['today']; ?><span class="text-sm font-normal">회</span></p>
                </div>
                <div class="bg-orange-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-orange-500 font-bold mb-1">최근 1시간 실패</p>
                    <p class="text-3xl font-black text-orange-700"><?php echo $result['hour']; ?><span class="text-sm font-normal">회</span></p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-bold text-gray-600 mb-3">실패 횟수 상위 IP</p>
                    <?php if (empty($result['top_ips'])): ?>
                        <p class="text-gray-400 text-sm">데이터 없음</p>
                    <?php else: ?>
                        <?php foreach ($result['top_ips'] as $ip): ?>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 text-sm">
                            <span class="font-mono text-gray-700"><?php echo htmlspecialchars($ip['ip_address']); ?></span>
                            <span class="font-bold text-red-600"><?php echo $ip['cnt']; ?>회</span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-600 mb-3">실패 횟수 상위 계정</p>
                    <?php if (empty($result['top_ids'])): ?>
                        <p class="text-gray-400 text-sm">데이터 없음</p>
                    <?php else: ?>
                        <?php foreach ($result['top_ids'] as $uid): ?>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 text-sm">
                            <span class="font-mono text-blue-600"><?php echo htmlspecialchars($uid['user_id'] ?: '알수없음'); ?></span>
                            <span class="font-bold text-red-600"><?php echo $uid['cnt']; ?>회</span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($result['type'] === 'db_integrity'): ?>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">테이블명</th>
                        <th class="px-4 py-3 text-left">상태</th>
                        <th class="px-4 py-3 text-left">상세</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($result['data'] as $row): ?>
                    <tr>
                        <td class="px-4 py-3 font-mono font-bold text-gray-700"><?php echo htmlspecialchars($row['table']); ?></td>
                        <td class="px-4 py-3">
                            <?php if ($row['status'] === 'ok'): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">정상</span>
                            <?php elseif ($row['status'] === 'warning'): ?>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">경고</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">오류</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-500"><?php echo htmlspecialchars($row['detail']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($result['type'] === 'port_scan'): ?>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <?php foreach ($result['data'] as $p): ?>
                <div class="p-4 rounded-xl border <?php echo $p['status'] === 'open' ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'; ?> flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full <?php echo $p['status'] === 'open' ? 'bg-green-500' : 'bg-gray-300'; ?>"></div>
                    <div>
                        <p class="font-bold text-sm <?php echo $p['status'] === 'open' ? 'text-green-700' : 'text-gray-500'; ?>"><?php echo $p['name']; ?></p>
                        <p class="text-xs text-gray-400">포트 <?php echo $p['port']; ?> — <?php echo $p['status'] === 'open' ? '열림' : '닫힘'; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($result['type'] === 'sqli_stats'): ?>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-orange-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-orange-500 font-bold mb-1">전체 SQLi 시도</p>
                    <p class="text-3xl font-black text-orange-700"><?php echo $result['total']; ?><span class="text-sm font-normal">건</span></p>
                </div>
                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-red-500 font-bold mb-1">오늘 SQLi 시도</p>
                    <p class="text-3xl font-black text-red-700"><?php echo $result['today']; ?><span class="text-sm font-normal">건</span></p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-bold text-gray-600 mb-3">상위 공격 IP / 계정</p>
                    <?php if (empty($result['top'])): ?>
                        <p class="text-gray-400 text-sm">탐지된 SQLi 없음</p>
                    <?php else: ?>
                        <?php foreach ($result['top'] as $r): ?>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 text-sm">
                            <span class="font-mono text-gray-700"><?php echo htmlspecialchars($r['source_ip']); ?> / <span class="text-blue-600"><?php echo htmlspecialchars($r['user_id'] ?: '알수없음'); ?></span></span>
                            <span class="font-bold text-red-600"><?php echo $r['cnt']; ?>건</span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-600 mb-3">최근 SQLi 시도</p>
                    <?php if (empty($result['recent'])): ?>
                        <p class="text-gray-400 text-sm">탐지된 SQLi 없음</p>
                    <?php else: ?>
                        <?php foreach ($result['recent'] as $r): ?>
                        <div class="py-2 border-b border-gray-100 text-sm">
                            <p class="font-mono text-xs text-gray-400"><?php echo $r['detection_time']; ?></p>
                            <p class="text-gray-700"><?php echo htmlspecialchars($r['source_ip']); ?> → <span class="text-blue-600"><?php echo htmlspecialchars($r['user_id'] ?: '알수없음'); ?></span></p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>

<?php include 'include/footer.php'; ?>