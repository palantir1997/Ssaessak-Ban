<?php 
// PHP 인클루드 파일을 통한 구역화 모듈 연동
include_once 'includes/header.php'; 
?>

<main class="flex-grow">
    <!-- HERO SECTION -->
    <section id="hero" class="relative overflow-hidden bg-gradient-to-tr from-sprout-100 via-white to-sprout-50 py-16 lg:py-28">
        <!-- 배경 데코레이션 그라디언트 블롭 -->
        <div class="absolute top-20 right-[-10%] w-96 h-96 bg-sprout-200/50 rounded-full mix-blend-multiply filter blur-3xl opacity-60"></div>
        <div class="absolute bottom-10 left-[-5%] w-80 h-80 bg-emerald-100/40 rounded-full mix-blend-multiply filter blur-3xl opacity-60"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- 헤드 카피 단락 -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-sprout-100 text-sprout-800">
                        🌱 당신의 건강에 언제나 푸른 봄날을
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-tight">
                        건강한 새싹이 큰 나무로<br class="hidden sm:inline" />
                        성장하듯, <span class="text-sprout-600">평생의 건강</span>을 함께합니다.
                    </h1>
                    <p class="text-base sm:text-lg text-gray-600 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        새싹병원은 각 분야 최고의 전문의들이 성심을 다해 환자 중심의 맞춤 의료 서비스를 제공합니다. 과잉 진료 없는 정밀한 안심 진료 약속을 직접 경험해 보세요.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
                        <a href="#booking" class="inline-flex justify-center items-center gap-2 bg-sprout-600 hover:bg-sprout-700 text-white font-semibold px-8 py-4 rounded-xl shadow-lg shadow-sprout-200 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            간편예약/진료예약 바로가기
                        </a>
                        <a href="#schedule" class="inline-flex justify-center items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold px-8 py-4 rounded-xl border border-gray-200 shadow-sm transition-all">
                            외래 진료 시간표 조회
                        </a>
                    </div>

                    <!-- 실시간 진료 상태 표시 (JS 연동) -->
                    <div class="pt-4 flex justify-center lg:justify-start items-center gap-3">
                        <div class="relative flex h-3.5 w-3.5">
                            <span id="status-ping" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span id="status-dot" class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500"></span>
                        </div>
                        <span id="realtime-status-text" class="text-sm font-semibold text-gray-600">진료 여부 확인 중...</span>
                    </div>
                </div>

                <!-- 히어로 우측 통계형 위젯 블록 -->
                <div class="lg:col-span-5 relative">
                    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 flex flex-col space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="bg-sprout-100 p-3.5 rounded-2xl text-sprout-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 leading-tight">새싹 통합 외래 케어 센터</h3>
                                <p class="text-xs text-gray-500 mt-1">5명의 세부 전문의 배치 완비</p>
                            </div>
                        </div>

                        <hr class="border-gray-100" />

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-xl text-center">
                                <div class="text-2xl font-bold text-sprout-600">5명</div>
                                <div class="text-xs text-gray-500 mt-1">상주 전임의</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl text-center">
                                <div class="text-2xl font-bold text-sprout-600">20:00</div>
                                <div class="text-xs text-gray-500 mt-1">화/목 야간진료</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl text-center">
                                <div class="text-2xl font-bold text-sprout-600">1등급</div>
                                <div class="text-xs text-gray-500 mt-1">환자 안심 진료병원</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl text-center">
                                <div class="text-2xl font-bold text-sprout-600">무상</div>
                                <div class="text-xs text-gray-500 mt-1">건물 주차 지원 (2시간)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 병원소개 및 조직도 (about.html 조각 로드) -->
    <section id="about" class="py-20 sm:py-24 bg-white border-b border-gray-100">
        <?php 
        // 조직도 및 소개가 있는 HTML 컴포넌트를 조각 분할하여 포함
        $about_content = file_get_contents('html/about.html'); 
        if ($about_content !== false) {
            echo $about_content;
        } else {
            echo '<p class="text-center text-gray-500">소개 콘텐츠를 로드하는 데 오류가 발생했습니다.</p>';
        }
        ?>
    </section>

    <!-- DOCTORS SECTION (대표 의료진 소개) -->
    <section id="doctors" class="py-20 sm:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-widest text-sprout-600">Medical Specialists</h2>
                <p class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">환자를 먼저 생각하는 새싹병원 대표 의료진</p>
                <p class="text-gray-600">수년간의 풍부한 대학병원 임상경험을 가진 각 진료과별 전문의들이 세심한 1대1 치유 솔루션을 설계해 드립니다.</p>
            </div>

            <!-- 의료진 프로필 리스트 (고품질 일러스트 내장 + 동적 폴백 탑재) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                <!-- 김새싹 원장 (가정의학과) -->
                <div class="bg-white rounded-3xl border border-gray-200/60 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden text-center group">
                    <div class="relative h-64 bg-emerald-50 flex items-center justify-center p-6">
                        <!-- 이미지 실패 시 일러스트 폴백 보존과 함께 <img> 렌더링 시도 -->
                        <img class="doctor-photo w-full h-full object-contain object-bottom absolute inset-0 z-10 hidden" src="watermarked_img_17502554902084857653.png" alt="김새싹 대표원장" onerror="handleImageError(this)">
                        
                        <!-- 일러스트 폴백 (클래식 의사 디자인) -->
                        <div class="doctor-fallback flex flex-col items-center justify-center text-center space-y-3 z-0">
                            <svg class="w-24 h-24 text-emerald-600" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="30" r="18" fill="currentColor" opacity="0.8"/>
                                <path d="M50 52 C30 52 20 64 20 80 L80 80 C80 64 70 52 50 52 Z" fill="currentColor" opacity="0.9"/>
                                <rect x="42" y="55" width="16" height="25" fill="#ffffff" />
                                <path d="M42 55 L35 70 M58 55 L65 70" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="50" cy="53" r="3" fill="#10b981"/>
                            </svg>
                            <span class="text-xs bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">CMO / 가정의학과</span>
                        </div>
                    </div>
                    <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="font-extrabold text-lg text-gray-900 group-hover:text-sprout-600 transition-colors">김새싹 대표원장</h3>
                            <p class="text-xs text-sprout-600 font-semibold mt-1">가정의학과 전문의</p>
                            <div class="text-xs text-gray-500 mt-3 space-y-1 text-left border-t border-gray-50 pt-3">
                                <p class="line-clamp-1">· 서울대학교 의과대학 졸업</p>
                                <p class="line-clamp-1">· 前 서울대학교병원 임상교수</p>
                                <p class="line-clamp-1">· 대한가정의학회 기획이사</p>
                            </div>
                        </div>
                        <a href="#booking" onclick="preselectDept('가정의학과')" class="block w-full py-2.5 rounded-xl text-xs font-bold border border-sprout-200 text-sprout-700 bg-sprout-50/50 hover:bg-sprout-600 hover:text-white transition-all">가정의학과 예약</a>
                    </div>
                </div>

                <!-- 박건우 원장 (내과) -->
                <div class="bg-white rounded-3xl border border-gray-200/60 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden text-center group">
                    <div class="relative h-64 bg-blue-50 flex items-center justify-center p-6">
                        <img class="doctor-photo w-full h-full object-contain object-bottom absolute inset-0 z-10 hidden" src="watermarked_img_17502554902084857653.png" alt="박건우 원장" onerror="handleImageError(this)">
                        
                        <div class="doctor-fallback flex flex-col items-center justify-center text-center space-y-3 z-0">
                            <svg class="w-24 h-24 text-blue-600" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="30" r="18" fill="currentColor" opacity="0.8"/>
                                <path d="M50 52 C30 52 20 64 20 80 L80 80 C80 64 70 52 50 52 Z" fill="currentColor" opacity="0.9"/>
                                <rect x="42" y="55" width="16" height="25" fill="#ffffff" />
                                <path d="M42 55 L35 70 M58 55 L65 70" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="50" cy="53" r="3" fill="#3b82f6"/>
                            </svg>
                            <span class="text-xs bg-blue-100 text-blue-800 font-bold px-2 py-0.5 rounded-full">내과 과장</span>
                        </div>
                    </div>
                    <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="font-extrabold text-lg text-gray-900 group-hover:text-blue-600 transition-colors">박건우 원장</h3>
                            <p class="text-xs text-blue-600 font-semibold mt-1">소화기내과 전문의</p>
                            <div class="text-xs text-gray-500 mt-3 space-y-1 text-left border-t border-gray-50 pt-3">
                                <p class="line-clamp-1">· 연세대학교 의학전문대학원 졸업</p>
                                <p class="line-clamp-1">· 前 신촌세브란스병원 임상조교수</p>
                                <p class="line-clamp-1">· 대한소화기내시경학회 평생회원</p>
                            </div>
                        </div>
                        <a href="#booking" onclick="preselectDept('내과')" class="block w-full py-2.5 rounded-xl text-xs font-bold border border-blue-200 text-blue-700 bg-blue-50/50 hover:bg-blue-600 hover:text-white transition-all">내과 예약하기</a>
                    </div>
                </div>

                <!-- 최태양 원장 (정형외과) -->
                <div class="bg-white rounded-3xl border border-gray-200/60 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden text-center group">
                    <div class="relative h-64 bg-orange-50 flex items-center justify-center p-6">
                        <img class="doctor-photo w-full h-full object-contain object-bottom absolute inset-0 z-10 hidden" src="watermarked_img_17502554902084857653.png" alt="최태양 원장" onerror="handleImageError(this)">
                        
                        <div class="doctor-fallback flex flex-col items-center justify-center text-center space-y-3 z-0">
                            <svg class="w-24 h-24 text-orange-600" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="30" r="18" fill="currentColor" opacity="0.8"/>
                                <path d="M50 52 C30 52 20 64 20 80 L80 80 C80 64 70 52 50 52 Z" fill="currentColor" opacity="0.9"/>
                                <rect x="42" y="55" width="16" height="25" fill="#ffffff" />
                                <path d="M42 55 L35 70 M58 55 L65 70" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="50" cy="53" r="3" fill="#f97316"/>
                            </svg>
                            <span class="text-xs bg-orange-100 text-orange-800 font-bold px-2 py-0.5 rounded-full">정형외과 과장</span>
                        </div>
                    </div>
                    <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="font-extrabold text-lg text-gray-900 group-hover:text-orange-600 transition-colors">최태양 원장</h3>
                            <p class="text-xs text-orange-600 font-semibold mt-1">척추·관절 정형외과 전문의</p>
                            <div class="text-xs text-gray-500 mt-3 space-y-1 text-left border-t border-gray-50 pt-3">
                                <p class="line-clamp-1">· 고려대학교 의과대학 석사 졸업</p>
                                <p class="line-clamp-1">· 前 고려대학교 구로병원 외래조교수</p>
                                <p class="line-clamp-1">· 대한도수의학회 정회원</p>
                            </div>
                        </div>
                        <a href="#booking" onclick="preselectDept('정형외과')" class="block w-full py-2.5 rounded-xl text-xs font-bold border border-orange-200 text-orange-700 bg-orange-50/50 hover:bg-orange-600 hover:text-white transition-all">정형외과 예약</a>
                    </div>
                </div>

                <!-- 이지민 원장 (소아청소년과) -->
                <div class="bg-white rounded-3xl border border-gray-200/60 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden text-center group">
                    <div class="relative h-64 bg-pink-50 flex items-center justify-center p-6">
                        <img class="doctor-photo w-full h-full object-contain object-bottom absolute inset-0 z-10 hidden" src="watermarked_img_17502554902084857653.png" alt="이지민 원장" onerror="handleImageError(this)">
                        
                        <div class="doctor-fallback flex flex-col items-center justify-center text-center space-y-3 z-0">
                            <svg class="w-24 h-24 text-pink-600" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="30" r="18" fill="currentColor" opacity="0.8"/>
                                <path d="M50 52 C30 52 20 64 20 80 L80 80 C80 64 70 52 50 52 Z" fill="currentColor" opacity="0.9"/>
                                <rect x="42" y="55" width="16" height="25" fill="#ffffff" />
                                <path d="M42 55 L35 70 M58 55 L65 70" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="50" cy="53" r="3" fill="#ec4899"/>
                            </svg>
                            <span class="text-xs bg-pink-100 text-pink-800 font-bold px-2 py-0.5 rounded-full">소아과 과장</span>
                        </div>
                    </div>
                    <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="font-extrabold text-lg text-gray-900 group-hover:text-pink-600 transition-colors">이지민 원장</h3>
                            <p class="text-xs text-pink-600 font-semibold mt-1">소아과 세부전문의</p>
                            <div class="text-xs text-gray-500 mt-3 space-y-1 text-left border-t border-gray-50 pt-3">
                                <p class="line-clamp-1">· 가톨릭대학교 의과대학 의학박사</p>
                                <p class="line-clamp-1">· 前 서울성모병원 소아청소년과 전문의</p>
                                <p class="line-clamp-1">· 대한소아알레르기호흡기학회 회원</p>
                            </div>
                        </div>
                        <a href="#booking" onclick="preselectDept('소아청소년과')" class="block w-full py-2.5 rounded-xl text-xs font-bold border border-pink-200 text-pink-700 bg-pink-50/50 hover:bg-pink-600 hover:text-white transition-all">소아청소년과 예약</a>
                    </div>
                </div>

                <!-- 한소희 원장 (종합건진센터 소장) -->
                <div class="bg-white rounded-3xl border border-gray-200/60 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden text-center group">
                    <div class="relative h-64 bg-teal-50 flex items-center justify-center p-6">
                        <img class="doctor-photo w-full h-full object-contain object-bottom absolute inset-0 z-10 hidden" src="watermarked_img_17502554902084857653.png" alt="한소희 원장" onerror="handleImageError(this)">
                        
                        <div class="doctor-fallback flex flex-col items-center justify-center text-center space-y-3 z-0">
                            <svg class="w-24 h-24 text-teal-600" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="30" r="18" fill="currentColor" opacity="0.8"/>
                                <path d="M50 52 C30 52 20 64 20 80 L80 80 C80 64 70 52 50 52 Z" fill="currentColor" opacity="0.9"/>
                                <rect x="42" y="55" width="16" height="25" fill="#ffffff" />
                                <path d="M42 55 L35 70 M58 55 L65 70" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="50" cy="53" r="3" fill="#14b8a6"/>
                            </svg>
                            <span class="text-xs bg-teal-100 text-teal-800 font-bold px-2 py-0.5 rounded-full">검진 소장</span>
                        </div>
                    </div>
                    <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="font-extrabold text-lg text-gray-900 group-hover:text-teal-600 transition-colors">한소희 원장</h3>
                            <p class="text-xs text-teal-600 font-semibold mt-1">가정의학 및 정밀검진 전문의</p>
                            <div class="text-xs text-gray-500 mt-3 space-y-1 text-left border-t border-gray-50 pt-3">
                                <p class="line-clamp-1">· 한양대학교 의과대학 외래교수</p>
                                <p class="line-clamp-1">· 前 KMI 한국의학연구소 검진과장</p>
                                <p class="line-clamp-1">· 대한임상건강의학회 정회원</p>
                            </div>
                        </div>
                        <a href="#booking" onclick="preselectDept('가정의학과')" class="block w-full py-2.5 rounded-xl text-xs font-bold border border-teal-200 text-teal-700 bg-teal-50/50 hover:bg-teal-600 hover:text-white transition-all">종합검진 상담예약</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CLINIC HOURS / SCHEDULE (진료시간 및 진료일정) -->
    <section id="schedule" class="py-20 sm:py-24 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- 진료시간 설명 영역 -->
                <div class="lg:col-span-5 space-y-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-sprout-600">Clinic Hours</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">요일별 연장 야간진료 및 직장인을 위한 주말진료 안내</h2>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">
                        새싹병원은 바쁘신 현대 직장인들과 학생들의 진료 편의를 위하여 **매주 화요일과 목요일은 오후 8시까지 야간 진료**를 가동하고 있으며, 토요일에도 아침 9시부터 오후 1시까지 주말 정상 외래 진료를 제공합니다.
                    </p>
                    <div class="bg-sprout-50 rounded-2xl p-5 border border-sprout-100/60 flex flex-col space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-sprout-900">당일 접수 안내</span>
                            <span class="bg-sprout-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">마감 30분 전</span>
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            원활한 검사 및 진료를 확보하고자 모든 외래 당일 접수는 진료 마감 시간 30분 전까지 완료하셔야 합니다.
                        </p>
                    </div>
                </div>

                <!-- 진료시간표 테이블 영역 -->
                <div class="lg:col-span-7 bg-gray-50 rounded-3xl p-6 sm:p-8 border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 text-gray-400 font-medium">
                                    <th class="py-3 pb-4">진료 시간</th>
                                    <th class="py-3 pb-4">평일 (월~금)</th>
                                    <th class="py-3 pb-4">토요일</th>
                                    <th class="py-3 pb-4">일요일/공휴일</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                                <tr class="hover:bg-white/50 transition-colors">
                                    <td class="py-4 text-gray-900 font-semibold">오전 진료</td>
                                    <td class="py-4">09:00 ~ 13:00</td>
                                    <td class="py-4">09:00 ~ 13:00</td>
                                    <td class="py-4 text-red-500">정기 휴진</td>
                                </tr>
                                <tr class="hover:bg-white/50 transition-colors">
                                    <td class="py-4 text-gray-900 font-semibold">점심 시간</td>
                                    <td class="py-4">13:00 ~ 14:00</td>
                                    <td class="py-4 text-gray-400">점심시간 없음</td>
                                    <td class="py-4 text-red-500">휴진</td>
                                </tr>
                                <tr class="hover:bg-white/50 transition-colors">
                                    <td class="py-4 text-gray-900 font-semibold">오후 진료</td>
                                    <td class="py-4">14:00 ~ 18:00</td>
                                    <td class="py-4 text-red-500">휴진</td>
                                    <td class="py-4 text-red-500">휴진</td>
                                </tr>
                                <tr class="hover:bg-white/50 transition-colors bg-sprout-50/50">
                                    <td class="py-4 text-sprout-900 font-bold">야간 진료</td>
                                    <td class="py-4 font-semibold text-sprout-700">18:00 ~ 20:00 (화, 목)</td>
                                    <td class="py-4 text-red-500">휴진</td>
                                    <td class="py-4 text-red-500">휴진</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BOOKING SYSTEM (간편진료예약 및 리얼타임 데이터 전송) -->
    <section id="booking" class="py-20 sm:py-24 bg-sprout-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-widest text-sprout-600">Online Quick Booking</h2>
                <p class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">전화 대기 없이 해결하는 간편예약 신청</p>
                <p class="text-gray-600">회원가입 후 로그인 상태이거나 혹은 기본 정보(성함, 휴대폰 번호) 기재만으로 안전하게 진료 예약을 접수할 수 있습니다.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                <!-- 예약 입력 양식 카드 -->
                <div class="lg:col-span-7 bg-white p-6 sm:p-10 rounded-3xl shadow-xl shadow-gray-100 border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span>📅</span> 외래 진료 예약을 작성해 주세요
                    </h3>

                    <form id="booking-form" onsubmit="handleFormSubmit(event)" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">성함 <span class="text-red-500">*</span></label>
                                <input type="text" id="book-name" required placeholder="홍길동" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none focus:ring-2 focus:ring-sprout-100 transition-all text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">연락처 <span class="text-red-500">*</span></label>
                                <input type="tel" id="book-phone" required placeholder="010-1234-5678" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none focus:ring-2 focus:ring-sprout-100 transition-all text-sm" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">희망 진료과 <span class="text-red-500">*</span></label>
                                <select id="book-dept" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none focus:ring-2 focus:ring-sprout-100 bg-white transition-all text-sm">
                                    <option value="">진료과를 선택해 주세요</option>
                                    <option value="가정의학과">가정의학과 (김새싹 원장)</option>
                                    <option value="내과">내과 (박건우 원장)</option>
                                    <option value="정형외과">정형외과 (최태양 원장)</option>
                                    <option value="소아청소년과">소아청소년과 (이지민 원장)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">진료 희망 일자 <span class="text-red-500">*</span></label>
                                <input type="date" id="book-date" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none focus:ring-2 focus:ring-sprout-100 transition-all text-sm" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">원하시는 시간대 <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="flex items-center justify-center border border-gray-200 rounded-xl p-3.5 cursor-pointer hover:bg-sprout-50 hover:border-sprout-300 transition-all text-xs font-semibold">
                                        <input type="radio" name="book-time" value="오전" required class="sr-only peer" />
                                        <span class="text-gray-600 peer-checked:text-sprout-700 peer-checked:font-bold">오전 (09:00~13:00)</span>
                                    </label>
                                    <label class="flex items-center justify-center border border-gray-200 rounded-xl p-3.5 cursor-pointer hover:bg-sprout-50 hover:border-sprout-300 transition-all text-xs font-semibold">
                                        <input type="radio" name="book-time" value="오후" class="sr-only peer" />
                                        <span class="text-gray-600 peer-checked:text-sprout-700 peer-checked:font-bold">오후 (14:00~18:00)</span>
                                    </label>
                                    <label class="flex items-center justify-center border border-gray-200 rounded-xl p-3.5 cursor-pointer hover:bg-sprout-50 hover:border-sprout-300 transition-all text-xs font-semibold">
                                        <input type="radio" name="book-time" value="야간" class="sr-only peer" />
                                        <span class="text-gray-600 peer-checked:text-sprout-700 peer-checked:font-bold">야간 (18:00~20:00)</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">아프신 부위 및 증상 기술</label>
                                <textarea id="book-notes" rows="3" placeholder="예: '정형외과 - 일주일 전부터 등산 후 오른쪽 무릎 관절 부위에 통증이 발생하고 있습니다.'" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none focus:ring-2 focus:ring-sprout-100 transition-all text-sm placeholder:text-gray-300"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-sprout-600 hover:bg-sprout-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-sprout-100 transition-all transform active:scale-[0.98]">
                            간편 예약 접수 신청하기
                        </button>
                    </form>
                </div>

                <!-- 실시간 나의 접수 확인 대시보드 -->
                <div class="lg:col-span-5 bg-gradient-to-br from-sprout-800 to-sprout-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col justify-between h-full min-h-[460px]">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold flex items-center gap-2">
                                <span>🔍</span> 실시간 나의 예약 현황
                            </h3>
                            <span class="bg-emerald-500/20 text-emerald-300 text-xs font-semibold px-2.5 py-1 rounded-full border border-emerald-500/30">안심 세션 연동</span>
                        </div>
                        <p class="text-xs text-sprout-200 leading-relaxed">
                            접수 완료된 본 세션의 예약 현황 및 이전 검사 대기 리스트입니다. (예약 취소는 목록 우측 상단의 x 버튼을 이용하실 수 있습니다)
                        </p>

                        <!-- 예약 목록 돔 로드 -->
                        <div id="booking-list" class="space-y-4 mt-6 max-h-[300px] overflow-y-auto pr-1">
                            <!-- JS를 통해 동적 생성됨 -->
                        </div>
                    </div>

                    <div class="bg-white/5 p-4 rounded-2xl border border-white/10 text-xs space-y-1.5 text-sprout-100 mt-4">
                        <p class="font-bold text-white">진료 내원 약속 안내</p>
                        <p>· 예약이 확정되면 기재해주신 연락처로 예약 확인 알림 문자가 발송됩니다.</p>
                        <p>· 진료 시작 시간 10분 전까지 본원 2층 원무 접수대로 내원해 주시기 바랍니다.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NOTICE SECTION (공지사항 - 검색 및 아코디언 컴포넌트) -->
    <section id="notice" class="py-20 sm:py-24 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div class="space-y-4 max-w-2xl">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-sprout-600">Notice & News</h2>
                    <p class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">새싹병원 소식지 및 건강 칼럼</p>
                    <p class="text-gray-600">병원 일정 변경 및 우리동네 필수 건강 소식을 빠르고 정확하게 공유합니다.</p>
                </div>

                <!-- 검색창 -->
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    <input type="text" id="notice-search" oninput="filterNotices()" placeholder="공지사항 검색어 입력..." class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none focus:ring-2 focus:ring-sprout-100 transition-all" />
                </div>
            </div>

            <!-- 아코디언 컨테이너 -->
            <div id="notice-container" class="border border-gray-100 rounded-3xl overflow-hidden divide-y divide-gray-100 shadow-sm">
                <!-- JS를 통해 렌더링 -->
            </div>
        </div>
    </section>

    <!-- DIRECTIONS & CONTACT (오시는 길 - SVG 그래픽 맵 포함) -->
    <section id="directions" class="py-20 sm:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-widest text-sprout-600">Directions & Map</h2>
                <p class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">새싹병원 오시는 길 및 주차</p>
                <p class="text-gray-600">지하철 2호선 새싹역 4번 출구에서 보행자 기준 3분 도보거리 빌딩에 입주해 있습니다.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-stretch">
                <!-- 약도 그래픽 영역 -->
                <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-gray-200/60 shadow-sm flex flex-col justify-between space-y-4 min-h-[400px]">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-800 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-sprout-500"></span> 새싹병원 디지털 로컬 약도
                        </span>
                        <span class="text-xs text-gray-400">지하철 출구 상세</span>
                    </div>
                    
                    <!-- SVG 약도 일러스트 -->
                    <div class="relative flex-grow bg-slate-100/60 rounded-2xl overflow-hidden border border-slate-100 flex items-center justify-center min-h-[280px]">
                        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg">
                            <rect width="800" height="400" fill="#f8fafc" />
                            <rect x="0" y="240" width="800" height="60" fill="#e2e8f0" />
                            <rect x="250" y="0" width="70" height="400" fill="#e2e8f0" />
                            <rect x="30" y="30" width="180" height="150" rx="12" fill="#dcfce7" />
                            <text x="120" y="110" font-family="sans-serif" font-size="14" font-weight="bold" fill="#15803d" text-anchor="middle">새싹 푸른 어린이 공원</text>
                            
                            <rect x="350" y="40" width="140" height="140" rx="12" fill="#ffffff" stroke="#f1f5f9" stroke-width="2" />
                            <text x="420" y="110" font-family="sans-serif" font-size="13" font-weight="bold" fill="#475569" text-anchor="middle">종합금융센터 빌딩</text>
                            
                            <rect x="520" y="40" width="180" height="140" rx="12" fill="#ecfdf5" stroke="#10b981" stroke-width="2" />
                            <text x="610" y="100" font-family="sans-serif" font-size="16" font-weight="extrabold" fill="#047857" text-anchor="middle">새싹빌딩 (2-3F)</text>
                            <text x="610" y="125" font-family="sans-serif" font-size="13" font-weight="bold" fill="#059669" text-anchor="middle">🌱 새싹병원</text>
                            
                            <rect x="220" y="330" width="130" height="50" rx="10" fill="#3b82f6" />
                            <text x="285" y="360" font-family="sans-serif" font-size="13" font-weight="bold" fill="#ffffff" text-anchor="middle">2호선 새싹역</text>
                            
                            <circle cx="310" cy="270" r="14" fill="#3b82f6" />
                            <text x="310" y="274" font-family="sans-serif" font-size="11" font-weight="extrabold" fill="#ffffff" text-anchor="middle">4</text>
                            
                            <path d="M 310 270 L 310 210 L 610 210 L 610 180" fill="none" stroke="#059669" stroke-width="3" stroke-dasharray="6,6" />
                            <g transform="translate(610, 80)">
                                <circle cx="0" cy="-30" r="15" fill="#ef4444" />
                                <path d="M 0 -15 L -15 -30 L 15 -30 Z" fill="#ef4444" />
                                <circle cx="0" cy="-30" r="6" fill="#ffffff" />
                            </g>
                        </svg>
                    </div>
                </div>

                <!-- 교통 설명 안내 컬럼 -->
                <div class="lg:col-span-5 flex flex-col justify-between space-y-6">
                    <div class="bg-gradient-to-tr from-sprout-700 to-sprout-900 text-white p-6 rounded-3xl shadow-md">
                        <h4 class="font-bold text-lg mb-3">병원 소재지 정보</h4>
                        <p class="text-sm leading-relaxed opacity-90">
                            주소: 서울시 새싹구 건강로 123 (새싹빌딩 2층 원무과 접수처, 3층 정밀외래진료실)<br />
                            문의 대표전화: 1588-0000
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
                        <div class="bg-blue-500 text-white rounded-full p-2.5 flex items-center justify-center font-bold text-sm h-10 w-10 shrink-0">2</div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">지하철 2호선 역세권 내원</h4>
                            <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                2호선 새싹역 4번 출구에서 하차 후 150m 직진 후 새싹어린이공원 사거리 방면에서 금융빌딩을 끼고 우측 진입 후 2층 새싹병원으로 입장
                            </p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
                        <div class="bg-emerald-500 text-white rounded-full p-2.5 flex items-center justify-center text-sm h-10 w-10 shrink-0">🚌</div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">일반 대중교통 버스 정류장</h4>
                            <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                새싹역·건강로 중앙 버스정류장 정차<br />
                                - 간선버스: 100번, 200번, 300번<br />
                                - 지선버스: 1122번, 2233번
                            </p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
                        <div class="bg-amber-500 text-white rounded-full p-2.5 flex items-center justify-center text-sm h-10 w-10 shrink-0">🚗</div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">내원 환자 자가용 무료 주차</h4>
                            <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                빌딩 지하 주차장 전체 구역 이용 가능. 진료비 혜택 증빙 시 원무과 접수처에서 무료 2시간 주차 쿠폰을 등록해 드립니다.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- 공통 예약 완료 알림 팝업 모달 -->
<div id="booking-success-modal" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative border border-gray-100">
        <div class="text-center space-y-4">
            <div class="w-16 h-16 bg-sprout-100 text-sprout-600 rounded-full flex items-center justify-center mx-auto text-3xl">🎉</div>
            <h3 class="text-2xl font-bold text-gray-900">진료 예약 접수 완료</h3>
            <p class="text-sm text-gray-500">진료 신청 예약 내용이 정상 접수되었습니다. 세션 현황판에서 조회가 가능합니다.</p>
            <div class="bg-gray-50 p-4 rounded-xl text-left text-xs space-y-2">
                <div class="flex justify-between"><span>성함:</span><span id="ticket-name" class="font-bold text-gray-900"></span></div>
                <div class="flex justify-between"><span>예약 진료과:</span><span id="ticket-dept" class="font-bold text-sprout-700"></span></div>
                <div class="flex justify-between"><span>예약 시간:</span><span id="ticket-time" class="font-bold text-gray-900"></span></div>
            </div>
            <button onclick="closeBookingModal()" class="w-full bg-sprout-600 hover:bg-sprout-700 text-white font-bold py-3 rounded-xl">확인</button>
        </div>
    </div>
</div>

<?php 
// 푸터 공통 모듈 인클루드
include_once 'includes/footer.php'; 
?>