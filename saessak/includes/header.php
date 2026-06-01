<!DOCTYPE html>
<html lang="ko" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>새싹병원 - 언제나 당신 곁에 푸른 건강을</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sprout: {
                            50: '#f2fcf5',
                            100: '#e2f7e9',
                            200: '#c5eed2',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    },
                    fontFamily: {
                        sans: ['Pretendard', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- CSS 불러오기 -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Pretendard Font -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.css" />
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <!-- HEADER / NAVIGATION -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- 로고 영역 -->
                <div class="flex items-center gap-2">
                    <a href="index.php" class="flex items-center gap-2 group">
                        <span class="text-3xl sm:text-4xl group-hover:scale-110 transition-transform duration-300">🌱</span>
                        <div class="flex flex-col">
                            <span class="text-lg sm:text-xl font-bold text-sprout-800 tracking-tight leading-none">새싹병원</span>
                            <span class="text-[10px] text-sprout-500 font-semibold tracking-wider uppercase mt-1">Sprout Hospital</span>
                        </div>
                    </a>
                </div>

                <!-- 데스크톱 대메뉴 (마우스 오버 시 하위 메뉴 노출) -->
                <nav class="hidden md:flex space-x-10 text-base font-semibold">
                    <!-- 병원소개 메뉴 -->
                    <div class="relative group py-6">
                        <a href="#about" class="text-gray-600 hover:text-sprout-600 transition-colors flex items-center gap-1">
                            병원소개
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-sprout-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </a>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 w-48 bg-white border border-gray-100 rounded-2xl shadow-xl py-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0">
                            <a href="#about" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">소개 및 이념</a>
                            <a href="#organization" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">병원 조직도</a>
                            <a href="#doctors" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">대표 의료진</a>
                            <a href="#directions" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">오시는 길</a>
                        </div>
                    </div>

                    <!-- 진료안내 메뉴 -->
                    <div class="relative group py-6">
                        <a href="#schedule" class="text-gray-600 hover:text-sprout-600 transition-colors flex items-center gap-1">
                            진료안내
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-sprout-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </a>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 w-48 bg-white border border-gray-100 rounded-2xl shadow-xl py-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0">
                            <a href="#schedule" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">진료시간 안내</a>
                            <a href="#schedule" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">진료일정 표</a>
                            <a href="#booking" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">간편 예약하기</a>
                        </div>
                    </div>

                    <a href="#notice" class="text-gray-600 hover:text-sprout-600 transition-colors py-6">공지사항</a>
                    <a href="#directions" class="text-gray-600 hover:text-sprout-600 transition-colors py-6">오시는 길</a>
                </nav>

                <!-- 우측 사용자 제어 세션 (회원 로그인 / 마이페이지 상태) -->
                <div class="hidden md:flex items-center gap-4">
                    <div id="auth-status-container" class="flex items-center gap-3">
                        <!-- 로그인 전 상태 -->
                        <button onclick="toggleAuthModal('login')" class="text-sm font-medium text-gray-600 hover:text-sprout-600 px-3 py-1.5 rounded-lg">로그인</button>
                        <button onclick="toggleAuthModal('signup')" class="text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-1.5 rounded-xl transition-all">회원가입</button>
                    </div>
                    <a href="#booking" class="bg-sprout-600 hover:bg-sprout-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md shadow-sprout-100 transition-all text-sm">
                        빠른 진료예약
                    </a>
                </div>

                <!-- 모바일 메뉴 버튼 -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-sprout-600 focus:outline-none p-2 rounded-lg" aria-label="메뉴 열기">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path id="menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- 모바일 아코디언 서브메뉴 Drawer -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-gray-100 max-h-[80vh] overflow-y-auto">
            <div class="px-4 pt-2 pb-6 space-y-3 text-base font-medium">
                <!-- 병원소개 모바일 그룹 -->
                <div class="border-b border-gray-50 pb-2">
                    <span class="block px-3 py-1.5 text-xs font-bold text-gray-400 uppercase tracking-wider">병원소개</span>
                    <div class="grid grid-cols-2 gap-1.5 mt-1 pl-2">
                        <a href="#about" class="px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-sprout-50">소개 및 이념</a>
                        <a href="#organization" class="px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-sprout-50">병원 조직도</a>
                        <a href="#doctors" class="px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-sprout-50">대표 의료진</a>
                        <a href="#directions" class="px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-sprout-50">오시는 길</a>
                    </div>
                </div>

                <!-- 진료안내 모바일 그룹 -->
                <div class="border-b border-gray-50 pb-2">
                    <span class="block px-3 py-1.5 text-xs font-bold text-gray-400 uppercase tracking-wider">진료안내</span>
                    <div class="grid grid-cols-2 gap-1.5 mt-1 pl-2">
                        <a href="#schedule" class="px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-sprout-50">진료시간 안내</a>
                        <a href="#schedule" class="px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-sprout-50">진료일정 표</a>
                        <a href="#booking" class="px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-sprout-50">간편 예약</a>
                    </div>
                </div>

                <a href="#notice" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-sprout-50 hover:text-sprout-600">공지사항</a>
                <a href="#directions" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-sprout-50 hover:text-sprout-600">오시는 길</a>
                
                <div class="pt-4 border-t border-gray-100 flex flex-col gap-2">
                    <div id="mobile-auth-status-container" class="grid grid-cols-2 gap-2 px-3">
                        <button onclick="toggleAuthModal('login')" class="text-center py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50">로그인</button>
                        <button onclick="toggleAuthModal('signup')" class="text-center py-2.5 rounded-xl bg-gray-100 text-sm font-semibold text-gray-800 hover:bg-gray-200">회원가입</button>
                    </div>
                    <a href="#booking" class="block w-full text-center bg-sprout-600 hover:bg-sprout-700 text-white font-bold py-3.5 rounded-xl shadow-sm transition-colors text-sm">
                        간편 예약하기
                    </a>
                </div>
            </div>
        </div>
    </header>