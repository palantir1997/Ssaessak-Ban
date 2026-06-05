<?php
// 기존에 사용하시던 헤더 인클루드 경로 그대로 유지합니다.
include 'include/header.php';

$mock_charts = [
    ["chart_id" => "CH-1001", "name" => "김철수", "age" => 45, "dept" => "내과", "doctor" => "박건우", "date" => "2026-06-02", "diagnosis" => "고혈압", "prescription" => "암로디핀 5mg", "note" => "혈압 145/90, 1개월 후 재진"],
    ["chart_id" => "CH-1002", "name" => "이영희", "age" => 32, "dept" => "이비인후과", "doctor" => "김새싹", "date" => "2026-06-02", "diagnosis" => "급성 편도염", "prescription" => "항생제 5일치", "note" => "초진, 발열 38.2도"],
    ["chart_id" => "CH-1003", "name" => "박지민", "age" => 28, "dept" => "정형외과", "doctor" => "최태양", "date" => "2026-06-01", "diagnosis" => "요추 추간판 탈출증", "prescription" => "소염진통제", "note" => "X-Ray 촬영 완료, MRI 권고"],
    ["chart_id" => "CH-1004", "name" => "최동훈", "age" => 61, "dept" => "내과", "doctor" => "박건우", "date" => "2026-06-01", "diagnosis" => "당뇨 2형", "prescription" => "메트포르민 500mg", "note" => "혈당 186, 식이요법 안내"],
];

$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$search_dept = isset($_GET['search_dept']) ? trim($_GET['search_dept']) : '';
$search_date = isset($_GET['search_date']) ? trim($_GET['search_date']) : '';

$filtered_charts = $mock_charts;
$error_message = "";

if ($search_name !== '' || $search_dept !== '' || $search_date !== '') {
    if ($search_name === '' || $search_dept === '' || $search_date === '') {
        $error_message = "환자명, 진료과, 진료일을 모두 입력하셔야 검색이 가능합니다.";
    } else {
        $filtered_charts = array_filter($mock_charts, function($chart) use ($search_name, $search_dept, $search_date) {
            $name_match = (strpos($chart['name'], $search_name) !== false);
            $dept_match = ($chart['dept'] === $search_dept);
            $date_match = ($chart['date'] === $search_date);
            return $name_match && $dept_match && $date_match;
        });
    }
}
?>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
}
</style>

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
                                <button type="button" 
                                onclick="openCertificate('<?php echo $chart['chart_id']; ?>', '<?php echo htmlspecialchars($chart['name']); ?>', '<?php echo $chart['date']; ?>', '<?php echo htmlspecialchars($chart['diagnosis']); ?>', '<?php echo htmlspecialchars($chart['prescription']); ?>', '<?php echo htmlspecialchars($chart['note']); ?>')" 
                                class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                                 상세
                                </button>
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

<div id="certificateModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-60 overflow-y-auto py-10">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden border border-gray-300 my-auto">
        
        <div class="px-6 py-4 bg-gray-800 text-white flex justify-between items-center no-print">
            <h3 class="text-lg font-bold tracking-wide">📄 진단서 상세 보기</h3>
            <button type="button" onclick="closeCertModal()" class="text-gray-400 hover:text-white text-2xl font-semibold">&times;</button>
        </div>

        <div id="printArea" class="p-10 bg-white text-black font-sans">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-extrabold tracking-[1em] text-center uppercase border-b-4 border-double border-black pb-4 inline-block w-full">진단서</h1>
            </div>

            <table class="w-full border-collapse border border-black text-sm mb-6">
                <tbody>
                    <tr>
                        <td class="border border-black bg-gray-50 px-4 py-3 font-bold text-center w-24">병명</td>
                        <td class="border border-black px-4 py-3" id="cert_diagnosis">--</td>
                        <td class="border border-black bg-gray-50 px-4 py-3 font-bold text-center w-28">진료 일자</td>
                        <td class="border border-black px-4 py-3 w-40" id="cert_date">--</td>
                    </tr>
                    <tr>
                        <td class="border border-black bg-gray-50 px-4 py-3 font-bold text-center">환자 성명</td>
                        <td class="border border-black px-4 py-3 font-bold text-base" id="cert_name">--</td>
                        <td class="border border-black bg-gray-50 px-4 py-3 font-bold text-center">차트 번호</td>
                        <td class="border border-black px-4 py-3 font-mono" id="cert_id">--</td>
                    </tr>
                </tbody>
            </table>

            <div class="border border-black p-6 min-h-[150px] mb-6">
                <h3 class="font-bold text-sm text-gray-700 mb-2">■ 처방 내역 및 조제 정보</h3>
                <p class="text-base leading-relaxed pl-2 whitespace-pre-wrap" id="cert_prescription">--</p>
            </div>

            <div class="border border-black p-6 min-h-[200px] mb-12">
                <h3 class="font-bold text-sm text-gray-700 mb-2">■ 의사 소견 및 향후 치료 의견</h3>
                <p class="text-base leading-relaxed pl-2 whitespace-pre-wrap" id="cert_note">--</p>
            </div>

            <div class="text-center space-y-2 mb-8">
                <p class="text-lg font-medium">위와 같이 진단합니다.</p>
                <p class="text-gray-600 font-mono" id="cert_today_date">2026년 06월 05일</p>
            </div>

            <div class="flex justify-end items-center gap-4 border-t border-gray-200 pt-6">
                <div class="text-right">
                    <p class="text-sm text-gray-500">의료기관 명칭 : 새싹종합병원</p>
                    <p class="text-base font-bold text-gray-800">담당의사 : 홍길동 (인)</p>
                </div>
                <div class="w-14 h-14 border border-red-400 rounded-full flex items-center justify-center text-red-500 text-xs font-bold border-dashed transform rotate-12">
                    새싹병원
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2 no-print">
            <button type="button" onclick="closeCertModal()" class="px-4 py-2 text-sm font-medium bg-white hover:bg-gray-100 text-gray-700 border border-gray-200 rounded-lg transition-colors">닫기</button>
            <button type="button" onclick="printCertificate()" class="px-4 py-2 text-sm font-medium bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-1">
                🖨️ 진단서 출력
            </button>
        </div>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="../js/chart.js"></script>

<?php 
// 기존에 쓰시던 푸터 경로 그대로 유지합니다.
include 'include/footer.php'; 
?>