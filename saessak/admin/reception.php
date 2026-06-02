<?php
include 'include/header.php';

$mock_reservations = [
    ["res_id" => "RS-1001", "name" => "김철수", "dept" => "내과", "date" => "2026-06-02", "time" => "10:30", "type" => "예약", "status" => "확정"],
    ["res_id" => "RS-1002", "name" => "이영희", "dept" => "이비인후과", "date" => "2026-06-02", "time" => "10:45", "type" => "현장접수", "status" => "대기중"],
    ["res_id" => "RS-1003", "name" => "박지민", "dept" => "정형외과", "date" => "2026-06-02", "time" => "11:00", "type" => "예약", "status" => "확정"],
    ["res_id" => "RS-1004", "name" => "최동훈", "dept" => "내과", "date" => "2026-06-02", "time" => "11:30", "type" => "현장접수", "status" => "취소"],
];

function get_status_color($status) {
    switch ($status) {
        case '확정':   return 'bg-green-100 text-green-700 border-green-200';
        case '대기중': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case '취소':   return 'bg-red-100 text-red-700 border-red-200';
        default:       return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}

function get_type_color($type) {
    switch ($type) {
        case '예약':     return 'bg-blue-100 text-blue-700 border-blue-200';
        case '현장접수': return 'bg-purple-100 text-purple-700 border-purple-200';
        default:         return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
?>

<!-- 신규 접수/예약 폼 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">신규 접수 / 예약 등록</h2>
    <form id="reception_form" action="" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">환자 성명</label>
                <input type="text" name="patient_name"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="예: 홍길동">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">접수 유형</label>
                <select name="res_type"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">유형 선택</option>
                    <option value="예약">예약</option>
                    <option value="현장접수">현장접수</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 진료과</label>
                <select name="dept_name"
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
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 날짜</label>
                <input type="date" name="res_date"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">희망 시간</label>
                <select name="res_time"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">시간 선택</option>
                    <option value="09:00">09:00</option>
                    <option value="09:30">09:30</option>
                    <option value="10:00">10:00</option>
                    <option value="10:30">10:30</option>
                    <option value="11:00">11:00</option>
                    <option value="11:30">11:30</option>
                    <option value="14:00">14:00</option>
                    <option value="14:30">14:30</option>
                    <option value="15:00">15:00</option>
                    <option value="15:30">15:30</option>
                    <option value="16:00">16:00</option>
                    <option value="16:30">16:30</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 mb-1">증상 메모 (선택)</label>
            <textarea name="memo" rows="3"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none"
                placeholder="증상이나 특이사항을 입력하세요."></textarea>
        </div>

        <button type="submit"
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition-colors">
            접수 등록
        </button>
    </form>
</div>

<!-- 접수/예약 현황 테이블 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">오늘 접수/예약 현황</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                    <th class="px-6 py-4 font-medium">접수번호</th>
                    <th class="px-6 py-4 font-medium">환자명</th>
                    <th class="px-6 py-4 font-medium">진료과</th>
                    <th class="px-6 py-4 font-medium">날짜</th>
                    <th class="px-6 py-4 font-medium">시간</th>
                    <th class="px-6 py-4 font-medium">유형</th>
                    <th class="px-6 py-4 font-medium">상태</th>
                    <th class="px-6 py-4 font-medium">관리</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($mock_reservations as $res): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo htmlspecialchars($res['res_id']); ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($res['name']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($res['dept']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($res['date']); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($res['time']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_type_color($res['type']); ?>">
                            <?php echo htmlspecialchars($res['type']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_status_color($res['status']); ?>">
                            <?php echo htmlspecialchars($res['status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button class="px-3 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">수정</button>
                            <button class="px-3 py-1 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition-colors">취소</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>