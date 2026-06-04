<?php
include 'include/header.php';
include 'include/db.php'; // DB 연결

// 1. 공지사항 등록 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $important = isset($_POST['important']) ? 1 : 0;

    $insert_sql = "INSERT INTO notices (title, category, content, important) VALUES ('$title', '$category', '$content', '$important')";
    mysqli_query($conn, $insert_sql);
    
    echo "<script>location.replace('notice.php');</script>";
    exit;
}

// 2. 공지사항 목록 조회
$sql = "SELECT * FROM notices ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$notices = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                    placeholder="공지사항 제목을 입력하세요">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">카테고리</label>
                <select name="category" required
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
            <textarea name="content" rows="4" required
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
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($notices as $notice): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo $notice['id']; ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_category_color($notice['category']); ?>">
                            <?php echo htmlspecialchars($notice['category']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-800">
                        <?php if ($notice['important']): ?> <span class="text-red-500 mr-1">★</span> <?php endif; ?>
                        <?php echo htmlspecialchars($notice['title']); ?>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($notice['author']); ?></td>
                    <td class="px-6 py-4 text-gray-500"><?php echo htmlspecialchars($notice['created_at']); ?></td>
                    <td class="px-6 py-4">
                        <?php if ($notice['important']): ?>
                            <span class="px-3 py-1 rounded-full text-xs font-bold border bg-red-100 text-red