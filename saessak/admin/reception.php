<?php
include 'include/header.php';

$db_host = '172.16.11.210'; 
$db_user = 'root';
$db_pass = ''; 
$db_name = 'saessak';
$db_port = 3306;     

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// ── 취소 처리 ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $no = mysqli_real_escape_string($conn, $_POST['reception_no']);
    mysqli_query($conn, "UPDATE receptions SET status='취소' WHERE reception_no='$no'");
    echo "<script>alert('취소 처리되었습니다.'); location.href='reception.php';</script>";
    exit;
}

// ── 수정 처리 ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $no     = mysqli_real_escape_string($conn, $_POST['reception_no']);
    $status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $dept   = mysqli_real_escape_string($conn, $_POST['new_dept']);
    $date   = mysqli_real_escape_string($conn, $_POST['new_date']);
    $time   = mysqli_real_escape_string($conn, $_POST['new_time']);
    mysqli_query($conn, "UPDATE receptions SET status='$status', dept_name='$dept', target_date='$date', target_time='$time' WHERE reception_no='$no'");
    echo "<script>alert('수정되었습니다.'); location.href='reception.php';</script>";
    exit;
}

// ── 신규 등록 처리 ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
    $patient_name   = mysqli_real_escape_string($conn, trim($_POST['patient_name'] ?? ''));
    $reception_type = mysqli_real_escape_string($conn, trim($_POST['res_type'] ?? ''));
    $dept_name      = mysqli_real_escape_string($conn, trim($_POST['dept_name'] ?? ''));
    $target_date    = mysqli_real_escape_string($conn, trim($_POST['res_date'] ?? ''));
    $target_time    = mysqli_real_escape_string($conn, trim($_POST['res_time'] ?? ''));
    $symptoms_memo  = mysqli_real_escape_string($conn, trim($_POST['memo'] ?? ''));
    $reception_no   = 'RS-' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);

    mysqli_query($conn, "INSERT INTO receptions (reception_no, patient_name, reception_type, dept_name, target_date, target_time, symptoms_memo, status)
        VALUES ('$reception_no', '$patient_name', '$reception_type', '$dept_name', '$target_date', '$target_time', '$symptoms_memo', '대기중')");
    echo "<script>alert('접수 등록 완료!'); location.href='reception.php';</script>";
    exit;
}

// ── 완료 처리 + 차트 자동 생성 ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete') {
    $no           = mysqli_real_escape_string($conn, $_POST['reception_no']);
    $age          = (int)$_POST['age'];
    $doctor_name  = mysqli_real_escape_string($conn, trim($_POST['doctor_name']));
    $diagnosis    = mysqli_real_escape_string($conn, trim($_POST['diagnosis']));
    $prescription = mysqli_real_escape_string($conn, trim($_POST['prescription']));
    $notes        = mysqli_real_escape_string($conn, trim($_POST['notes']));

    // 접수 정보 가져오기
    $res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM receptions WHERE reception_no='$no'"));

    if ($res) {
        // 이미 차트 있는지 확인
        $exist = mysqli_fetch_assoc(mysqli_query($conn, "SELECT chart_no FROM medical_records WHERE reception_no='$no'"));

        if (!$exist) {
            $chart_no    = 'CH-' . date('YmdHis') . rand(10, 99);
            $record_date = $res['target_date'];
            $patient     = mysqli_real_escape_string($conn, $res['patient_name']);
            $dept        = mysqli_real_escape_string($conn, $res['dept_name']);

            mysqli_query($conn, "INSERT INTO medical_records 
                (chart_no, reception_no, patient_name, age, dept_name, doctor_name, record_date, diagnosis, prescription, notes)
                VALUES ('$chart_no', '$no', '$patient', $age, '$dept', '$doctor_name', '$record_date', '$diagnosis', '$prescription', '$notes')");
        }

        // 상태 완료로 변경
        mysqli_query($conn, "UPDATE receptions SET status='완료' WHERE reception_no='$no'");
        echo "<script>alert('진료 완료 처리 및 차트가 생성되었습니다.'); location.href='reception.php';</script>";
    } else {
        echo "<script>alert('접수 정보를 찾을 수 없습니다.'); history.back();</script>";
    }
    exit;
}

// ── 목록 조회 (전체 날짜) ──
$result = mysqli_query($conn, "SELECT * FROM receptions ORDER BY target_date DESC, target_time ASC");
$reservations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reservations[] = $row;
}

// 의사 목록 (담당의 선택용)
$doctor_result = mysqli_query($conn, "SELECT name, dept_name FROM medical_staffs WHERE position='의사' AND status='재직중' ORDER BY dept_name");
$doctors = [];
while ($d = mysqli_fetch_assoc($doctor_result)) {
    $doctors[] = $d;
}

function get_status_color($s) {
    switch($s) {
        case '확정':   return 'bg-green-100 text-green-700 border-green-200';
        case '대기중': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case '취소':   return 'bg-red-100 text-red-700 border-red-200';
        case '진료중': return 'bg-blue-100 text-blue-700 border-blue-200';
        case '완료':   return 'bg-gray-100 text-gray-600 border-gray-200';
        default:       return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
function get_type_color($t) {
    switch($t) {
        case '예약':     return 'bg-blue-100 text-blue-700 border-blue-200';
        case '현장접수': return 'bg-purple-100 text-purple-700 border-purple-200';
        default:         return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
?>

<!-- ── 수정 모달 ── -->
<div id="edit-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">접수 정보 수정</h3>
        <form method="POST" action="reception.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="reception_no" id="edit-no">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">상태</label>
                    <select name="new_status" id="edit-status" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <option value="대기중">대기중</option>
                        <option value="진료중">진료중</option>
                        <option value="확정">확정</option>
                        <option value="완료">완료</option>
                        <option value="취소">취소</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">진료과</label>
                    <select name="new_dept" id="edit-dept" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <?php foreach(['내과','일반내과','호흡기내과','이비인후과','정형외과','소아청소년과','가정의학과','신경과','종합건진센터'] as $d): ?>
                        <option value="<?= $d ?>"><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">날짜</label>
                    <input type="date" name="new_date" id="edit-date" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">시간</label>
                    <select name="new_time" id="edit-time" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <?php foreach(['09:00','09:30','10:00','10:30','11:00','11:30','14:00','14:30','15:00','15:30','16:00','16:30'] as $t): ?>
                        <option value="<?= $t ?>"><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg text-sm">저장</button>
                <button type="button" onclick="closeModal('edit-modal')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-lg text-sm">취소</button>
            </div>
        </form>
    </div>
</div>

<!-- ── 완료 처리 (차트 생성) 모달 ── -->
<div id="complete-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg my-auto">
        <div class="px-6 py-4 bg-green-600 text-white flex justify-between items-center rounded-t-xl">
            <h3 class="text-lg font-bold">✅ 진료 완료 처리 및 차트 생성</h3>
            <button type="button" onclick="closeModal('complete-modal')" class="text-white hover:text-gray-200 text-2xl font-semibold">&times;</button>
        </div>
        <form method="POST" action="reception.php" class="p-6 space-y-4">
            <input type="hidden" name="action" value="complete">
            <input type="hidden" name="reception_no" id="complete-no">

            <!-- 환자 정보 표시 -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 grid grid-cols-2 gap-2 text-sm">
                <div><span class="text-gray-400 text-xs font-bold">환자명</span><p id="complete-name" class="font-bold text-gray-800 mt-0.5"></p></div>
                <div><span class="text-gray-400 text-xs font-bold">진료과</span><p id="complete-dept" class="font-bold text-gray-800 mt-0.5"></p></div>
                <div><span class="text-gray-400 text-xs font-bold">예약일</span><p id="complete-date" class="text-gray-600 mt-0.5"></p></div>
                <div><span class="text-gray-400 text-xs font-bold">증상메모</span><p id="complete-memo" class="text-gray-600 mt-0.5 text-xs"></p></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">나이 <span class="text-red-500">*</span></label>
                    <input type="number" name="age" required min="1" max="120"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-500" placeholder="예: 45">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">담당의 <span class="text-red-500">*</span></label>
                    <select name="doctor_name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-500">
                        <option value="">선택</option>
                        <?php foreach ($doctors as $doc): ?>
                        <option value="<?= htmlspecialchars($doc['name']) ?>">
                            <?= htmlspecialchars($doc['name']) ?> (<?= htmlspecialchars($doc['dept_name']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">진단명 <span class="text-red-500">*</span></label>
                <input type="text" name="diagnosis" required
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-500" placeholder="예: 고혈압, 급성 편도염">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">처방 내역</label>
                <textarea name="prescription" rows="3"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-500 font-mono text-xs"
                    placeholder="예: 암로디핀 5mg 1일 1회, 아스피린 100mg 1일 1회"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">의사 소견 / 비고</label>
                <textarea name="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-500 text-xs"
                    placeholder="예: 혈압 145/90, 1개월 후 재진 권고"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg text-sm">완료 처리 + 차트 생성</button>
                <button type="button" onclick="closeModal('complete-modal')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-lg text-sm">취소</button>
            </div>
        </form>
    </div>
</div>

<!-- ── 진단서 모달 (완료 환자용) ── -->
<div id="cert-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 overflow-y-auto py-10">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden border border-gray-300 my-auto">
        <div class="px-6 py-4 bg-gray-800 text-white flex justify-between items-center">
            <h3 class="text-lg font-bold">📄 진단서 보기</h3>
            <button type="button" onclick="closeModal('cert-modal')" class="text-gray-400 hover:text-white text-2xl font-semibold">&times;</button>
        </div>
        <div id="certPrintArea" class="p-10 bg-white text-black">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-extrabold tracking-[1em] border-b-4 border-double border-black pb-4 inline-block w-full">진단서</h1>
            </div>
            <table class="w-full border-collapse border border-black text-sm mb-6">
                <tbody>
                    <tr>
                        <td class="border border-black bg-gray-50 px-4 py-3 font-bold text-center w-24">병명</td>
                        <td class="border border-black px-4 py-3" id="c-diagnosis">--</td>
                        <td class="border border-black bg-gray-50 px-4 py-3 font-bold text-center w-28">진료 일자</td>
                        <td class="border border-black px-4 py-3 w-40" id="c-date">--</td>
                    </tr>
                    <tr>
                        <td class="border border-black bg-gray-50 px-4 py-3 font-bold text-center">환자 성명</td>
                        <td class="border border-black px-4 py-3 font-bold text-base" id="c-name">--</td>
                        <td class="border border-black bg-gray-50 px-4 py-3 font-bold text-center">차트 번호</td>
                        <td class="border border-black px-4 py-3 font-mono" id="c-chartno">--</td>
                    </tr>
                </tbody>
            </table>
            <div class="border border-black p-6 min-h-[120px] mb-6">
                <h3 class="font-bold text-sm text-gray-700 mb-2">■ 처방 내역 및 조제 정보</h3>
                <p class="text-base leading-relaxed pl-2 whitespace-pre-wrap" id="c-prescription">--</p>
            </div>
            <div class="border border-black p-6 min-h-[150px] mb-10">
                <h3 class="font-bold text-sm text-gray-700 mb-2">■ 의사 소견 및 향후 치료 의견</h3>
                <p class="text-base leading-relaxed pl-2 whitespace-pre-wrap" id="c-notes">--</p>
            </div>
            <div class="text-center space-y-2 mb-8">
                <p class="text-lg font-medium">위와 같이 진단합니다.</p>
                <p class="text-gray-600 font-mono"><?= date('Y년 m월 d일') ?></p>
            </div>
            <div class="flex justify-end items-center gap-4 border-t border-gray-200 pt-6">
                <div class="text-right">
                    <p class="text-sm text-gray-500">의료기관 : 새싹종합병원</p>
                    <p class="text-base font-bold text-gray-800">담당의사 : <span id="c-doctor">--</span> (인)</p>
                </div>
                <div class="w-14 h-14 border border-red-400 rounded-full flex items-center justify-center text-red-500 text-xs font-bold border-dashed transform rotate-12">새싹병원</div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-2">
            <button type="button" onclick="closeModal('cert-modal')" class="px-4 py-2 text-sm font-medium bg-white hover:bg-gray-100 text-gray-700 border border-gray-200 rounded-lg">닫기</button>
            <button type="button" onclick="window.print()" class="px-4 py-2 text-sm font-medium bg-green-600 hover:bg-green-700 text-white rounded-lg">🖨️ 진단서 출력</button>
        </div>
    </div>
</div>

<!-- ── 신규 등록 폼 ── -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 접수 / 예약 등록</h2>
    <form method="POST" action="reception.php">
        <input type="hidden" name="action" value="insert">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">환자 성명</label>
                <input type="text" name="patient_name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="예: 홍길동">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">접수 유형</label>
                <select name="res_type" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                    <option value="">유형 선택</option>
                    <option value="예약">예약</option>
                    <option value="현장접수">현장접수</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 진료과</label>
                <select name="dept_name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                    <option value="">진료과 선택</option>
                    <?php foreach(['내과','일반내과','호흡기내과','이비인후과','정형외과','소아청소년과','가정의학과','신경과','종합건진센터'] as $d): ?>
                    <option value="<?= $d ?>"><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 날짜</label>
                <input type="date" name="res_date" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 시간</label>
                <select name="res_time" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                    <option value="">시간 선택</option>
                    <?php foreach(['09:00','09:30','10:00','10:30','11:00','11:30','14:00','14:30','15:00','15:30','16:00','16:30'] as $t): ?>
                    <option value="<?= $t ?>"><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 mb-1">증상 메모 (선택)</label>
            <textarea name="memo" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm resize-none" placeholder="증상이나 특이사항을 입력하세요."></textarea>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm">접수 등록</button>
    </form>
</div>

<!-- ── 목록 ── -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">전체 접수 / 예약 현황</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                    <th class="px-4 py-4 font-medium">접수번호</th>
                    <th class="px-4 py-4 font-medium">환자명</th>
                    <th class="px-4 py-4 font-medium">진료과</th>
                    <th class="px-4 py-4 font-medium">날짜</th>
                    <th class="px-4 py-4 font-medium">시간</th>
                    <th class="px-4 py-4 font-medium">유형</th>
                    <th class="px-4 py-4 font-medium">상태</th>
                    <th class="px-4 py-4 font-medium">관리</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($reservations)): ?>
                <tr><td colspan="8" class="px-6 py-10 text-center text-gray-400">등록된 접수/예약 내역이 없습니다.</td></tr>
                <?php else: ?>
                <?php foreach ($reservations as $res): ?>
                <tr class="hover:bg-gray-50 transition-colors <?= $res['status'] === '완료' ? 'opacity-60' : '' ?>">
                    <td class="px-4 py-4 text-gray-400 font-mono text-xs"><?= htmlspecialchars($res['reception_no']) ?></td>
                    <td class="px-4 py-4 font-bold text-gray-800"><?= htmlspecialchars($res['patient_name']) ?></td>
                    <td class="px-4 py-4 text-gray-600"><?= htmlspecialchars($res['dept_name']) ?></td>
                    <td class="px-4 py-4 text-gray-600"><?= htmlspecialchars($res['target_date']) ?></td>
                    <td class="px-4 py-4 text-gray-600"><?= htmlspecialchars($res['target_time']) ?></td>
                    <td class="px-4 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?= get_type_color($res['reception_type']) ?>">
                            <?= htmlspecialchars($res['reception_type']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?= get_status_color($res['status']) ?>">
                            <?= htmlspecialchars($res['status']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex gap-1.5 flex-wrap">
                            <?php if ($res['status'] !== '완료' && $res['status'] !== '취소'): ?>
                            <!-- 수정 버튼 -->
                            <button onclick="openEditModal('<?= $res['reception_no'] ?>', '<?= $res['status'] ?>', '<?= $res['dept_name'] ?>', '<?= $res['target_date'] ?>', '<?= $res['target_time'] ?>')"
                                class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">수정</button>
                            <!-- 완료 처리 버튼 -->
                            <button onclick="openCompleteModal('<?= $res['reception_no'] ?>', '<?= htmlspecialchars($res['patient_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($res['dept_name'], ENT_QUOTES) ?>', '<?= $res['target_date'] ?>', '<?= htmlspecialchars($res['symptoms_memo'] ?? '', ENT_QUOTES) ?>')"
                                class="px-3 py-1 text-xs font-medium bg-green-100 hover:bg-green-200 text-green-700 rounded-lg">완료</button>
                            <!-- 취소 버튼 -->
                            <form method="POST" action="reception.php" onsubmit="return confirm('취소 처리하시겠습니까?')" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="reception_no" value="<?= $res['reception_no'] ?>">
                                <button type="submit" class="px-3 py-1 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-700 rounded-lg">취소</button>
                            </form>
                            <?php else: ?>
                            <!-- 완료된 경우 진단서 보기 -->
                            <?php
                            $chart = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM medical_records WHERE reception_no='" . mysqli_real_escape_string($conn, $res['reception_no']) . "'"));
                            if ($chart):
                            ?>
                            <button onclick="openCertModal(
                                '<?= htmlspecialchars($chart['patient_name'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($chart['diagnosis'], ENT_QUOTES) ?>',
                                '<?= $chart['record_date'] ?>',
                                '<?= htmlspecialchars($chart['doctor_name'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($chart['prescription'] ?? '', ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($chart['notes'] ?? '', ENT_QUOTES) ?>',
                                '<?= $chart['chart_no'] ?>'
                            )" class="px-3 py-1 text-xs font-medium bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg">📄 진단서</button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function openEditModal(no, status, dept, date, time) {
    document.getElementById('edit-no').value = no;
    document.getElementById('edit-status').value = status;
    document.getElementById('edit-dept').value = dept;
    document.getElementById('edit-date').value = date;
    document.getElementById('edit-time').value = time;
    document.getElementById('edit-modal').classList.remove('hidden');
}

function openCompleteModal(no, name, dept, date, memo) {
    document.getElementById('complete-no').value = no;
    document.getElementById('complete-name').textContent = name;
    document.getElementById('complete-dept').textContent = dept;
    document.getElementById('complete-date').textContent = date;
    document.getElementById('complete-memo').textContent = memo || '없음';
    document.getElementById('complete-modal').classList.remove('hidden');
}

function openCertModal(name, diagnosis, date, doctor, prescription, notes, chartno) {
    document.getElementById('c-name').textContent = name;
    document.getElementById('c-diagnosis').textContent = diagnosis;
    document.getElementById('c-date').textContent = date;
    document.getElementById('c-doctor').textContent = doctor;
    document.getElementById('c-prescription').textContent = prescription || '처방 없음';
    document.getElementById('c-notes').textContent = notes || '소견 없음';
    document.getElementById('c-chartno').textContent = chartno;
    document.getElementById('cert-modal').classList.remove('hidden');
}

// 인쇄 시 진단서만 출력
window.onbeforeprint = function() {
    document.body.innerHTML = document.getElementById('certPrintArea').innerHTML;
};
</script>

<?php 
mysqli_close($conn);
include 'include/footer.php'; 
?>