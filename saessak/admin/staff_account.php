<?php
include 'include/header.php';

// ==========================================
// 1. DB 연결
// ==========================================
try {
    $db_host = '172.16.11.222'; 
    $db_user = 'root';
    $db_pass = ''; 
    $db_name = 'saessak';
    $db_port = 3306; 

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

    if (!$conn) {
        die("<div class='p-6 bg-red-100 text-red-700 rounded-xl m-6'>
                <h1 class='text-xl font-bold mb-2'>🚨 DB 서버 연결 실패</h1>
                <p>PHP 웹서버가 데이터베이스({$db_host}) 접속에 실패했습니다.</p>
                <p class='mt-4 font-mono text-sm bg-white p-3 rounded border border-red-200'>에러 내용: " . mysqli_connect_error() . "</p>
             </div>");
    }

    mysqli_set_charset($conn, 'utf8mb4');
} catch (Exception $e) {
    die("<div class='p-6 bg-red-100 text-red-700 rounded-xl m-6'>
            <h1 class='text-xl font-bold mb-2'>🚨 DB 서버 연결 실패</h1>
            <p>에러 내용: " . $e->getMessage() . "</p>
         </div>");
}


// ==========================================
// 2. 신규 의료진/스케줄 등록 (INSERT)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_no = 'DR-' . date('YmdHis') . rand(10, 99); // 자동 생성
    $name = mysqli_real_escape_string($conn, trim($_POST['staff_name'] ?? ''));
    $position = mysqli_real_escape_string($conn, trim($_POST['staff_role'] ?? ''));
    $dept_name = mysqli_real_escape_string($conn, trim($_POST['dept_name'] ?? ''));
    $work_schedule = mysqli_real_escape_string($conn, trim($_POST['work_schedule'] ?? ''));
    $hire_date = date('Y-m-d');
    $phone = '010-0000-0000'; // 기본값 (나중에 수정 가능)

    if (!empty($name) && !empty($position) && !empty($dept_name) && !empty($work_schedule)) {
        // medical_staffs 테이블에 INSERT
        $insert_query = "
            INSERT INTO medical_staffs (staff_no, name, position, dept_name, phone, hire_date, status)
            VALUES ('$staff_no', '$name', '$position', '$dept_name', '$phone', '$hire_date', '재직중')
        ";

        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('의료진이 등록되었습니다!'); location.href='staff_account.php';</script>";
            exit;
        } else {
            echo "<script>alert('등록 오류: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('필수 입력 항목을 모두 채워주세요.');</script>";
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
                        <td class="px-6 py-4 text-gray-600">미정</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_status_badge($staff['status']); ?>">
                                <?php echo htmlspecialchars($staff['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button class="px-4 py-1.5 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
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

<?php 
mysqli_close($conn);
include 'include/footer.php'; 
?>