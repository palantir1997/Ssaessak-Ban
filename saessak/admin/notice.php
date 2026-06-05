<?php
// DB 연결 (이미 설정된 파일이 있다면 include 또는 require 사용)
// require_once 'includes/db.php';

// 데이터 조회 로직 (예시)
// $query = "SELECT * FROM notices ORDER BY id DESC";
// $result = mysqli_query($conn, $query);
// $notices = mysqli_fetch_all($result, MYSQLI_ASSOC);

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

<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">공지사항 등록</h2>
    <form id="notice_form" action="notice.php" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">제목</label>
                <input type="text" name="title" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg"
                    placeholder="공지사항 제목을 입력하세요">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">카테고리</label>
                <select name="category" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg">
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
            <textarea name="content" rows="4" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg"></textarea>
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition">
            등록하기
        </button>
    </form>
</div>
