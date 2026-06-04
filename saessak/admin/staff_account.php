<?php
// 1. 헤더 불러오기 (경로 주의)
include '../include/header.php';

// ==========================================
// 2. [독립형 DB 연결] db.php를 거치지 않고 직접 연결
// ==========================================
$db_host = '192.168.0.53'; // 타임아웃 방지를 위해 localhost 대신 IP 사용
$db_user = 'saessak_user'; // 우분투에서 만든 계정
$db_pass = '1234';         // 비밀번호
$db_name = 'saessak';      // 데이터베이스 이름

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// 연결 에러가 나면 화면 상단에 경고창 띄우기
if (!$conn) {
    echo "<div style='background-color:#fee2e2; color:#991b1b; padding:15px; margin-bottom:20px; border-radius:8px; border:1px solid #f87171;'>";
    echo "<strong>[DB 연결 실패]</strong> 우분투 서버의 DB 설정(아이디, 비번, 권한)을 다시 확인해주세요.<br>";
    echo "에러 내용: " . mysqli_connect_error();
    echo "</div>";
} else {
    // 한글 깨짐 방지
    mysqli_set_charset($conn, 'utf8mb4');

    // 3. [INSERT] 신규 직원 계정 등록 처리
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['user_id'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
        
        // 비밀번호는 안전하게 해시(Hash) 암호화하여 저장
        $raw_password = $_POST['password'];
        $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);
        
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        $dept = mysqli_real_escape_string($conn, $_POST['dept']);
        $status = '활성'; // 가입 시 기본 상태

        $sql_insert = "INSERT INTO staff_accounts (user_id, password, name, role, dept, status) 
                       VALUES ('$user_id', '$hashed_password', '$name', '$role', '$dept', '$status')";

        if (mysqli_query($conn, $sql_insert)) {
            echo "<script>alert('신규 직원이 등록되었습니다.'); location.href='staff_account.php';</script>";
            exit();
        } else {
            echo "<script>alert('계정 등록 오류: " . mysqli_error($conn) . "');</script>";
        }
    }

    // 4. [SELECT] DB에서 직원 계정 목록 불러오기
    $sql_select = "SELECT * FROM staff_accounts ORDER BY created_at DESC";
    $result_accounts = mysqli_query($conn, $sql_select);
}
// ==========================================

// 뱃지 색상 함수
function get_role_color($role) {
    switch ($role) {
        case 'admin':  return 'bg-red-100 text-red-700 border-red-200';
        case '의사':   return 'bg-blue-100 text-blue-700 border-blue-200';
        case '간호사': return 'bg-purple-100 text-purple-700 border-purple-200';
        default:       return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}

function get_status_color($status) {
    return $status === '활성'
        ? 'bg-green-100 text-green-700 border-green-200'
        : 'bg-gray-100 text-gray-500 border-gray-200';
}
?>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 직원 계정 등록</h2>
    <form id="account_form" action="" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">성명</label>
                <input type="text" name="name" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: 홍길동">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">아이디</label>
                <input type="text" name="user_id" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: dr_hong">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">비밀번호</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="8자 이상">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">권한</label>
                <select name="role" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">권한 선택</option>
                    <option value="admin">admin</option>
                    <option value="의사">의사</option>
                    <option value="간호사">간호사</option>
                    <option value="행정직">행정직</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">담당 부서</label>
                <select name="dept" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">부서 선택</option>
                    <option value="관리부">관리부</option>
                    <option value="가정의학과">가정의학과</option>
                    <option value="일반내과">일반내과</option>
                    <option value="정형외과">정형외과</option>
                    <option value="소아청소년과">소아청소년과</option>
                    <option value="종합건진센터">종합건진센터</option>
                </select>
            </div>
        </div>

        <button type="submit"
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition-colors">
            계정 등록
        </button>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">직원 계정 목록</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                    <th class="px-6 py-4 font-medium">고유번호</th>
                    <th class="px-6 py-4 font-medium">아이디</th>
                    <th class="px-6 py-4 font-medium">성명</th>
                    <th class="px-6 py-4 font-medium">권한</th>
                    <th class="px-6 py-4 font-medium">부서</th>
                    <th class="px-6 py-4 font-medium">마지막 로그인</th>
                    <th class="px-6 py-4 font-medium">상태</th>
                    <th class="px-6 py-4 font-medium">관리</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php 
                // 연결이 성공했고($conn), 쿼리 결과가 존재할 때만 출력합니다.
                if (isset($conn) && $conn && isset($result_accounts) && mysqli_num_rows($result_accounts) > 0):
                    while ($acc = mysqli_fetch_assoc($result_accounts)): 
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo htmlspecialchars($acc['id'] ?? '-'); ?></td>
                    <td class="px-6 py-4 font-mono text-sm text-gray-700"><?php echo htmlspecialchars($acc['user_id']); ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($acc['name']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_role_color($acc['role']); ?>">
                            <?php echo htmlspecialchars($acc['role']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($acc['dept']); ?></td>
                    <td class="px-6 py-4 text-gray-500 text-xs"><?php echo htmlspecialchars($acc['last_login'] ?? '기록 없음'); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_status_color($acc['status']); ?>">
                            <?php echo htmlspecialchars($acc['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">수정</button>
                            <button class="px-3 py-1 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors">삭제</button>
                        </div>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        <?php echo (isset($conn) && $conn) ? '등록된 직원 계정이 없습니다.' : 'DB 연결 문제로 데이터를 불러올 수 없습니다.'; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../include/footer.php'; ?>