<?php
include 'include/header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================================
// 1. DB 연결
// ==========================================
$conn = mysqli_connect('175.210.161.42', 'DH', '1234', 'saessak', 3306);
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// ── 의료진 정보 수정 처리 ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $staff_no = mysqli_real_escape_string($conn, $_POST['staff_no']);
    $status   = mysqli_real_escape_string($conn, $_POST['new_status']);
    $dept     = mysqli_real_escape_string($conn, $_POST['new_dept']);
    $schedule = mysqli_real_escape_string($conn, $_POST['new_schedule']);

    $update_query = "UPDATE medical_staffs 
                     SET status='$status', dept_name='$dept', work_schedule='$schedule' 
                     WHERE staff_no='$staff_no'";
    
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('수정되었습니다.'); location.href='staff_account.php';</script>";
        exit;
    } else {
        echo "<script>alert('수정 실패: " . mysqli_error($conn) . "');</script>";
    }
}

// ==========================================
// 2. 신규 의료진/스케줄 등록 (INSERT)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_no = 'DR-' . date('YmdHis') . rand(10, 99);
    $name = mysqli_real_escape_string($conn, trim($_POST['staff_name'] ?? ''));
    $position = mysqli_real_escape_string($conn, trim($_POST['staff_role'] ?? ''));
    $dept_name = mysqli_real_escape_string($conn, trim($_POST['dept_name'] ?? ''));
    $work_schedule = mysqli_real_escape_string($conn, trim($_POST['work_schedule'] ?? ''));
    $hire_date = date('Y-m-d');
    $phone = '010-0000-0000';

    if (!empty($name) && !empty($position) && !empty($dept_name) && !empty($work_schedule)) {
        $insert_query = "INSERT INTO medical_staffs (staff_no, name, position, dept_name, phone, hire_date, status, work_schedule) 
                         VALUES ('$staff_no', '$name', '$position', '$dept_name', '$phone', '$hire_date', '재직중', '$work_schedule')";
        
        if (mysqli_query($conn, $insert_query)) {
            // 등록 성공 후 즉시 페이지 새로고침 (Redirect)
            echo "<script>alert('의료진 등록이 완료되었습니다.'); location.href='staff_account.php';</script>";
            exit; 
        } else {
            echo "<script>alert('등록 실패: " . mysqli_error($conn) . "');</script>";
        }
    }
}


// ==========================================
// 3. 현재 의료진 스케줄 현황 조회 (SELECT)
// ==========================================
$select_query = "SELECT * FROM medical_staffs ORDER BY hire_date DESC";
$result = mysqli_query($conn, $select_query);

$staff_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $staff_list[] = $row;
    }
} else {
    echo "쿼리 에러: " . mysqli_error($conn);
}


// 역할 배지 색상 함수
function get_role_badge($position) {
    switch ($position) {
        case '의사': return 'bg-blue-100 text-blue-700 border-blue-200';
        case '간호사': return 'bg-purple-100 text-purple-700 border-purple-200';
        default: return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}

// 상태 배지 색상 함수
function get_status_badge($status) {
    switch ($status) {
        case '재직중': return 'bg-green-100 text-green-700 border-green-200';
        case '휴직중': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case '퇴사': return 'bg-red-100 text-red-700 border-red-200';
        default: return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
?>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 의료진/스케줄 등록</h2>
    <form id="staff_form" action="" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">성명</label>
                <input type="text" name="staff_name" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: 김닥터">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">직책</label>
                <select name="staff_role" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">선택</option>
                    <option value="의사">의사</option>
                    <option value="간호사">간호사</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">담당 과</label>
                <select name="dept_name" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">선택</option>
                    <option value="가정의학과">가정의학과</option>
                    <option value="일반내과">일반내과</option>
                    <option value="정형외과">정형외과</option>
                    <option value="소아청소년과">소아청소년과</option>
                    <option value="이비인후과">이비인후과</option>
                    <option value="종합건진센터">종합건진센터</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 mb-1">근무 형태 (스케줄)</label>
            <select name="work_schedule" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                <option value="주간 (09:00 - 18:00)">주간 (09:00 - 18:00)</option>
                <option value="오후 교대 (14:00 - 22:00)">오후 교대 (14:00 - 22:00)</option>
                <option value="야간 (22:00 - 09:00)">야간 (22:00 - 09:00)</option>
                <option value="휴무 (Off)">휴무 (Off)</option>
            </select>
        </div>

        <button type="submit"
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition-colors">
            의료진 등록
        </button>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">현재 의료진 스케줄 현황</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                    <th class="px-6 py-4 font-medium">사번</th>
                    <th class="px-6 py-4 font-medium">성명</th>
                    <th class="px-6 py-4 font-medium">직책</th>
                    <th class="px-6 py-4 font-medium">담당 과</th>
                    <th class="px-6 py-4 font-medium">현재 스케줄</th>
                    <th class="px-6 py-4 font-medium">상태</th>
                    <th class="px-6 py-4 font-medium">관리</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($staff_list)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">등록된 의료진이 없습니다.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($staff_list as $staff): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo htmlspecialchars($staff['staff_no']); ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($staff['name']); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_role_badge($staff['position']); ?>">
                                <?php echo htmlspecialchars($staff['position']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($staff['dept_name']); ?></td>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo htmlspecialchars($staff['work_schedule']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_status_badge($staff['status']); ?>">
                                <?php echo htmlspecialchars($staff['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="openModal('<?php echo $staff['staff_no']; ?>', '<?php echo $staff['status']; ?>', '<?php echo $staff['dept_name']; ?>', '<?php echo $staff['work_schedule']; ?>')" 
                                    class="px-4 py-1.5 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                                수정
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        
    </div>
</div>

<!-- 수정 모달 -->
<div id="edit-modal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">

        <h3 class="text-lg font-bold text-gray-800 mb-4">
            의료진 정보 수정
        </h3>

        <form method="POST">

            <input type="hidden" name="action" value="update">
            <input type="hidden" name="staff_no" id="edit-no">

            <!-- 상태 -->
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    상태
                </label>

                <select name="new_status" id="edit-status"
                    class="w-full px-4 py-2.5 border rounded-lg text-sm">

                    <option value="재직중">재직중</option>
                    <option value="휴직중">휴직중</option>
                    <option value="퇴사">퇴사</option>

                </select>
            </div>

            <!-- 담당과 -->
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    담당 과
                </label>

                <select name="new_dept" id="edit-dept"
                    class="w-full px-4 py-2.5 border rounded-lg text-sm">

                    <option value="가정의학과">가정의학과</option>
                    <option value="일반내과">일반내과</option>
                    <option value="정형외과">정형외과</option>
                    <option value="소아청소년과">소아청소년과</option>
                    <option value="이비인후과">이비인후과</option>
                    <option value="종합건진센터">종합건진센터</option>

                </select>
            </div>

            <!-- 스케줄 -->
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">
                    근무 스케줄
                </label>

                <select name="new_schedule" id="edit-schedule"
                    class="w-full px-4 py-2.5 border rounded-lg text-sm">

                    <option value="주간 (09:00 - 18:00)">주간 (09:00 - 18:00)</option>
                    <option value="오후 교대 (14:00 - 22:00)">오후 교대 (14:00 - 22:00)</option>
                    <option value="야간 (22:00 - 09:00)">야간 (22:00 - 09:00)</option>
                    <option value="휴무 (Off)">휴무 (Off)</option>

                </select>
            </div>

            <div class="flex gap-2">

                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm">
                    저장
                </button>

                <button type="button"
                    onclick="closeModal()"
                    class="bg-gray-100 hover:bg-gray-200 py-2 px-4 rounded-lg text-sm">
                    취소
                </button>

            </div>

        </form>

    </div>
</div>


<script>
function openModal(no, status, dept, sched) {

    document.getElementById('edit-no').value = no;
    document.getElementById('edit-status').value = status;
    document.getElementById('edit-dept').value = dept;
    document.getElementById('edit-schedule').value = sched;

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