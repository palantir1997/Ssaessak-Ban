<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 예시: 사용자 권한 확인 (데이터베이스 설정에 따라 'admin' 또는 숫자 등 변경 가능)
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>SaessakAdmin - 병원 관리자 페이지</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans h-screen flex overflow-hidden">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col hidden md:flex">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-blue-600 tracking-tight">SaessakHospital</h1>
        </div>
        <nav class="flex-1 p-4 space-y-6 overflow-y-auto">
            
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 px-4">환자 진료 관리</p>
                <div class="space-y-1">
                    <a href="index.php" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg"><i data-lucide="activity" class="w-4 h-4"></i> 대시보드</a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg"><i data-lucide="user-plus" class="w-4 h-4"></i> 접수/예약</a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg"><i data-lucide="file-text" class="w-4 h-4"></i> 진료 기록/차트</a>
                </div>
            </div>

            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 px-4">의료 자원 관리</p>
                <div class="space-y-1">
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg"><i data-lucide="stethoscope" class="w-4 h-4"></i> 의료진 관리</a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg"><i data-lucide="cpu" class="w-4 h-4"></i> 의료 장비 관리</a>
                </div>
            </div>

            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 px-4">원무/행정 관리</p>
                <div class="space-y-1">
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg"><i data-lucide="shield-user" class="w-4 h-4"></i> 직원 계정 관리</a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg"><i data-lucide="megaphone" class="w-4 h-4"></i> 공지사항</a>
                </div>
            </div>

            <?php if ($isAdmin): ?>
            <div class="pt-4 border-t border-gray-200">
                <p class="text-xs font-bold text-red-400 uppercase tracking-wider mb-2 px-4">보안 관리 (Admin)</p>
                <div class="space-y-1">
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 rounded-lg font-medium"><i data-lucide="shield-alert" class="w-4 h-4"></i> 보안 관제 대시보드</a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 rounded-lg font-medium"><i data-lucide="terminal" class="w-4 h-4"></i> 보안 점검 도구</a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 rounded-lg font-medium"><i data-lucide="file-lock" class="w-4 h-4"></i> 침입 탐지 로그</a>
                </div>
            </div>
            <?php endif; ?>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
            <div class="text-gray-500">SaessakHospital Management System</div>
            <div class="flex items-center gap-4">
                <span class="text-sm"><?php echo $_SESSION['user_id']; ?>님</span>
                <a href="include/logout.php" class="text-xs text-red-500 underline">로그아웃</a>
            </div>
        </header>
        <div class="flex-1 overflow-auto p-8">