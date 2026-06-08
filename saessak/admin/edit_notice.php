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
        die("<div class='p-6 bg-red-100 text-red-700 rounded-xl m-6'>DB 연결 실패: " . mysqli_connect_error() . "</div>");
    }
    mysqli_set_charset($conn, 'utf8mb4');
} catch (Exception $e) {
    die("<div class='p-6 bg-red-100 text-red-700 rounded-xl m-6'>에러: " . $e->getMessage() . "</div>");
}

// notice_no 받아오기
$no = isset($_GET['no']) ? mysqli_real_escape_string($conn, $_GET['no']) : '';

if (empty($no)) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='notice.php';</script>";
    exit();
}

// 기존 데이터 불러오기
$query  = "SELECT * FROM notices WHERE notice_no = '$no'";
$result = mysqli_query($conn, $query);
$data   = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<script>alert('공지사항을 찾을 수 없습니다.'); location.href='notice.php';</script>";
    exit();
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

<div class="max-w-4xl mx-auto">

    <!-- 상단 헤더 -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-800">공지사항 상세 / 수정</h2>
        <a href="notice.php" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            ← 목록으로
        </a>
    </div>

    <!-- 공지사항 상세보기 카드 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="flex items-center gap-3 mb-3">
            <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo get_category_color($data['category']); ?>">
                <?php echo htmlspecialchars($data['category']); ?>
            </span>
            <?php if ($data['is_important']): ?>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600 border border-red-200">⚠ 중요</span>
            <?php endif; ?>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($data['title']); ?></h3>
        <p class="text-xs text-gray-400 mb-4">
            작성자: <?php echo htmlspecialchars($data['author_name']); ?> &nbsp;|&nbsp; 
            작성일: <?php echo htmlspecialchars($data['created_at']); ?>
        </p>
        <div class="border-t border-gray-100 pt-4 text-sm text-gray-700 whitespace-pre-line leading-relaxed">
            <?php echo htmlspecialchars($data['content']); ?>
        </div>
    </div>

    <!-- 수정 폼 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-base font-bold text-gray-800 mb-4">내용 수정</h3>
        <form action="update_notice.php" method="POST">
            <input type="hidden" name="no" value="<?php echo htmlspecialchars($data['notice_no']); ?>">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">카테고리</label>
                    <select name="category" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                        <?php foreach (['공지','휴진','소식','안내'] as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo ($data['category'] === $cat) ? 'selected' : ''; ?>>
                            <?php echo $cat; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">제목</label>
                    <input type="text" name="title" required
                           value="<?php echo htmlspecialchars($data['title']); ?>"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">내용</label>
                <textarea name="content" rows="8" required
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm"><?php echo htmlspecialchars($data['content']); ?></textarea>
            </div>

            <!-- 버튼 영역 -->
            <div class="flex gap-3">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition text-sm">
                    수정 완료
                </button>
                <a href="notice.php" 
                   class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition text-sm">
                    취소
                </a>
                <a href="delete_notice.php?no=<?php echo urlencode($data['notice_no']); ?>"
                   onclick="return confirm('정말 삭제하시겠습니까?')"
                   class="ml-auto px-6 py-2 bg-red-500 text-white rounded-lg font-bold hover:bg-red-600 transition text-sm">
                    삭제
                </a>
            </div>
        </form>
    </div>

</div>

<?php 
mysqli_close($conn);
include 'include/footer.php'; 
?>