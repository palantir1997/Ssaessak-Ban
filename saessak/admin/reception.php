<?php
include 'include/header.php';

// 1. 디버깅 및 에러 출력 강제 활성화
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
*/


try {
    $db_host = '172.16.11.222'; 
    $db_user = 'root';
    $db_pass = ''; 
    $db_name = 'saessak';
    $db_port = 3306; 

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

    // ✅ 연결 성공 여부 확인 추가!
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


// ==========================================
// [1] 신규 접수/예약 등록 (INSERT 로직)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_name   = mysqli_real_escape_string($conn, trim($_POST['patient_name'] ?? ''));
    $reception_type = mysqli_real_escape_string($conn, trim($_POST['res_type'] ?? ''));
    $dept_name      = mysqli_real_escape_string($conn, trim($_POST['dept_name'] ?? ''));
    $target_date    = mysqli_real_escape_string($conn, trim($_POST['res_date'] ?? ''));
    $target_time    = mysqli_real_escape_string($conn, trim($_POST['res_time'] ?? ''));
    $symptoms_memo  = mysqli_real_escape_string($conn, trim($_POST['memo'] ?? ''));

    if (!empty($patient_name) && !empty($reception_type) && !empty($dept_name) && !empty($target_date) && !empty($target_time)) {
        
        // 고유 접수번호 자동 생성 (서버 시간 기준 타임스탬프)
        $reception_no = 'RS-' . date('His') . rand(10, 99);

        $insert_query = "
            INSERT INTO receptions (reception_no, patient_name, reception_type, dept_name, target_date, target_time, symptoms_memo, status)
            VALUES ('$reception_no', '$patient_name', '$reception_type', '$dept_name', '$target_date', '$target_time', '$symptoms_memo', '대기중')
        ";

        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('우분투 DB에 신규 접수 등록이 완료되었습니다!'); location.href=location.href;</script>";
            exit;
        }
    } else {
        echo "<script>alert('필수 입력 항목을 모두 채워주세요.');</script>";
    }
}


// ==========================================
// [2] 우분투 실시간 DB 현황 조회 (SELECT 로직)
// ==========================================
// SQL 쿼리문: 테이블에 입력된 데이터들을 진료 희망 시간 순서대로 정렬하여 긁어옵니다.
// [2] 우분투 실시간 DB 현황 조회 (SELECT 로직) 수정
// 오늘 날짜를 PHP에서 자동으로 가져와서 쿼리 조건으로 사용합니다.
$today = date('Y-m-d'); 
$select_query = "SELECT * FROM receptions WHERE target_date = '$today' ORDER BY target_time ASC";
$result = mysqli_query($conn, $select_query);

$reservations = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = $row;
    }
} else {
    // 쿼리 자체가 실패하면 에러를 출력해줍니다.
    echo "쿼리 에러: " . mysqli_error($conn);
}


// UI 상태 레이블 전용 색상 변경 함수
function get_status_color($status) {
    switch ($status) {
        case '확정':   return 'bg-green-100 text-green-700 border-green-200';
        case '대기중': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case '취소':   return 'bg-red-100 text-red-700 border-red-200';
        default:       return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}

function get_type_color($type) {
    switch ($type) {
        case '예약':     return 'bg-blue-100 text-blue-700 border-blue-200';
        case '현장접수': return 'bg-purple-100 text-purple-700 border-purple-200';
        default:         return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
?>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 접수 / 예약 등록</h2>
    <form id="reception_form" action="" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">환자 성명</label>
                <input type="text" name="patient_name" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: 홍길동">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">접수 유형</label>
                <select name="res_type" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">유형 선택</option>
                    <option value="예약">예약</option>
                    <option value="현장접수">현장접수</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 진료과</label>
                <select name="dept_name" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">진료과 선택</option>
                    <option value="내과">내과</option>
                    <option value="일반내과">일반내과</option>
                    <option value="이비인후과">이비인후과</option>
                    <option value="정형외과">정형외과</option>
                    <option value="소아청소년과">소아청소년과</option>
                    <option value="가정의학과">가정의학과</option>
                    <option value="종합건진센터">종합건진센터</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 날짜</label>
                <input type="date" name="res_date" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 시간</label>
                <select name="res_time" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">시간 선택</option>
                    <option value="09:00">09:00</option>
                    <option value="09:30">09:30</option>
                    <option value="10:00">10:00</option>
                    <option value="10:30">10:30</option>
                    <option value="11:00">11:00</option>
                    <option value="11:30">11:30</option>
                    <option value="14:00">14:00</option>
                    <option value="14:30">14:30</option>
                    <option value="15:00">15:00</option>
                    <option value="15:30">15:30</option>
                    <option value="16:00">16:00</option>
                    <option value="16:30">16:30</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 mb-1">증상 메모 (선택)</label>
            <textarea name="memo" rows="3"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none"
                placeholder="증상이나 특이사항을 입력하세요."></textarea>
        </div>

        <button type="submit"
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition-colors">
            접수 등록
        </button>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">오늘 접수/예약 현황</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                    <th class="px-6 py-4 font-medium">접수번호</th>
                    <th class="px-6 py-4 font-medium">환자명</th>
                    <th class="px-6 py-4 font-medium">진료과</th>
                    <th class="px-6 py-4 font-medium">날짜</th>
                    <th class="px-6 py-4 font-medium">시간</th>
                    <th class="px-6 py-4 font-medium">유형</th>
                    <th class="px-6 py-4 font-medium">상태</th>
                    <th class="px-6 py-4 font-medium">관리</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($reservations)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-gray-400">오늘 등록된 접수/예약 내역이 없습니다.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($reservations as $res): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo htmlspecialchars($res['reception_no']); ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($res['patient_name']); ?></td>
                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($res['dept_name']); ?></td>
                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($res['target_date']); ?></td>
                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($res['target_time']); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_type_color($res['reception_type']); ?>">
                                <?php echo htmlspecialchars($res['reception_type']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_status_color($res['status']); ?>">
                                <?php echo htmlspecialchars($res['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">수정</button>
                                <button class="px-3 py-1 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors">취소</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
mysqli_close($conn);
include 'include/footer.php'; 
?>