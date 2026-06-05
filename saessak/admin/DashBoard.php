<?php 
    // 1. 헤더 포함
    include 'include/header.php'; 
    
    // 2. 데이터베이스 연결
    try {
        $db_host = '172.16.11.222'; 
        $db_user = 'root';
        $db_pass = ''; 
        $db_name = 'saessak';
        $db_port = 3306; 

        $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

        // ✅ 연결 성공 여부 확인
        if (!$conn) {
            die("<div class='p-6 bg-red-100 text-red-700 rounded-xl m-6'>
                    <h1 class='text-xl font-bold mb-2'>🚨 우분투 DB 서버 연결 실패</h1>
                    <p>PHP 웹서버가 우분투 가상머신({$db_host}) 접속에 실패했습니다.</p>
                    <p class='mt-4 font-mono text-sm bg-white p-3 rounded border border-red-200'>에러 내용: " . mysqli_connect_error() . "</p>
                 </div>");
        }

        mysqli_set_charset($conn, 'utf8mb4');
    } catch (Exception $e) {
        die("<div class='p-6 bg-red-100 text-red-700 rounded-xl m-6'>
                <h1 class='text-xl font-bold mb-2'>🚨 우분투 DB 서버 연결 실패</h1>
                <p>에러 내용: " . $e->getMessage() . "</p>
             </div>");
    }

    // 3. 오늘 날짜 설정
    $today = date('Y-m-d');

    // 4. 실시간 대기 현황 데이터 조회
    $query = "SELECT 
                reception_no as id,
                patient_name as name,
                target_time as time,
                dept_name as dept,
                status,
                symptoms_memo as note
              FROM receptions 
              WHERE target_date = '$today'
              ORDER BY target_time ASC";
    
    $result = $conn->query($query);
    $waitlist = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $waitlist[] = $row;
        }
    }

    // 5. 통계 데이터 조회
    // 5-1) 금일 전체 예약 수
    $total_query = "SELECT COUNT(*) as count FROM receptions WHERE target_date = '$today'";
    $total_result = $conn->query($total_query);
    $total_count = $total_result->fetch_assoc()['count'] ?? 0;

    // 5-2) 현재 대기 환자 수
    $waiting_query = "SELECT COUNT(*) as count FROM receptions WHERE target_date = '$today' AND status = '대기중'";
    $waiting_result = $conn->query($waiting_query);
    $waiting_count = $waiting_result->fetch_assoc()['count'] ?? 0;

    // 5-3) 진료 완료 환자 수
    $completed_query = "SELECT COUNT(*) as count FROM receptions WHERE target_date = '$today' AND status = '완료'";
    $completed_result = $conn->query($completed_query);
    $completed_count = $completed_result->fetch_assoc()['count'] ?? 0;

    // 5-4) 긴급 알림 (처리 대기 중인 침입 로그)
    $alert_query = "SELECT COUNT(*) as count FROM intrusion_logs WHERE status = '처리대기'";
    $alert_result = $conn->query($alert_query);
    $alert_count = $alert_result->fetch_assoc()['count'] ?? 0;

    // 6. 상태별 색상 함수
    function getStatusColor($status) {
        switch ($status) {
            case '진료중': return 'bg-blue-100 text-blue-700 border-blue-200';
            case '대기중': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
            case '완료': return 'bg-gray-100 text-gray-700 border-gray-200';
            case '확정': return 'bg-green-100 text-green-700 border-green-200';
            case '취소': return 'bg-red-100 text-red-700 border-red-200';
            default: return 'bg-gray-100 text-gray-700';
        }
    }
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- 금일 전체 예약 -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-4 bg-blue-100 text-blue-600 rounded-lg"><i data-lucide="calendar" class="w-8 h-8"></i></div>
        <div>
            <p class="text-sm text-gray-500 font-medium">금일 전체 예약</p>
            <p class="text-2xl font-bold"><?php echo $total_count; ?><span class="text-sm font-normal text-gray-400 ml-1">명</span></p>
        </div>
    </div>

    <!-- 현재 대기 환자 -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-4 bg-yellow-100 text-yellow-600 rounded-lg"><i data-lucide="users" class="w-8 h-8"></i></div>
        <div>
            <p class="text-sm text-gray-500 font-medium">현재 대기 환자</p>
            <p class="text-2xl font-bold text-yellow-600"><?php echo $waiting_count; ?><span class="text-sm font-normal text-gray-400 ml-1">명</span></p>
        </div>
    </div>

    <!-- 진료 완료 -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-4 bg-green-100 text-green-600 rounded-lg"><i data-lucide="check-circle" class="w-8 h-8"></i></div>
        <div>
            <p class="text-sm text-gray-500 font-medium">진료 완료</p>
            <p class="text-2xl font-bold"><?php echo $completed_count; ?><span class="text-sm font-normal text-gray-400 ml-1">명</span></p>
        </div>
    </div>

    <!-- 긴급 알림 -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
        <div class="p-4 bg-red-100 text-red-600 rounded-lg"><i data-lucide="alert-circle" class="w-8 h-8"></i></div>
        <div>
            <p class="text-sm text-gray-500 font-medium">긴급 알림</p>
            <p class="text-2xl font-bold text-red-600"><?php echo $alert_count; ?><span class="text-sm font-normal text-gray-400 ml-1">건</span></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    <!-- 실시간 대기 현황 테이블 -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800">실시간 대기 현황</h2>
            <button class="text-sm text-blue-600 font-medium hover:underline">전체보기</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-sm border-b border-gray-200">
                        <th class="px-6 py-4 font-medium">예약시간</th>
                        <th class="px-6 py-4 font-medium">환자명 (접수번호)</th>
                        <th class="px-6 py-4 font-medium">진료과</th>
                        <th class="px-6 py-4 font-medium">상태</th>
                        <th class="px-6 py-4 font-medium">비고</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    <?php if (!empty($waitlist)): ?>
                        <?php foreach ($waitlist as $row): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($row['time']); ?></td>
                            <td class="px-6 py-4">
                                <span class="font-bold"><?php echo htmlspecialchars($row['name']); ?></span> 
                                <span class="text-gray-400">(<?php echo htmlspecialchars($row['id']); ?>)</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($row['dept']); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo getStatusColor($row['status']); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 truncate max-w-xs"><?php echo htmlspecialchars($row['note']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                오늘 예약된 환자가 없습니다.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 빠른 실행 패널 -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h2 class="text-lg font-bold text-gray-800 mb-4">빠른 실행</h2>
            <div class="grid grid-cols-2 gap-3">
                <button class="flex flex-col items-center justify-center p-4 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors gap-2">
                    <i data-lucide="user-plus" class="w-6 h-6"></i>
                    <span class="font-medium text-sm">현장 접수</span>
                </button>
                <button class="flex flex-col items-center justify-center p-4 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors gap-2">
                    <i data-lucide="megaphone" class="w-6 h-6"></i>
                    <span class="font-medium text-sm">긴급 공지</span>
                </button>
            </div>
        </div>
    </div>
</div>

<?php 
    // DB 연결 종료
    $conn->close();
    include 'include/footer.php'; 
?>