<?php
include 'includes/header.php';
include 'includes/db.php';

$mock_equip_list = [
    ["equip_id" => "EQ-1001", "equip_name" => "MRI 스캐너 3.0T", "category" => "영상진단기기", "purchase_date" => "2023-05-12", "current_status" => "사용 가능", "last_inspection" => "2026-05-20"],
    ["equip_id" => "EQ-1002", "equip_name" => "이동형 X-Ray", "category" => "영상진단기기", "purchase_date" => "2024-01-15", "current_status" => "사용 중", "last_inspection" => "2026-06-01"],
    ["equip_id" => "EQ-2001", "equip_name" => "심전도 측정기", "category" => "생체계측기기", "purchase_date" => "2022-11-30", "current_status" => "사용 가능", "last_inspection" => "2026-04-10"],
    ["equip_id" => "EQ-3005", "equip_name" => "위내시경 장비", "category" => "진료용장비", "purchase_date" => "2025-02-10", "current_status" => "수리 중", "last_inspection" => "2026-05-28"],
];

function get_equip_status_color($status) {
    switch ($status) {
        case '사용 가능': return 'bg-green-100 text-green-700 border-green-200';
        case '사용 중':   return 'bg-blue-100 text-blue-700 border-blue-200';
        case '수리 중':   return 'bg-red-100 text-red-700 border-red-200';
        default:          return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
?>

<!-- 신규 장비 등록 폼 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 의료 장비 등록</h2>
    <form id="equip_form" action="" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">장비명 (모델명)</label>
                <input type="text" id="equip_name" name="equip_name"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: 초음파 영상 진단기">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">장비 분류</label>
                <select id="category" name="category"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">분류 선택</option>
                    <option value="영상진단기기">영상진단기기 (X-Ray, MRI 등)</option>
                    <option value="생체계측기기">생체계측기기 (혈압, 심전도 등)</option>
                    <option value="진료용장비">진료/수술용 장비</option>
                    <option value="기타소모품">기타 의료 비품</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">구입 일자</label>
                <input type="date" id="purchase_date" name="purchase_date"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">현재 상태</label>
                <select id="current_status" name="current_status"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="사용 가능">사용 가능</option>
                    <option value="사용 중">사용 중</option>
                    <option value="수리 중">점검/수리 중</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 mb-1">수리/점검 이력 메모</label>
            <textarea id="repair_history" name="repair_history" rows="3"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none"
                placeholder="특이사항이나 수리 내역을 기록하세요."></textarea>
        </div>

        <button type="submit"
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition-colors">
            장비 등록
        </button>
    </form>
</div>

<!-- 보유 장비 현황 테이블 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">보유 장비 현황</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                    <th class="px-6 py-4 font-medium">관리번호</th>
                    <th class="px-6 py-4 font-medium">장비명</th>
                    <th class="px-6 py-4 font-medium">분류</th>
                    <th class="px-6 py-4 font-medium">최근 점검일</th>
                    <th class="px-6 py-4 font-medium">상태</th>
                    <th class="px-6 py-4 font-medium">관리</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($mock_equip_list as $equip): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo htmlspecialchars($equip['equip_id']); ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($equip['equip_name']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($equip['category']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($equip['last_inspection']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_equip_status_color($equip['current_status']); ?>">
                            <?php echo htmlspecialchars($equip['current_status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">수리 기록</button>
                            <button class="px-3 py-1 text-xs font-medium bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors">상태 변경</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>