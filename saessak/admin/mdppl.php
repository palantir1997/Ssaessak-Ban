<?php
include 'include/header.php';

$mock_staff_list = [
    ["staff_id" => "DR-1001", "name" => "김새싹", "role" => "의사", "dept" => "가정의학과", "phone" => "010-1234-5678", "status" => "재직중", "join_date" => "2023-03-01"],
    ["staff_id" => "DR-1002", "name" => "박건우", "role" => "의사", "dept" => "일반내과", "phone" => "010-2345-6789", "status" => "재직중", "join_date" => "2023-05-15"],
    ["staff_id" => "DR-1003", "name" => "최태양", "role" => "의사", "dept" => "정형외과", "phone" => "010-3456-7890", "status" => "재직중", "join_date" => "2026-01-10"],
    ["staff_id" => "DR-1004", "name" => "이지민", "role" => "의사", "dept" => "소아청소년과", "phone" => "010-4567-8901", "status" => "재직중", "join_date" => "2024-02-20"],
    ["staff_id" => "NR-2001", "name" => "한소희", "role" => "간호사", "dept" => "종합건진센터", "phone" => "010-5678-9012", "status" => "재직중", "join_date" => "2022-07-01"],
    ["staff_id" => "NR-2002", "name" => "정다은", "role" => "간호사", "dept" => "일반내과", "phone" => "010-6789-0123", "status" => "휴직중", "join_date" => "2023-09-01"],
];

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
        case '퇴직':   return 'bg-red-100 text-red-700 border-red-200';
        default:       return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
?>

<!-- 신규 의료진 등록 폼 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 의료진 등록</h2>
    <form id="staff_form" action="" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">성명</label>
                <input type="text" id="staff_name" name="staff_name"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: 홍길동">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">직책</label>
                <select id="staff_role" name="staff_role"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">직책 선택</option>
                    <option value="의사">의사</option>
                    <option value="간호사">간호사</option>
                    <option value="행정직">행정직</option>
                    <option value="기타">기타</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">담당 진료과</label>
                <select id="dept_name" name="dept_name"
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
                <input type="text" id="staff_phone" name="staff_phone"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="010-0000-0000">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">입사일</label>
                <input type="date" id="join_date" name="join_date"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
        </div>

        <button type="submit"
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition-colors">
            의료진 등록
        </button>
    </form>
</div>

<!-- 의료진 현황 테이블 -->
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
                <?php foreach ($mock_staff_list as $staff): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo htmlspecialchars($staff['staff_id']); ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($staff['name']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_role_color($staff['role']); ?>">
                            <?php echo htmlspecialchars($staff['role']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($staff['dept']); ?></td>
                    <td class="px-6 py-4 text-gray-600 font-mono text-xs"><?php echo htmlspecialchars($staff['phone']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($staff['join_date']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_status_color($staff['status']); ?>">
                            <?php echo htmlspecialchars($staff['status']); ?>
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