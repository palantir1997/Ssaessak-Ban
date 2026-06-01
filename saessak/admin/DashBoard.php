<?php
session_start();

// 로그인 정보가 없으면 즉시 로그인 페이지로 보냅니다.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // 로그인 페이지 경로가 맞는지 확인하세요!
    exit();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaessakAdmin - 병원 관리자 페이지</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans h-screen flex overflow-hidden">

    <?php
    // --- [Mock Data / 나중에 DB 연동할 영역] ---
    $waitlist = [
        ["id" => "1001", "name" => "김철수", "time" => "10:30", "dept" => "내과", "status" => "진료중", "note" => "혈압 체크 필요"],
        ["id" => "1002", "name" => "이영희", "time" => "10:45", "dept" => "이비인후과", "status" => "대기중", "note" => "초진"],
        ["id" => "1003", "name" => "박지민", "time" => "11:00", "dept" => "정형외과", "status" => "대기중", "note" => "X-Ray 촬영 완료"],
        ["id" => "1004", "name" => "최동훈", "time" => "10:15", "dept" => "내과", "status" => "완료", "note" => "처방전 발급"],
    ];

    // 상태별 색상을 리턴하는 PHP 함수
    function getStatusColor($status) {
        switch ($status) {
            case '진료중': return 'bg-blue-100 text-blue-700 border-blue-200';
            case '대기중': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
            case '완료': return 'bg-gray-100 text-gray-700 border-gray-200';
            default: return 'bg-gray-100 text-gray-700';
        }
    }
    ?>

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col hidden md:flex">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-blue-600 tracking-tight">SaessakHospital</h1>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg font-medium">
                <i data-lucide="activity" class="w-5 h-5"></i> 대시보드
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="users" class="w-5 h-5"></i> 환자 관리
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="calendar" class="w-5 h-5"></i> 예약 캘린더
            </a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shrink-0">
    <div class="relative w-96">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
        <input type="text" placeholder="환자 이름, 연락처 검색" class="w-full pl-10 pr-4 py-2 bg-gray-100 border border-transparent rounded-md focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none" />
    </div>

    <div class="flex items-center gap-4">
        <button class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
            <i data-lucide="bell" class="w-6 h-6"></i>
            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
        </button>

        <div class="flex items-center gap-2 pl-4 border-l border-gray-200">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                <?php echo strtoupper(substr($_SESSION['user_id'], 0, 1)); ?>
            </div>
            <span class="font-medium text-sm">
                <?php echo $_SESSION['user_id']; ?>님 환영합니다
            </span>
            <a href="include/logout.php" class="ml-2 text-xs text-red-500 hover:underline">로그아웃</a>
        </div>
    </div>
</header>

        <div class="flex-1 overflow-auto p-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                    <div class="p-4 bg-blue-100 text-blue-600 rounded-lg"><i data-lucide="calendar" class="w-8 h-8"></i></div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">금일 전체 예약</p>
                        <p class="text-2xl font-bold">142<span class="text-sm font-normal text-gray-400 ml-1">명</span></p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                    <div class="p-4 bg-yellow-100 text-yellow-600 rounded-lg"><i data-lucide="users" class="w-8 h-8"></i></div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">현재 대기 환자</p>
                        <p class="text-2xl font-bold text-yellow-600">18<span className="text-sm font-normal text-gray-400 ml-1">명</span></p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                    <div class="p-4 bg-green-100 text-green-600 rounded-lg"><i data-lucide="check-circle" class="w-8 h-8"></i></div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">진료 완료</p>
                        <p class="text-2xl font-bold">85<span class="text-sm font-normal text-gray-400 ml-1">명</span></p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
                    <div class="p-4 bg-red-100 text-red-600 rounded-lg"><i data-lucide="alert-circle" class="w-8 h-8"></i></div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">긴급 알림</p>
                        <p class="text-2xl font-bold text-red-600">2<span class="text-sm font-normal text-gray-400 ml-1">건</span></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-gray-50/50">
                        <h2 class="text-lg font-bold text-gray-800">실시간 대기 현황</h2>
                        <button class="text-sm text-blue-600 font-medium hover:underline">전체보기</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-sm border-b border-gray-200">
                                    <th class="px-6 py-4 font-medium">예약시간</th>
                                    <th class="px-6 py-4 font-medium">환자명 (차트번호)</th>
                                    <th class="px-6 py-4 font-medium">진료과</th>
                                    <th class="px-6 py-4 font-medium">상태</th>
                                    <th class="px-6 py-4 font-medium">비고</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                <?php foreach ($waitlist as $row): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900"><?php echo $row['time']; ?></td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold"><?php echo $row['name']; ?></span> 
                                        <span class="text-gray-400">(<?php echo $row['id']; ?>)</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600"><?php echo $row['dept']; ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo getStatusColor($row['status']); ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 truncate max-w-xs"><?php echo $row['note']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">빠른 실행</h2>
                        <div class="grid grid-cols-2 gap-3">
                            <button class="flex flex-col items-center justify-center p-4 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors gap-2">
                                <i data-lucide="user-plus" class="w-6 h-6"></i>
                                <span class="font-medium text-sm">현장 접수</span>
                            </button>
                            <button class="flex flex-col items-center justify-center p-4 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors gap-2">
                                <i data-lucide="megaphone" class="w-6 h-6"></i>
                                <span class="font-medium text-sm">긴급 공지</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>