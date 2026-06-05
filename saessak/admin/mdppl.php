<?php
include 'include/header.php'; 

$db_host = '172.16.11.222'; 
$db_user = 'root'; 
$db_pass = '';         
$db_name = 'saessak';      

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    echo "<div style='background-color:#fee2e2; color:#991b1b; padding:15px; margin-bottom:20px;'>";
    echo "<strong>[DB 연결 실패]</strong> 에러 내용: " . mysqli_connect_error();
    echo "</div>";
} else {
    mysqli_set_charset($conn, 'utf8mb4');

    // 신규 의료진 등록 처리
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['staff_name'])) {
        $name      = mysqli_real_escape_string($conn, $_POST['staff_name']);
        $position  = mysqli_real_escape_string($conn, $_POST['staff_role']);
        $dept_name = mysqli_real_escape_string($conn, $_POST['dept_name']);
        $phone     = mysqli_real_escape_string($conn, $_POST['staff_phone']);
        $hire_date = mysqli_real_escape_string($conn, $_POST['join_date']);
        
        // staff_no 자동 생성
        $rand_no = 'DR-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $status = '재직중';

        $sql_insert = "INSERT INTO medical_staffs (staff_no, name, position, dept_name, phone, hire_date, status) 
                       VALUES ('$rand_no', '$name', '$position', '$dept_name', '$phone', '$hire_date', '$status')";

        if (mysqli_query($conn, $sql_insert)) {
            echo "<script>alert('신규 의료진이 등록되었습니다.'); location.href='mdppl.php';</script>";
            exit();
        } else {
            echo "<script>alert('등록 오류: " . mysqli_error($conn) . "');</script>";
        }
    }

    // 의료진 목록 조회
    $sql_select = "SELECT * FROM medical_staffs ORDER BY hire_date DESC";
    $result_staff = mysqli_query($conn, $sql_select);
}

function get_role_color($role) {
    switch ($role) {
        case '의사':   return 'bg-blue-100 text-blue-700 border-blue-200';
        case '간호사': return 'bg-purple-100 text-purple-700 border-purple-200';
        default:       return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}

function get_status_color($status) {
    switch ($status) {
        case '재직중': return 'bg-green-100 text-green-700 border-green-200';
        case '휴직중': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case '퇴사':   return 'bg-red-100 text-red-700 border-red-200';
        default:       return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
?>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 의료진 등록</h2>
    <form action="" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">성명</label>
                <input type="text" name="staff_name" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: 홍길동">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">직책</label>
                <select name="staff_role" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">직책 선택</option>
                    <option value="의사">의사</option>
                    <option value="간호사">간호사</option>
                    <option value="행정직">행정직</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">담당 진료과</label>
                <select name="dept_name" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">진료과 선택</option>
                    <option value="가정의학과">가정의학과</option>
                    <option value="일반내과">일반내과</option>
                    <option value="정형외과">정형외과</option>
                    <option value="소아청소년과">소아청소년과</option>
                    <option value="종합건진센터">종합건진센터</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">연락처</label>
                <input type="text" name="staff_phone" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="010-0000-0000">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">입사일</label>
                <input type="date" name="join_date" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
        </div>

        <button type="submit"
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition-colors">
            의료진 등록
        </button>
    </form>
</div>

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
                <?php 
                if (isset($conn) && $conn && isset($result_staff) && mysqli_num_rows($result_staff) > 0):
                    while ($staff = mysqli_fetch_assoc($result_staff)): 
                ?>
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
                            <button class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">수정</button>
                            <button class="px-3 py-1 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-700 rounded-lg">삭제</button>
                        </div>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        <?= (isset($conn) && $conn) ? '등록된 의료진이 없습니다.' : 'DB 연결 문제로 데이터를 불러올 수 없습니다.' ?>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php';  ?>