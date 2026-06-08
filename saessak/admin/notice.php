<?php
include 'include/header.php';

try {
    $db_host = '172.16.11.210'; 
    $db_user = 'root';
    $db_pass = ''; 
    $db_name = 'saessak';
    $db_port = 3306; 

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

    if (!$conn) {
        die("<div class='p-6 bg-red-100 text-red-700 rounded-xl m-6'>
                <h1 class='text-xl font-bold mb-2'>🚨 DB 서버 연결 실패</h1>
                <p>PHP 웹서버가 데이터베이스({$db_host}) 접속에 실패했습니다.</p>
                <p class='mt-4 font-mono text-sm bg-white p-3 rounded border border-red-200'>에러 내용: " . mysqli_connect_error() . "</p>
             </div>");
    }

    mysqli_set_charset($conn, 'utf8mb4');
} catch (Exception $e) {
    die("<div class='p-6 bg-red-100 text-red-700 rounded-xl m-6'>
            <h1 class='text-xl font-bold mb-2'>🚨 DB 서버 연결 실패</h1>
            <p>에러 내용: " . $e->getMessage() . "</p>
         </div>");
}

// ==========================================
// [1] 공지사항 등록 (INSERT 로직)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category    = mysqli_real_escape_string($conn, trim($_POST['category'] ?? ''));
    $title       = mysqli_real_escape_string($conn, trim($_POST['title'] ?? ''));
    $content     = mysqli_real_escape_string($conn, trim($_POST['content'] ?? ''));
    $is_important = isset($_POST['important']) ? 1 : 0;
    $author_name = 'Admin'; 

    if (!empty($category) && !empty($title) && !empty($content)) {
        $notice_no  = 'NT-' . date('YmdHis') . rand(10, 99);
        $created_at = date('Y-m-d');

        $insert_query = "
            INSERT INTO notices (notice_no, category, title, content, author_name, created_at, is_important)
            VALUES ('$notice_no', '$category', '$title', '$content', '$author_name', '$created_at', $is_important)
        ";

        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('공지사항이 등록되었습니다!'); location.href='notice.php';</script>";
            exit;
        } else {
            echo "<script>alert('DB 저장 오류: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('필수 입력 항목을 모두 채워주세요.');</script>";
    }
}

// ==========================================
// [2] 공지사항 목록 조회 (SELECT 로직)
// ==========================================
$select_query = "SELECT * FROM notices ORDER BY is_important DESC, created_at DESC";
$result = mysqli_query($conn, $select_query);

$notices = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $notices[] = $row;
    }
}

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
    <form id="notice_form" action="" method="POST">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">카테고리</label>
                <select name="category" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg">
                    <option value="">카테고리 선택</option>
                    <option value="공지">공지</option>
                    <option value="휴진">휴진</option>
                    <option value="소식">소식</option>
                    <option value="안내">안내</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">제목</label>
                <input type="text" name="title" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" placeholder="공지사항 제목을 입력하세요">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 mb-1">내용</label>
            <textarea name="content" rows="4" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg"></textarea>
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition">등록하기</button>
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
                    <th class="px-6 py-4 font-medium">카테고리</th>
                    <th class="px-6 py-4 font-medium">제목</th>
                    <th class="px-6 py-4 font-medium">작성자</th>
                    <th class="px-6 py-4 font-medium">작성일</th>
                    <th class="px-6 py-4 font-medium">관리</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($notices)): ?>
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">등록된 공지사항이 없습니다.</td></tr>
                <?php else: foreach ($notices as $notice): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_category_color($notice['category']); ?>">
                            <?php echo htmlspecialchars($notice['category']); ?>
                        </span>
                    </td>
                    <!-- 제목 클릭 → 상세/수정 페이지 이동 -->
                    <td class="px-6 py-4 font-medium text-gray-800">
                        <a href="edit_notice.php?no=<?php echo urlencode($notice['notice_no']); ?>" 
                           class="hover:text-blue-600 hover:underline cursor-pointer">
                            <?php if ($notice['is_important']): ?>
                                <span class="text-red-500 font-bold mr-1">[중요]</span>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($notice['title']); ?>
                        </a>
                    </td>
                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($notice['author_name']); ?></td>
                    <td class="px-6 py-4 text-gray-500"><?php echo htmlspecialchars($notice['created_at']); ?></td>
                    <td class="px-6 py-4">
                        <a href="delete_notice.php?no=<?php echo urlencode($notice['notice_no']); ?>" 
                           onclick="return confirm('정말 삭제하시겠습니까?')"
                           class="text-red-500 hover:text-red-700 text-xs font-bold border border-red-200 px-3 py-1 rounded-lg hover:bg-red-50 transition">
                            삭제
                        </a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
mysqli_close($conn);
include 'include/footer.php'; 
?>