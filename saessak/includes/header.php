<?php
require_once __DIR__ . '/session_check.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="ko" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>새싹병원 - 언제나 당신 곁에 푸른 건강을</title>
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.css" />
    <!-- ✅ reCAPTCHA 스크립트 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <a href="index.php" class="flex items-center gap-2 group">
                        <span class="text-3xl sm:text-4xl group-hover:scale-110 transition-transform duration-300">🌱</span>
                        <div class="flex flex-col">
                            <span class="text-lg sm:text-xl font-bold text-sprout-800 tracking-tight leading-none">새싹병원</span>
                            <span class="text-[10px] text-sprout-500 font-semibold tracking-wider uppercase mt-1">Sprout Hospital</span>
                        </div>
                    </a>
                </div>

                <nav class="hidden md:flex space-x-10 text-base font-semibold">
                    <div class="relative group py-6">
                        <a href="html/about.html" class="text-gray-600 hover:text-sprout-600 transition-colors flex items-center gap-1">
                            병원소개
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-sprout-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </a>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 w-48 bg-white border border-gray-100 rounded-2xl shadow-xl py-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0">
                            <a href="html/about.html" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">소개 및 이념</a>
                            <a href="html/about.html#organization" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">병원 조직도</a>
                            <a href="index.php#doctors" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">대표 의료진</a>
                            <a href="index.php#directions" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">오시는 길</a>
                        </div>
                    </div>

                    <div class="relative group py-6">
                        <a href="#schedule" class="text-gray-600 hover:text-sprout-600 transition-colors flex items-center gap-1">
                            진료안내
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-sprout-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </a>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 w-48 bg-white border border-gray-100 rounded-2xl shadow-xl py-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0">
                            <a href="#schedule" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">진료일정 표</a>
                            <a href="#booking" class="block px-5 py-2.5 text-sm text-gray-600 hover:bg-sprout-50 hover:text-sprout-700">간편 예약하기</a>
                        </div>
                    </div>

                    <a href="#notice" class="text-gray-600 hover:text-sprout-600 transition-colors py-6">공지사항</a>
                    <a href="#directions" class="text-gray-600 hover:text-sprout-600 transition-colors py-6">오시는 길</a>
                </nav>

                <div class="hidden md:flex items-center gap-4">
                    <div id="desktop-auth-status-container" class="flex items-center gap-3">
                        <?php if (isset($_SESSION['patient_login_id'])): ?>
                            <span class="text-sm font-semibold text-sprout-700"><?= htmlspecialchars($_SESSION['patient_name'] ?? $_SESSION['patient_login_id']) ?>님</span>
                            <span class="w-px h-3 bg-gray-200"></span>
                            <a href="includes/logout.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold px-4 py-2.5 rounded-xl transition-all">로그아웃</a>
                        <?php else: ?>
                            <button onclick="toggleAuthModal('login')" class="text-sm font-semibold text-gray-600 hover:text-sprout-600 transition-colors">로그인</button>
                            <span class="w-px h-3 bg-gray-200"></span>
                            <button onclick="toggleAuthModal('signup')" class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold px-4 py-2.5 rounded-xl transition-all">회원가입</button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-500 hover:text-sprout-600 focus:outline-none p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 px-4 pt-4 pb-6 space-y-2 shadow-inner">
            <div class="space-y-1">
                <span class="block px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Menu 리스트</span>
                <div class="pl-2 space-y-1">
                    <a href="html/about.html" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-sprout-50 hover:text-sprout-600">소개 및 이념</a>
                    <a href="html/about.html#organization" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-sprout-50 hover:text-sprout-600">병원 조직도</a>
                    <a href="#doctors" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-sprout-50 hover:text-sprout-600">대표 의료진</a>
                </div>
                <div class="pl-2 pt-2 space-y-1 border-t border-slate-50">
                    <a href="#schedule" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-sprout-50">진료일정 표</a>
                    <a href="#booking" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-sprout-50">간편 예약</a>
                </div>
            </div>

            <a href="#notice" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-sprout-50 hover:text-sprout-600">공지사항</a>
            <a href="#directions" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-sprout-50 hover:text-sprout-600">오시는 길</a>
            
            <div class="pt-4 border-t border-gray-100 flex flex-col gap-2">
                <div id="mobile-auth-status-container" class="grid grid-cols-2 gap-2 px-3">
                    <?php if (isset($_SESSION['patient_login_id'])): ?>
                        <span class="text-center py-2.5 rounded-xl border border-sprout-100 text-sm font-semibold text-sprout-700"><?= htmlspecialchars($_SESSION['patient_name'] ?? $_SESSION['patient_login_id']) ?>님</span>
                        <a href="includes/logout.php" class="text-center py-2.5 rounded-xl bg-gray-100 text-sm font-semibold text-gray-800 hover:bg-gray-200">로그아웃</a>
                    <?php else: ?>
                        <button onclick="toggleAuthModal('login')" class="text-center py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50">로그인</button>
                        <button onclick="toggleAuthModal('signup')" class="text-center py-2.5 rounded-xl bg-gray-100 text-sm font-semibold text-gray-800 hover:bg-gray-200">회원가입</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div id="auth-modal" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative border border-gray-100">
            <button onclick="toggleAuthModal(null)" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            <form id="modal-login-form" action="includes/patient_login_process.php" method="POST" class="space-y-5">
                <div class="text-center space-y-2">
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">환자 로그인</h3>
                    <p class="text-xs text-gray-400">새싹병원 통합 전산 예약을 위해 회원 계정을 입력해 주세요.</p>
                </div>
                <div class="space-y-3 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">환자 로그인 ID</label>
                        <input type="text" id="login-id" name="login_id" required placeholder="patient" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">비밀번호</label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500">
                    </div>
                </div>
                <!-- ✅ reCAPTCHA 위젯 -->
                <div class="g-recaptcha" data-sitekey="보안사항"></div>
                <button type="submit" class="w-full bg-sprout-600 hover:bg-sprout-700 text-white font-bold py-3.5 rounded-xl text-sm shadow-md shadow-sprout-100 transition-all">접속 및 인증 가동</button>
                <div class="text-center text-xs text-gray-400 pt-2">
                    아직 새싹병원 회원이 아니신가요? <button type="button" onclick="toggleAuthModal('signup')" class="text-sprout-600 font-bold underline">즉시 가입</button>
                </div>
            </form>

            <form id="modal-signup-form" action="includes/signup_process.php" method="POST" onsubmit="return validateSignupForm()" class="space-y-4 hidden">
                <div class="text-center space-y-1">
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">신규 환자등록 (회원가입)</h3>
                    <p class="text-xs text-gray-400">본인확인 및 알림톡 발송을 위한 가입 절차를 진행합니다.</p>
                </div>
                <div class="space-y-2 pt-2 max-h-[350px] overflow-y-auto pr-1">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">환자 성명</label>
                        <input type="text" name="name" required placeholder="홍길동" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">희망 로그인 ID</label>
                        <input type="text" name="login_id" required placeholder="patient" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">비밀번호 설정</label>
                        <input type="password" id="signup-pw" name="password" required placeholder="최소 4자 이상" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">비밀번호 확인</label>
                        <input type="password" id="signup-pw-confirm" required placeholder="동일하게 입력" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">연락처 (휴대폰 번호)</label>
                        <input type="tel" name="phone" required placeholder="010-0000-0000" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500">
                    </div>
                </div>
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 rounded-xl text-sm transition-all mt-2">정식 환자 등록 완료</button>
                <div class="text-center text-xs text-gray-400 pt-1">
                    기존 계정이 존재하나요? <button type="button" onclick="toggleAuthModal('login')" class="text-sprout-600 font-bold underline">로그인하기</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        window.SAESSAK_PATIENT_LOGGED_IN = <?= isset($_SESSION['patient_login_id']) ? 'true' : 'false' ?>;
        window.SAESSAK_PATIENT_NAME = <?= json_encode($_SESSION['patient_name'] ?? '', JSON_UNESCAPED_UNICODE) ?>;
    </script>
