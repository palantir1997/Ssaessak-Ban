<?php
include 'include/header.php';

$mock_accounts = [
    ["acc_id" => "AC-1001", "user_id" => "admin", "name" => "김관리", "role" => "admin", "dept" => "관리부", "last_login" => "2026-06-02 09:15", "status" => "활성"],
    ["acc_id" => "AC-1002", "user_id" => "dr_park", "name" => "박건우", "role" => "의사", "dept" => "일반내과", "last_login" => "2026-06-02 08:50", "status" => "활성"],
    ["acc_id" => "AC-1003", "user_id" => "dr_choi", "name" => "최태양", "role" => "의사", "dept" => "정형외과", "last_login" => "2026-06-01 17:30", "status" => "활성"],
    ["acc_id" => "AC-1004", "user_id" => "nr_han", "name" => "한소희", "role" => "간호사", "dept" => "종합건진센터", "last_login" => "2026-06-02 09:00", "status" => "활성"],
    ["acc_id" => "AC-1005", "user_id" => "nr_jung", "name" => "정다은", "role" => "간호사", "dept" => "일반내과", "last_login" => "2026-05-20 14:00", "status" => "비활성"],
];

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

<!-- 신규 계정 등록 폼 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 직원 계정 등록</h2>
    <form id="account_form" action="" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">성명</label>
                <input type="text" name="name"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: 홍길동">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">아이디</label>
                <input type="text" name="user_id"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: dr_hong">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">비밀번호</label>
                <input type="password" name="password"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="8자 이상">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">권한</label>
                <select name="role"
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
                <select name="dept"
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

<!-- 직원 계정 목록 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">직원 계정 목록</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                    <th class="px-6 py-4 font-medium">계정번호</th>
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
                <?php foreach ($mock_accounts as $acc): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo htmlspecialchars($acc['acc_id']); ?></td>
                    <td class="px-6 py-4 font-mono text-sm text-gray-700"><?php echo htmlspecialchars($acc['user_id']); ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($acc['name']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_role_color($acc['role']); ?>">
                            <?php echo htmlspecialchars($acc['role']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($acc['dept']); ?></td>
                    <td class="px-6 py-4 text-gray-500 text-xs"><?php echo htmlspecialchars($acc['last_login']); ?></td>
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
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>