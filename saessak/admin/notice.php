<?php
include 'include/header.php';

$mock_notices = [
    ["notice_id" => "NT-1001", "title" => "2026년 상반기 정기 소독 일정 안내", "author" => "김관리", "category" => "공지", "date" => "2026-06-01", "important" => true],
    ["notice_id" => "NT-1002", "title" => "현충일(6월 6일) 휴진 안내", "author" => "김관리", "category" => "휴진", "date" => "2026-05-28", "important" => true],
    ["notice_id" => "NT-1003", "title" => "정형외과 최태양 과장 신규 부임 안내", "author" => "김관리", "category" => "소식", "date" => "2026-05-20", "important" => false],
    ["notice_id" => "NT-1004", "title" => "원내 주차 공간 이용 안내", "author" => "김관리", "category" => "안내", "date" => "2026-05-15", "important" => false],
    ["notice_id" => "NT-1005", "title" => "전자 차트 시스템 업데이트 예정 안내", "author" => "김관리", "category" => "공지", "date" => "2026-05-10", "important" => false],
];

function get_category_color($category) {
    switch ($category) {
        case '공지': return 'bg-blue-100 text-blue-700 border-blue-200';
        case '휴진': return 'bg-red-100 text-red-700 border-red-200';
        case '소식': return 'bg-green-100 text-green-700 border-green-200';
        case '안내': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        default:     return 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
?>

<!-- 공지사항 등록 폼 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">공지사항 등록</h2>
    <form id="notice_form" action="" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">제목</label>
                <input type="text" name="title"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="공지사항 제목을 입력하세요">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">카테고리</label>
                <select name="category"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">카테고리 선택</option>
                    <option value="공지">공지</option>
                    <option value="휴진">휴진</option>
                    <option value="소식">소식</option>
                    <option value="안내">안내</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 mb-1">내용</label>
            <textarea name="content" rows="4"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none"
                placeholder="공지사항 내용을 입력하세요."></textarea>
        </div>

        <div class="flex items-center gap-3 mb-4">
            <input type="checkbox" id="important" name="important" class="rounded text-blue-600">
            <label for="important" class="text-sm text-gray-600 cursor-pointer">중요 공지로 설정</label>
        </div>

        <button type="submit"
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition-colors">
            공지 등록
        </button>
    </form>
</div>

<!-- 공지사항 목록 -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-200 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800">공지사항 목록</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                    <th class="px-6 py-4 font-medium">번호</th>
                    <th class="px-6 py-4 font-medium">카테고리</th>
                    <th class="px-6 py-4 font-medium">제목</th>
                    <th class="px-6 py-4 font-medium">작성자</th>
                    <th class="px-6 py-4 font-medium">작성일</th>
                    <th class="px-6 py-4 font-medium">중요</th>
                    <th class="px-6 py-4 font-medium">관리</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($mock_notices as $notice): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo htmlspecialchars($notice['notice_id']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_category_color($notice['category']); ?>">
                            <?php echo htmlspecialchars($notice['category']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-800">
                        <?php if ($notice['important']): ?>
                            <span class="text-red-500 mr-1">★</span>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($notice['title']); ?>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($notice['author']); ?></td>
                    <td class="px-6 py-4 text-gray-500"><?php echo htmlspecialchars($notice['date']); ?></td>
                    <td class="px-6 py-4">
                        <?php if ($notice['important']): ?>
                            <span class="px-3 py-1 rounded-full text-xs font-bold border bg-red-100 text-red-700 border-red-200">중요</span>
                        <?php else: ?>
                            <span class="text-gray-300 text-xs">-</span>
                        <?php endif; ?>
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