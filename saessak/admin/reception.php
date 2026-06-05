<?php
include 'include/header.php';

$db_host = '172.16.11.222'; 
$db_user = 'root';
$db_pass = ''; // MySQL 비밀번호가 있다면 여기에 입력하세요
$db_name = 'saessak';
$db_port = 3306;     


$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// ── 삭제 처리 ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $no = mysqli_real_escape_string($conn, $_POST['reception_no']);
    mysqli_query($conn, "UPDATE receptions SET status='취소' WHERE reception_no='$no'");
    echo "<script>alert('취소 처리되었습니다.'); location.href='reception.php';</script>";
    exit;
}

// ── 수정 처리 ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $no        = mysqli_real_escape_string($conn, $_POST['reception_no']);
    $status    = mysqli_real_escape_string($conn, $_POST['new_status']);
    $dept      = mysqli_real_escape_string($conn, $_POST['new_dept']);
    $date      = mysqli_real_escape_string($conn, $_POST['new_date']);
    $time      = mysqli_real_escape_string($conn, $_POST['new_time']);
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

// ── 목록 조회 ──
$today = date('Y-m-d');
$result = mysqli_query($conn, "SELECT * FROM receptions WHERE target_date = '$today' ORDER BY target_time ASC");
$reservations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reservations[] = $row;
}

function get_status_color($s) {
    switch($s) {
        case '확정':   return 'bg-green-100 text-green-700 border-green-200';
        case '대기중': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case '취소':   return 'bg-red-100 text-red-700 border-red-200';
        case '진료중': return 'bg-blue-100 text-blue-700 border-blue-200';
        case '완료':   return 'bg-gray-100 text-gray-700 border-gray-200';
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

<!-- 수정 모달 -->
<div id="edit-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">접수 정보 수정</h3>
        <form method="POST" action="reception.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="reception_no" id="edit-no">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">상태</label>
                    <select name="new_status" id="edit-status" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <option value="대기중">대기중</option>
                        <option value="진료중">진료중</option>
                        <option value="확정">확정</option>
                        <option value="완료">완료</option>
                        <option value="취소">취소</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">진료과</label>
                    <select name="new_dept" id="edit-dept" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <option value="내과">내과</option>
                        <option value="일반내과">일반내과</option>
                        <option value="호흡기내과">호흡기내과</option>
                        <option value="이비인후과">이비인후과</option>
                        <option value="정형외과">정형외과</option>
                        <option value="소아청소년과">소아청소년과</option>
                        <option value="가정의학과">가정의학과</option>
                        <option value="신경과">신경과</option>
                        <option value="종합건진센터">종합건진센터</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">날짜</label>
                    <input type="date" name="new_date" id="edit-date" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">시간</label>
                    <select name="new_time" id="edit-time" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <?php foreach(['09:00','09:30','10:00','10:30','11:00','11:30','14:00','14:30','15:00','15:30','16:00','16:30'] as $t): ?>
                        <option value="<?= $t ?>"><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg text-sm">저장</button>
                <button type="button" onclick="closeModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-lg text-sm">취소</button>
            </div>
        </form>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 접수 / 예약 등록</h2>
    <form method="POST" action="reception.php">
        <input type="hidden" name="action" value="insert">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">환자 성명</label>
                <input type="text" name="patient_name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500" placeholder="예: 홍길동">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">접수 유형</label>
                <select name="res_type" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">유형 선택</option>
                    <option value="예약">예약</option>
                    <option value="현장접수">현장접수</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 진료과</label>
                <select name="dept_name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">진료과 선택</option>
                    <option value="내과">내과</option>
                    <option value="일반내과">일반내과</option>
                    <option value="호흡기내과">호흡기내과</option>
                    <option value="이비인후과">이비인후과</option>
                    <option value="정형외과">정형외과</option>
                    <option value="소아청소년과">소아청소년과</option>
                    <option value="가정의학과">가정의학과</option>
                    <option value="신경과">신경과</option>
                    <option value="종합건진센터">종합건진센터</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 날짜</label>
                <input type="date" name="res_date" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 시간</label>
                <select name="res_time" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">시간 선택</option>
                    <?php foreach(['09:00','09:30','10:00','10:30','11:00','11:30','14:00','14:30','15:00','15:30','16:00','16:30'] as $t): ?>
                    <option value="<?= $t ?>"><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 mb-1">증상 메모 (선택)</label>
            <textarea name="memo" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none" placeholder="증상이나 특이사항을 입력하세요."></textarea>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm">접수 등록</button>
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
                <tr><td colspan="8" class="px-6 py-10 text-center text-gray-400">오늘 등록된 접수/예약 내역이 없습니다.</td></tr>
                <?php else: ?>
                <?php foreach ($reservations as $res): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?= htmlspecialchars($res['reception_no']) ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($res['patient_name']) ?></td>
                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($res['dept_name']) ?></td>
                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($res['target_date']) ?></td>
                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($res['target_time']) ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?= get_type_color($res['reception_type']) ?>">
                            <?= htmlspecialchars($res['reception_type']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?= get_status_color($res['status']) ?>">
                            <?= htmlspecialchars($res['status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button onclick="openModal('<?= $res['reception_no'] ?>', '<?= $res['status'] ?>', '<?= $res['dept_name'] ?>', '<?= $res['target_date'] ?>', '<?= $res['target_time'] ?>')"
                                class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">수정</button>
                            <form method="POST" action="reception.php" onsubmit="return confirm('취소 처리하시겠습니까?')" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="reception_no" value="<?= $res['reception_no'] ?>">
                                <button type="submit" class="px-3 py-1 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-700 rounded-lg">취소</button>
                            </form>
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
function openModal(no, status, dept, date, time) {
    document.getElementById('edit-no').value = no;
    document.getElementById('edit-status').value = status;
    document.getElementById('edit-dept').value = dept;
    document.getElementById('edit-date').value = date;
    document.getElementById('edit-time').value = time;
    document.getElementById('edit-modal').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}
</script>

<?php 
mysqli_close($conn);
include 'include/footer.php'; 
?>