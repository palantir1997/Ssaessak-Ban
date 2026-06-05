<?php
// admin/edit_notice.php
// 파일이 admin 폴더에 있으므로 상위 폴더(..)의 includes를 참조합니다.
include '../includes/header.php'; 
include '../includes/db.php';

// 넘겨받은 고유 번호(no) 확인
$no = isset($_GET['no']) ? mysqli_real_escape_string($conn, $_GET['no']) : 0;

// 기존 데이터 불러오기
$query = "SELECT * FROM notices WHERE notice_no = '$no'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<script>alert('공지사항을 찾을 수 없습니다.'); history.back();</script>";
    exit();
}
?>

<div class="p-8 max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-6">공지사항 수정</h2>
    
    <form action="update_notice.php" method="POST" class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
        <input type="hidden" name="no" value="<?php echo $data['notice_no']; ?>">
        
        <div class="mb-4">
            <label class="block font-semibold mb-2">제목</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" class="w-full border p-2 rounded" required>
        </div>
        
        <div class="mb-6">
            <label class="block font-semibold mb-2">내용</label>
            <textarea name="content" class="w-full border p-2 rounded" rows="10" required><?php echo htmlspecialchars($data['content']); ?></textarea>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">수정 완료</button>
            <a href="notice.php" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 transition">취소</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>