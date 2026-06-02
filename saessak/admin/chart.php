<?php
include 'include/header.php';

$mock_charts = [
    ["chart_id" => "CH-1001", "name" => "김철수", "age" => 45, "dept" => "내과", "doctor" => "박건우", "date" => "2026-06-02", "diagnosis" => "고혈압", "prescription" => "암로디핀 5mg", "note" => "혈압 145/90, 1개월 후 재진"],
    ["chart_id" => "CH-1002", "name" => "이영희", "age" => 32, "dept" => "이비인후과", "doctor" => "김새싹", "date" => "2026-06-02", "diagnosis" => "급성 편도염", "prescription" => "항생제 5일치", "note" => "초진, 발열 38.2도"],
    ["chart_id" => "CH-1003", "name" => "박지민", "age" => 28, "dept" => "정형외과", "doctor" => "최태양", "date" => "2026-06-01", "diagnosis" => "요추 추간판 탈출증", "prescription" => "소염진통제", "note" => "X-Ray 촬영 완료, MRI 권고"],
    ["chart_id" => "CH-1004", "name" => "최동훈", "age" => 61, "dept" => "내과", "doctor" => "박건우", "date" => "2026-06-01", "diagnosis" => "당뇨 2형", "prescription" => "메트포르민 500mg", "note" => "혈당 186, 식이요법 안내"],
];
?>

<!-- 진료 기록 검색 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">진료 기록 검색</h2>
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">환자명</label>
            <input type="text" placeholder="환자명 입력"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">진료과</label>
            <select class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                <option value="">전체</option>
                <option value="가정의학과">가정의학과</option>
                <option value="일반내과">일반내과</option>
                <option value="정형외과">정형외과</option>
                <option value="소아청소년과">소아청소년과</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">진료일</label>
            <input type="date"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
        </div>
        <div class="flex items-end">
            <button class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition-colors">
                검색
            </button>
        </div>
    </div>
</div>

<!-- 진료기록 테이블 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">진료 기록 / 차트</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                    <th class="px-6 py-4 font-medium">차트번호</th>
                    <th class="px-6 py-4 font-medium">환자명</th>
                    <th class="px-6 py-4 font-medium">나이</th>
                    <th class="px-6 py-4 font-medium">진료과</th>
                    <th class="px-6 py-4 font-medium">담당의</th>
                    <th class="px-6 py-4 font-medium">진료일</th>
                    <th class="px-6 py-4 font-medium">진단명</th>
                    <th class="px-6 py-4 font-medium">처방</th>
                    <th class="px-6 py-4 font-medium">비고</th>
                    <th class="px-6 py-4 font-medium">관리</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($mock_charts as $chart): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo htmlspecialchars($chart['chart_id']); ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($chart['name']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($chart['age']); ?>세</td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($chart['dept']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($chart['doctor']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($chart['date']); ?></td>
                    <td class="px-6 py-4 font-medium text-gray-800"><?php echo htmlspecialchars($chart['diagnosis']); ?></td>
                    <td class="px-6 py-4 text-gray-600 text-xs"><?php echo htmlspecialchars($chart['prescription']); ?></td>
                    <td class="px-6 py-4 text-gray-500 text-xs max-w-xs truncate"><?php echo htmlspecialchars($chart['note']); ?></td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">상세</button>
                            <button class="px-3 py-1 text-xs font-medium bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors">수정</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>