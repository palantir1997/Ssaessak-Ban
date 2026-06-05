<?php
include 'include/header.php';

$db_host = '172.16.11.222'; 
$db_user = 'root'; 
$db_pass = '';         
$db_name = 'saessak';      

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// ── 삭제 처리 (진짜 DELETE) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $no = mysqli_real_escape_string($conn, $_POST['staff_no']);
    mysqli_query($conn, "DELETE FROM medical_staffs WHERE staff_no='$no'");
    echo "<script>alert('의료진이 삭제되었습니다.'); location.href='mdppl.php';</script>";
    exit;
}

// ── 수정 처리 ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $no        = mysqli_real_escape_string($conn, $_POST['staff_no']);
    $name      = mysqli_real_escape_string($conn, $_POST['new_name']);
    $position  = mysqli_real_escape_string($conn, $_POST['new_position']);
    $dept_name = mysqli_real_escape_string($conn, $_POST['new_dept']);
    $phone     = mysqli_real_escape_string($conn, $_POST['new_phone']);
    $hire_date = mysqli_real_escape_string($conn, $_POST['new_hire_date']);
    $status    = mysqli_real_escape_string($conn, $_POST['new_status']);
    mysqli_query($conn, "UPDATE medical_staffs SET name='$name', position='$position', dept_name='$dept_name', phone='$phone', hire_date='$hire_date', status='$status' WHERE staff_no='$no'");
    echo "<script>alert('수정되었습니다.'); location.href='mdppl.php';</script>";
    exit;
}

// ── 신규 등록 처리 ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
    $name      = mysqli_real_escape_string($conn, $_POST['staff_name']);
    $position  = mysqli_real_escape_string($conn, $_POST['staff_role']);
    $dept_name = mysqli_real_escape_string($conn, $_POST['dept_name']);
    $phone     = mysqli_real_escape_string($conn, $_POST['staff_phone']);
    $hire_date = mysqli_real_escape_string($conn, $_POST['join_date']);
    $rand_no   = 'DR-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    $status    = '재직중';
    mysqli_query($conn, "INSERT INTO medical_staffs (staff_no, name, position, dept_name, phone, hire_date, status) VALUES ('$rand_no', '$name', '$position', '$dept_name', '$phone', '$hire_date', '$status')");
    echo "<script>alert('신규 의료진이 등록되었습니다.'); location.href='mdppl.php';</script>";
    exit;
}

// ── 목록 조회 ──
$result_staff = mysqli_query($conn, "SELECT * FROM medical_staffs ORDER BY hire_date DESC");
$staffs = [];
while ($row = mysqli_fetch_assoc($result_staff)) {
    $staffs[] = $row;
}

function get_role_color($r) {
    switch($r) {
        case '의사':   return 'bg-blue-100 text-blue-700 border-blue-200';
        case '간호사': return 'bg-purple-100 text-purple-700 border-purple-200';
        default:       return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
function get_status_color($s) {
    switch($s) {
        case '재직중': return 'bg-green-100 text-green-700 border-green-200';
        case '휴직중': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case '퇴사':   return 'bg-red-100 text-red-700 border-red-200';
        default:       return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
?>

<!-- 수정 모달 -->
<div id="edit-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">의료진 정보 수정</h3>
        <form method="POST" action="mdppl.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="staff_no" id="edit-no">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">성명</label>
                    <input type="text" name="new_name" id="edit-name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">직책</label>
                    <select name="new_position" id="edit-position" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <option value="의사">의사</option>
                        <option value="간호사">간호사</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">담당 진료과</label>
                    <select name="new_dept" id="edit-dept" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <option value="가정의학과">가정의학과</option>
                        <option value="일반내과">일반내과</option>
                        <option value="호흡기내과">호흡기내과</option>
                        <option value="정형외과">정형외과</option>
                        <option value="소아청소년과">소아청소년과</option>
                        <option value="신경과">신경과</option>
                        <option value="종합건진센터">종합건진센터</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">연락처</label>
                    <input type="text" name="new_phone" id="edit-phone" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">입사일</label>
                    <input type="date" name="new_hire_date" id="edit-hire-date" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">상태</label>
                    <select name="new_status" id="edit-status" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <option value="재직중">재직중</option>
                        <option value="휴직중">휴직중</option>
                        <option value="퇴사">퇴사</option>
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

<!-- 신규 등록 폼 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 의료진 등록</h2>
    <form method="POST" action="mdppl.php">
        <input type="hidden" name="action" value="insert">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">성명</label>
                <input type="text" name="staff_name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500" placeholder="예: 홍길동">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">직책</label>
                <select name="staff_role" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">직책 선택</option>
                    <option value="의사">의사</option>
                    <option value="간호사">간호사</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">담당 진료과</label>
                <select name="dept_name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">진료과 선택</option>
                    <option value="가정의학과">가정의학과</option>
                    <option value="일반내과">일반내과</option>
                    <option value="호흡기내과">호흡기내과</option>
                    <option value="정형외과">정형외과</option>
                    <option value="소아청소년과">소아청소년과</option>
                    <option value="신경과">신경과</option>
                    <option value="종합건진센터">종합건진센터</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">연락처</label>
                <input type="text" name="staff_phone" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500" placeholder="010-0000-0000">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">입사일</label>
                <input type="date" name="join_date" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm">의료진 등록</button>
    </form>
</div>

<!-- 목록 테이블 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">의료진 현황</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                    <th class="px-6 py-4 font-medium">직원번호</th>
                    <th class="px-6 py-4 font-medium">성명</th>
                    <th class="px-6 py-4 font-medium">직책</th>
                    <th class="px-6 py-4 font-medium">담당 진료과</th>
                    <th class="px-6 py-4 font-medium">연락처</th>
                    <th class="px-6 py-4 font-medium">입사일</th>
                    <th class="px-6 py-4 font-medium">상태</th>
                    <th class="px-6 py-4 font-medium">관리</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($staffs)): ?>
                <tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">등록된 의료진이 없습니다.</td></tr>
                <?php else: ?>
                <?php foreach ($staffs as $staff): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?= htmlspecialchars($staff['staff_no']) ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($staff['name']) ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?= get_role_color($staff['position']) ?>">
                            <?= htmlspecialchars($staff['position']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($staff['dept_name']) ?></td>
                    <td class="px-6 py-4 text-gray-600 font-mono text-xs"><?= htmlspecialchars($staff['phone']) ?></td>
                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($staff['hire_date']) ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?= get_status_color($staff['status']) ?>">
                            <?= htmlspecialchars($staff['status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button onclick="openModal('<?= $staff['staff_no'] ?>', '<?= addslashes($staff['name']) ?>', '<?= $staff['position'] ?>', '<?= $staff['dept_name'] ?>', '<?= $staff['phone'] ?>', '<?= $staff['hire_date'] ?>', '<?= $staff['status'] ?>')"
                                class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">수정</button>
                            <form method="POST" action="mdppl.php" onsubmit="return confirm('정말 삭제하시겠습니까? 복구할 수 없습니다.')" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="staff_no" value="<?= $staff['staff_no'] ?>">
                                <button type="submit" class="px-3 py-1 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-700 rounded-lg">삭제</button>
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
function openModal(no, name, position, dept, phone, hireDate, status) {
    document.getElementById('edit-no').value = no;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-position').value = position;
    document.getElementById('edit-dept').value = dept;
    document.getElementById('edit-phone').value = phone;
    document.getElementById('edit-hire-date').value = hireDate;
    document.getElementById('edit-status').value = status;
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