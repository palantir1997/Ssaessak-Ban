<?php
include 'include/header.php';

$mock_charts = [
    ["chart_id" => "CH-1001", "name" => "김철수", "age" => 45, "dept" => "내과", "doctor" => "박건우", "date" => "2026-06-02", "diagnosis" => "고혈압", "prescription" => "암로디핀 5mg", "note" => "혈압 145/90, 1개월 후 재진"],
    ["chart_id" => "CH-1002", "name" => "이영희", "age" => 32, "dept" => "이비인후과", "doctor" => "김새싹", "date" => "2026-06-02", "diagnosis" => "급성 편도염", "prescription" => "항생제 5일치", "note" => "초진, 발열 38.2도"],
    ["chart_id" => "CH-1003", "name" => "박지민", "age" => 28, "dept" => "정형외과", "doctor" => "최태양", "date" => "2026-06-01", "diagnosis" => "요추 추간판 탈출증", "prescription" => "소염진통제", "note" => "X-Ray 촬영 완료, MRI 권고"],
    ["chart_id" => "CH-1004", "name" => "최동훈", "age" => 61, "dept" => "내과", "doctor" => "박건우", "date" => "2026-06-01", "diagnosis" => "당뇨 2형", "prescription" => "메트포르민 500mg", "note" => "혈당 186, 식이요법 안내"],
];

// --- [검색 및 필터링 로직 추가] ---
// 사용자가 GET 방식으로 전달한 검색 키워드를 받아옵니다. (양끝 공백 제거)
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$search_dept = isset($_GET['search_dept']) ? trim($_GET['search_dept']) : '';
$search_date = isset($_GET['search_date']) ? trim($_GET['search_date']) : '';

// 테이블에 최종적으로 표시할 결과 배열 (기본값은 전체 데이터)
$filtered_charts = $mock_charts;
$error_message = "";

// 검색 버튼이 눌렸거나, 변수 중 하나라도 값이 존재하는 경우 (검색 시도)
if ($search_name !== '' || $search_dept !== '' || $search_date !== '') {
    
    // 조건: 세 가지 입력칸이 '모두' 필수입니다. 하나라도 비어있으면 경고를 띄웁니다.
    if ($search_name === '' || $search_dept === '' || $search_date === '') {
        $error_message = "환자명, 진료과, 진료일을 모두 입력하셔야 검색이 가능합니다.";
        // 필수 조건 미달 시 빈 목록을 보여주고 싶다면 아래 주석을 해제하세요.
        // $filtered_charts = []; 
    } else {
        // 세 칸이 모두 입력되었을 때만 필터링 수행
        $filtered_charts = array_filter($mock_charts, function($chart) use ($search_name, $search_dept, $search_date) {
            // 환자명 포함 여부 (대소문자 구분 없이 비교하기 위해 가공 가능, 여기선 단순 포함 검사)
            $name_match = (strpos($chart['name'], $search_name) !== false);
            // 진료과 일치 여부
            $dept_match = ($chart['dept'] === $search_dept);
            // 진료일 일치 여부
            $date_match = ($chart['date'] === $search_date);
            
            // 세 조건이 모두 충족(AND)되어야 true를 반환하여 결과에 포함시킵니다.
            return $name_match && $dept_match && $date_match;
        });
    }
}
// ----------------------------------
?>

<?php if (!empty($error_message)): ?>
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-lg">
    <?php echo htmlspecialchars($error_message); ?>
</div>
<?php endif; ?>

<form method="GET" action="chart.php" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">진료 기록 검색</h2>
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">환자명 <span class="text-red-500">*</span></label>
            <input type="text" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>" placeholder="환자명 입력" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">진료과 <span class="text-red-500">*</span></label>
            <select name="search_dept" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                <option value="">선택해주세요</option>
                <option value="내과" <?php echo $search_dept === '내과' ? 'selected' : ''; ?>>내과</option>
                <option value="이비인후과" <?php echo $search_dept === '이비인후과' ? 'selected' : ''; ?>>이비인후과</option>
                <option value="정형외과" <?php echo $search_dept === '정형외과' ? 'selected' : ''; ?>>정형외과</option>
                <option value="가정의학과" <?php echo $search_dept === '가정의학과' ? 'selected' : ''; ?>>가정의학과</option>
                <option value="소아청소년과" <?php echo $search_dept === '소아청소년과' ? 'selected' : ''; ?>>소아청소년과</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">진료일 <span class="text-red-500">*</span></label>
            <input type="date" name="search_date" value="<?php echo htmlspecialchars($search_date); ?>" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition-colors">
                검색
            </button>
        </div>
    </div>
</form>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">진료 기록 / 차트</h2>
            <?php if ($search_name !== '' || $search_dept !== '' || $search_date !== ''): ?>
                <a href="chart.php" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-1.5 rounded-lg font-medium transition-colors">전체 목록 보기</a>
            <?php endif; ?>
        </div>
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
                <?php if (count($filtered_charts) > 0): ?>
                    <?php foreach ($filtered_charts as $chart): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo htmlspecialchars($chart['chart_id']); ?></td>
                        <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($chart['name']); ?></td>
                        <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($chart['age']); ?></td>
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
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center text-gray-400">
                            일치하는 진료 기록이 없습니다.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>