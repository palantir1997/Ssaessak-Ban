<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'admin') {
    echo "<script>alert('관리자 로그인 후 이용 가능합니다.'); location.href='login.php';</script>";
    exit();
}
include_once __DIR__ . '/../includes/db.php';
$result = mysqli_query($conn, 'SELECT id, name, login_id, phone, created_at FROM patients ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>환자 계정 목록 - 새싹병원</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">환자 계정 목록</h1>
                <p class="text-sm text-gray-500 mt-1">patients 테이블에 저장된 환자 로그인 계정입니다.</p>
            </div>
            <a href="DashBoard.php" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold">대시보드</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 border-b">
                    <tr>
                        <th class="p-4">번호</th><th class="p-4">이름</th><th class="p-4">로그인 ID</th><th class="p-4">전화번호</th><th class="p-4">가입일</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-4"><?= htmlspecialchars($row['id']) ?></td>
                                <td class="p-4 font-bold text-gray-900"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-4 text-emerald-700 font-semibold"><?= htmlspecialchars($row['login_id']) ?></td>
                                <td class="p-4"><?= htmlspecialchars($row['phone']) ?></td>
                                <td class="p-4 text-gray-500"><?= htmlspecialchars($row['created_at']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="p-8 text-center text-gray-400">등록된 환자 계정이 없습니다.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
