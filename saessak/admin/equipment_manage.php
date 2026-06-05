<?php
// 1. 헤더 불러오기 (경로는 파일 구조에 맞게 유지)
include './include/header.php';
?>

<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/patient.css">

<?php

// 2. 이 페이지 전용 DB 연결 (방금 만든 우분투 계정 사용!)
$db_host = '175.210.161.42';
$db_user = 'DH';
$db_pass = '1234';
$db_name = 'saessak';

// mysqli 연결 생성 (변수명을 $conn으로 고정)
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// 연결 에러 시 화면에 크게 표시
if (!$conn) {
    echo "<div style='background-color:#fee2e2; color:#991b1b; padding:15px; margin-bottom:20px; border-radius:8px;'>";
    echo "<strong>[DB 연결 실패]</strong> 우분투 DB 계정 설정을 다시 확인해주세요.<br>에러 내용: " . mysqli_connect_error();
    echo "</div>";
} else {
    // 연결 성공 시 한글 깨짐 방지 설정
    mysqli_set_charset($conn, 'utf8mb4');


    // 3-1. [UPDATE] 상태 변경 처리
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_status'])) {
        $equipment_no = mysqli_real_escape_string($conn, $_POST['equipment_no']);
        $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);

        $sql_update_status = "UPDATE medical_equipments 
                              SET status = '$new_status' 
                              WHERE equipment_no = '$equipment_no'";

        if (mysqli_query($conn, $sql_update_status)) {
            echo "<script>alert('상태가 변경되었습니다.'); location.href='equipment_manage.php';</script>";
            exit();
        } else {
            echo "<script>alert('상태 변경 오류: " . mysqli_error($conn) . "');</script>";
        }
    }

    // 3-2. [UPDATE] 수리 기록 처리
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_repair_log'])) {
        $equipment_no = mysqli_real_escape_string($conn, $_POST['equipment_no']);
        $repair_memo = mysqli_real_escape_string($conn, $_POST['repair_memo']);
        $today = date('Y-m-d');

        $sql_update_repair = "UPDATE medical_equipments 
                              SET maintenance_memo = CONCAT(IFNULL(maintenance_memo, ''), '\n[$today] ', '$repair_memo'),
                                  last_check_date = '$today',
                                  status = '수리 중'
                              WHERE equipment_no = '$equipment_no'";

        if (mysqli_query($conn, $sql_update_repair)) {
            echo "<script>alert('수리 기록이 추가되었습니다.'); location.href='equipment_manage.php';</script>";
            exit();
        } else {
            echo "<script>alert('수리 기록 오류: " . mysqli_error($conn) . "');</script>";
        }
    }

    // 3-3. [UPDATE] 수리 기록 개별 삭제 처리
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_repair_log'])) {
        $equipment_no = mysqli_real_escape_string($conn, $_POST['equipment_no']);
        $repair_index = isset($_POST['repair_index']) ? intval($_POST['repair_index']) : -1;

        $sql_get_memo = "SELECT maintenance_memo FROM medical_equipments WHERE equipment_no = '$equipment_no' LIMIT 1";
        $result_memo = mysqli_query($conn, $sql_get_memo);

        if ($result_memo && mysqli_num_rows($result_memo) > 0) {
            $memo_row = mysqli_fetch_assoc($result_memo);
            $memo_lines = preg_split("/\r\n|\r|\n/", $memo_row['maintenance_memo'] ?? '');

            $memo_lines = array_values(array_filter($memo_lines, function($line) {
                return trim($line) !== '';
            }));

            if ($repair_index >= 0 && isset($memo_lines[$repair_index])) {
                unset($memo_lines[$repair_index]);
                $new_memo = implode("\n", array_values($memo_lines));
                $new_memo_escaped = mysqli_real_escape_string($conn, $new_memo);

                $sql_delete_repair = "UPDATE medical_equipments 
                                      SET maintenance_memo = '$new_memo_escaped'
                                      WHERE equipment_no = '$equipment_no'";

                if (mysqli_query($conn, $sql_delete_repair)) {
                    echo "<script>alert('수리 기록이 삭제되었습니다.'); location.href='equipment_manage.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('수리 기록 삭제 오류: " . mysqli_error($conn) . "');</script>";
                }
            } else {
                echo "<script>alert('삭제할 수리 기록을 찾을 수 없습니다.');</script>";
            }
        } else {
            echo "<script>alert('장비 정보를 찾을 수 없습니다.');</script>";
        }
    }

   // 3. [INSERT] 신규 장비 폼 제출 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['equipment_name'])) {
    $equipment_no = mysqli_real_escape_string($conn, $_POST['equipment_no']);
    

    // ⭐ 여기 추가: 중복 체크!
    $check_query = "SELECT * FROM medical_equipments WHERE equipment_no = '$equipment_no'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('⚠️ 이미 등록된 관리번호입니다!\\n다른 번호를 입력하세요.');</script>";
    } else {
        // 중복 아니면 계속 진행
        $equipment_name = mysqli_real_escape_string($conn, $_POST['equipment_name']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $purchase_date = mysqli_real_escape_string($conn, $_POST['purchase_date']);
        $last_check_date = mysqli_real_escape_string($conn, $_POST['last_check_date']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $maintenance_memo = mysqli_real_escape_string($conn, $_POST['maintenance_memo']);
        
        $sql_insert = "INSERT INTO medical_equipments 
                       (equipment_no, equipment_name, category, purchase_date, last_check_date, status, maintenance_memo) 
                       VALUES 
                       ('$equipment_no', '$equipment_name', '$category', '$purchase_date', '$last_check_date', '$status', '$maintenance_memo')";
        
        if (mysqli_query($conn, $sql_insert)) {
            echo "<script>location.href='equipment_manage.php';</script>";
            exit();
        } else {
            echo "<script>alert('DB 저장 오류: " . mysqli_error($conn) . "');</script>";
        }
    }
}

    // 4. [SELECT] DB에서 장비 목록 불러오기
    $sql_select = "SELECT equipment_no,
                          equipment_name,
                          category,
                          purchase_date,
                          last_check_date,
                          status,
                          maintenance_memo,
                          created_at
                   FROM medical_equipments
                   ORDER BY created_at DESC";
    $result_equip = mysqli_query($conn, $sql_select);
}

// 뱃지 색상 함수
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
                if (isset($result_equip) && $result_equip && mysqli_num_rows($result_equip) > 0): 
                    while($equip = mysqli_fetch_assoc($result_equip)): 
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
                            <button type="button"
                                onclick='openRepairModal(<?php echo json_encode($equip["equipment_no"], JSON_UNESCAPED_UNICODE); ?>, <?php echo json_encode($equip["maintenance_memo"] ?? "", JSON_UNESCAPED_UNICODE); ?>)'
                                class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                                수리 기록
                            </button>
                            <button type="button"
                                onclick='openStatusModal(<?php echo json_encode($equip["equipment_no"], JSON_UNESCAPED_UNICODE); ?>, <?php echo json_encode($equip["status"], JSON_UNESCAPED_UNICODE); ?>)'
                                class="px-3 py-1 text-xs font-medium bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors">
                                상태 변경
                            </button>
                        </div>
                    </td>
                </tr>
                <?php if (isset($equip['maintenance_memo']) && trim($equip['maintenance_memo']) !== ''): ?>
                <tr style="background:#f9fafb;">
                    <td colspan="6" class="px-6 py-4">
                        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:10px; padding:14px;">
                            <p class="text-xs font-bold text-gray-500 mb-2">수리/점검 기록</p>

                            <?php
                            $repair_lines = preg_split("/\r\n|\r|\n/", $equip['maintenance_memo']);
                            $repair_lines = array_values(array_filter($repair_lines, function($line) {
                                return trim($line) !== '';
                            }));
                            ?>

                            <div class="space-y-2">
                                <?php foreach ($repair_lines as $repair_index => $repair_line): ?>
                                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:10px 12px;">
                                    <div style="white-space:pre-wrap; word-break:break-word; line-height:1.7; color:#374151; font-size:13px;">
                                        <?php echo htmlspecialchars($repair_line); ?>
                                    </div>

                                    <form method="POST" onsubmit="return confirm('이 수리 기록을 삭제할까요?');" style="margin:0;">
                                        <input type="hidden" name="equipment_no" value="<?php echo htmlspecialchars($equip['equipment_no']); ?>">
                                        <input type="hidden" name="repair_index" value="<?php echo $repair_index; ?>">
                                        <button type="submit" name="delete_repair_log"
                                            style="color:#ef4444; font-weight:bold; font-size:14px; line-height:1; padding:4px 6px; border-radius:6px;"
                                            title="수리 기록 삭제">
                                            ×
                                        </button>
                                    </form>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
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


<!-- 수리 기록 모달 -->
<div id="repairModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">수리 기록 추가</h3>
        <form method="POST">
            <input type="hidden" id="repair_equipment_no" name="equipment_no">

            <div class="mb-4">
                <p class="text-xs font-bold text-gray-500 mb-1">기존 수리/점검 기록</p>
                <div id="repair_history_view"
                    class="min-h-[80px] max-h-48 overflow-y-auto whitespace-pre-wrap break-words bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm text-gray-700">
                    등록된 수리 기록이 없습니다.
                </div>
            </div>

            <p class="text-xs font-bold text-gray-500 mb-1">새 수리/점검 기록 추가</p>
            <textarea name="repair_memo" required rows="4"
                class="w-full border border-gray-200 rounded-lg p-3 text-sm"
                placeholder="추가할 수리/점검 내용을 입력하세요."></textarea>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeRepairModal()"
                    class="px-4 py-2 bg-gray-100 rounded-lg text-sm">취소</button>
                <button type="submit" name="add_repair_log"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">저장</button>
            </div>
        </form>
    </div>
</div>

<!-- 상태 변경 모달 -->
<div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">장비 상태 변경</h3>
        <form method="POST">
            <input type="hidden" id="status_equipment_no" name="equipment_no">

            <select id="new_status" name="new_status"
                class="w-full border border-gray-200 rounded-lg p-3 text-sm">
                <option value="사용 가능">사용 가능</option>
                <option value="사용 중">사용 중</option>
                <option value="수리 중">수리 중</option>
                <option value="폐기">폐기</option>
            </select>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeStatusModal()"
                    class="px-4 py-2 bg-gray-100 rounded-lg text-sm">취소</button>
                <button type="submit" name="change_status"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">변경</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRepairModal(equipmentNo, currentMemo) {
    document.getElementById('repair_equipment_no').value = equipmentNo;

    const historyBox = document.getElementById('repair_history_view');
    if (currentMemo && currentMemo.trim() !== '') {
        historyBox.textContent = currentMemo;
    } else {
        historyBox.textContent = '등록된 수리 기록이 없습니다.';
    }

    document.getElementById('repairModal').classList.remove('hidden');
}

function closeRepairModal() {
    document.getElementById('repairModal').classList.add('hidden');
}

function openStatusModal(equipmentNo, currentStatus) {
    document.getElementById('status_equipment_no').value = equipmentNo;
    document.getElementById('new_status').value = currentStatus;
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}
</script>

<?php // include './include/footer.php'; 
?>
