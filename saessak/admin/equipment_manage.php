<?php
include 'includes/header.php';
include 'includes/db.php'; 

// 1. [INSERT] 신규 장비 폼 제출 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['equipment_name'])) {
    // 보안: 입력값 필터링 (SQL 인젝션 방지)
    $equipment_no = $conn->real_escape_string($_POST['equipment_no']);
    $equipment_name = $conn->real_escape_string($_POST['equipment_name']);
    $category = $conn->real_escape_string($_POST['category']);
    $purchase_date = $conn->real_escape_string($_POST['purchase_date']);
    $last_check_date = $conn->real_escape_string($_POST['last_check_date']);
    $status = $conn->real_escape_string($_POST['status']);
    $maintenance_memo = $conn->real_escape_string($_POST['maintenance_memo']);
    
    // medical_equipments 테이블 구조에 맞춘 INSERT 쿼리
    $sql_insert = "INSERT INTO medical_equipments 
                   (equipment_no, equipment_name, category, purchase_date, last_check_date, status, maintenance_memo) 
                   VALUES 
                   ('$equipment_no', '$equipment_name', '$category', '$purchase_date', '$last_check_date', '$status', '$maintenance_memo')";
    
    if ($conn->query($sql_insert) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF']); 
        exit();
    } else {
        echo "<script>alert('DB 저장 오류: " . $conn->error . "');</script>";
    }
}

// 2. [SELECT] DB에서 장비 목록 불러오기 (최신 등록순)
$sql_select = "SELECT * FROM medical_equipments ORDER BY created_at DESC";
$result_equip = $conn->query($sql_select);

// 뱃지 색상 함수 (폐기 상태 추가)
function get_equip_status_color($status) {
    switch ($status) {
        case '사용 가능': return 'bg-green-100 text-green-700 border-green-200';
        case '사용 중':   return 'bg-blue-100 text-blue-700 border-blue-200';
        case '수리 중':   return 'bg-red-100 text-red-700 border-red-200';
        case '폐기':      return 'bg-gray-200 text-gray-500 border-gray-300';
        default:          return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
?>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 의료 장비 등록</h2>
    <form id="equip_form" action="" method="POST">
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">관리번호 (필수)</label>
                <input type="text" id="equipment_no" name="equipment_no" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: EQ-1001">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">장비명 (모델명)</label>
                <input type="text" id="equipment_name" name="equipment_name" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: 초음파 영상 진단기">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">장비 분류</label>
                <select id="category" name="category" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">분류 선택</option>
                    <option value="영상진단기기">영상진단기기</option>
                    <option value="생체계측기기">생체계측기기</option>
                    <option value="진료용장비">진료용장비</option>
                    <option value="기타분류">기타분류</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">현재 상태</label>
                <select id="status" name="status" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="사용 가능">사용 가능</option>
                    <option value="사용 중">사용 중</option>
                    <option value="수리 중">수리 중</option>
                    <option value="폐기">폐기</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">구입 일자</label>
                <input type="date" id="purchase_date" name="purchase_date" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">최근 점검일</label>
                <input type="date" id="last_check_date" name="last_check_date" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 mb-1">수리/점검 이력 메모</label>
            <textarea id="maintenance_memo" name="maintenance_memo" rows="3"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none"
                placeholder="특이사항이나 수리 내역을 기록하세요."></textarea>
        </div>

        <button type="submit"
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition-colors">
            장비 등록
        </button>
    </form>
</div>

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
                <?php 
                if ($result_equip && $result_equip->num_rows > 0): 
                    while($equip = $result_equip->fetch_assoc()): 
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-500 font-mono text-xs"><?php echo htmlspecialchars($equip['equipment_no']); ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($equip['equipment_name']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($equip['category']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($equip['last_check_date']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_equip_status_color($equip['status']); ?>">
                            <?php echo htmlspecialchars($equip['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">수리 기록</button>
                            <button class="px-3 py-1 text-xs font-medium bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors">상태 변경</button>
                        </div>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">등록된 의료 장비가 없습니다.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>