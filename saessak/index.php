<?php 
// PHP 인클루드 파일을 통한 구역화 모듈 연동
include_once 'includes/header.php'; 
?>

<main class="flex-grow">
    <section id="hero" class="relative overflow-hidden bg-gradient-to-tr from-sprout-100 via-white to-sprout-50 py-16 lg:py-28">
        <div class="absolute top-20 right-[-10%] w-96 h-96 bg-sprout-200/50 rounded-full mix-blend-multiply filter blur-3xl opacity-60"></div>
        <div class="absolute bottom-10 left-[-5%] w-80 h-80 bg-emerald-100/40 rounded-full mix-blend-multiply filter blur-3xl opacity-60"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-sprout-100 text-sprout-800">
                        🌱 당신의 건강에 언제나 푸른 봄날을
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-gray-950 tracking-tight leading-[1.15] sm:leading-[1.15]">
                        믿을 수 있는 의료진과<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-sprout-600 to-emerald-700">스마트한 통합 케어</span>
                    </h1>
                    <p class="text-base sm:text-lg text-gray-600 font-medium max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        새싹병원은 첨단 전산 진료 예약 시스템을 통해 대기 시간을 혁신적으로 단축합니다. 지역 사회의 건강 거점으로서 환자 중심의 따뜻한 의료를 실천합니다.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4 pt-2">
                        <a href="#booking" class="px-8 py-4 bg-sprout-600 hover:bg-sprout-700 text-white font-bold rounded-2xl shadow-lg shadow-sprout-200 text-center transition-all transform hover:-translate-y-0.5 text-sm sm:text-base">
                            📅 실시간 간편 외래접수
                        </a>
                        <a href="html/about.html" class="px-8 py-4 bg-white hover:bg-gray-50 text-gray-800 font-bold rounded-2xl border border-gray-200/80 text-center transition-all shadow-sm text-sm sm:text-base">
                            🏥 병원 소개 및 조직도
                        </a>
                    </div>

                    <div class="pt-8 grid grid-cols-3 gap-4 border-t border-gray-200/60 max-w-md mx-auto lg:mx-0">
                        <div class="text-center lg:text-left space-y-0.5">
                            <span class="block text-2xl font-black text-gray-950 tracking-tight">24H</span>
                            <span class="block text-xs text-gray-400 font-medium">실시간 접수 가동</span>
                        </div>
                        <div class="text-center lg:text-left space-y-0.5">
                            <span class="block text-2xl font-black text-gray-950 tracking-tight">5개</span>
                            <span class="block text-xs text-gray-400 font-medium">전문의 정밀 분과</span>
                        </div>
                        <div class="text-center lg:text-left space-y-0.5">
                            <span class="block text-2xl font-black text-gray-950 tracking-tight">10분</span>
                            <span class="block text-xs text-gray-400 font-medium">평균 진료 대기 감소</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 relative">
                    <div class="absolute inset-0 bg-gradient-to-tr from-sprout-400/20 to-emerald-400/10 rounded-[2.5rem] transform rotate-3 scale-105 filter blur-sm"></div>
                    <div class="relative bg-white border border-gray-100 rounded-[2.5rem] p-4 sm:p-6 shadow-2xl space-y-6">
                        <div class="bg-slate-900 rounded-[2rem] h-56 sm:h-64 overflow-hidden relative group">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent z-10"></div>
                            <img src="https://images.unsplash.com/photo-1587351021759-3e566b6af7cc?auto=format&fit=crop&w=800&q=80" alt="새싹병원 전경" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                            
                            <div class="absolute top-4 left-4 z-20 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-full shadow-sm flex items-center gap-2">
                                <span class="flex h-2 w-2 relative">
                                    <span id="status-ping" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span id="status-dot" class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span id="realtime-status-text" class="text-[11px] font-bold text-emerald-700">● 진료중 | 연장 운영 판별중</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="bg-sprout-50/50 border border-sprout-100/60 p-4 rounded-2xl flex items-center gap-4">
                                <div class="w-10 h-10 bg-sprout-600 rounded-xl text-white flex items-center justify-center font-bold text-lg shadow-md shadow-sprout-100">📞</div>
                                <div>
                                    <span class="block text-[10px] text-sprout-700 font-bold uppercase tracking-wider">Emergency Call</span>
                                    <span class="block text-base font-extrabold text-gray-900">02-1234-5678</span>
                                </div>
                            </div>
                            <div class="bg-slate-50 border border-slate-200/60 p-4 rounded-2xl flex items-center gap-4">
                                <div class="w-10 h-10 bg-slate-800 rounded-xl text-white flex items-center justify-center font-bold text-lg shadow-md">📍</div>
                                <div>
                                    <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Hospital Location</span>
                                    <span class="block text-xs font-bold text-gray-800 leading-tight">서울특별사 새싹구 푸른숲로 77 (새싹역 4번출구)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="departments" class="py-20 bg-white border-t border-gray-100/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-14">
            <div class="text-center max-w-3xl mx-auto space-y-3">
                <span class="text-xs font-bold text-sprout-600 uppercase tracking-widest">Medical Services</span>
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">특화된 5대 전문 진료 분과</h2>
                <p class="text-sm sm:text-base text-gray-500 font-medium">각 분과별 풍부한 임상 경험을 가진 전문의가 상주하여 정밀 진단과 맞춤형 치료를 약속합니다.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100/80 flex flex-col justify-between space-y-6 transition-all hover:shadow-xl hover:bg-white group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-md border border-gray-100 flex items-center justify-center text-2xl group-hover:bg-sprout-600 group-hover:text-white transition-colors">🩺</div>
                        <div class="space-y-1.5">
                            <h3 class="font-bold text-gray-900 text-base">가정의학과</h3>
                            <p class="text-xs text-gray-500 leading-relaxed">만성질환, 생활습관병 관리 및 온 가족 평생 주치의 전담 케어</p>
                        </div>
                    </div>
                    <button onclick="preselectDept('가정의학과')" class="w-full py-2 bg-white hover:bg-sprout-50 border border-gray-200 text-xs font-bold text-gray-700 hover:text-sprout-600 rounded-xl transition-all">바로 예약</button>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100/80 flex flex-col justify-between space-y-6 transition-all hover:shadow-xl hover:bg-white group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-md border border-gray-100 flex items-center justify-center text-2xl group-hover:bg-sprout-600 group-hover:text-white transition-colors">🧬</div>
                        <div class="space-y-1.5">
                            <h3 class="font-bold text-gray-900 text-base">일반내과</h3>
                            <p class="text-xs text-gray-500 leading-relaxed">고혈압, 당뇨, 소화기 질환 및 초음파 정밀 영상 진단</p>
                        </div>
                    </div>
                    <button onclick="preselectDept('내과')" class="w-full py-2 bg-white hover:bg-sprout-50 border border-gray-200 text-xs font-bold text-gray-700 hover:text-sprout-600 rounded-xl transition-all">바로 예약</button>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100/80 flex flex-col justify-between space-y-6 transition-all hover:shadow-xl hover:bg-white group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-md border border-gray-100 flex items-center justify-center text-2xl group-hover:bg-sprout-600 group-hover:text-white transition-colors">🦴</div>
                        <div class="space-y-1.5">
                            <h3 class="font-bold text-gray-900 text-base">정형외과</h3>
                            <p class="text-xs text-gray-500 leading-relaxed">척추 관절 비수술 보존 치료 및 맞춤형 도수·치료 연계</p>
                        </div>
                    </div>
                    <button onclick="preselectDept('정형외과')" class="w-full py-2 bg-white hover:bg-sprout-50 border border-gray-200 text-xs font-bold text-gray-700 hover:text-sprout-600 rounded-xl transition-all">바로 예약</button>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100/80 flex flex-col justify-between space-y-6 transition-all hover:shadow-xl hover:bg-white group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-md border border-gray-100 flex items-center justify-center text-2xl group-hover:bg-sprout-600 group-hover:text-white transition-colors">👶</div>
                        <div class="space-y-1.5">
                            <h3 class="font-bold text-gray-900 text-base">소아청소년과</h3>
                            <p class="text-xs text-gray-500 leading-relaxed">영유아 국가 무료 예방접종 및 소아 급성기 질환 안심 치료</p>
                        </div>
                    </div>
                    <button onclick="preselectDept('소아청소년과')" class="w-full py-2 bg-white hover:bg-sprout-50 border border-gray-200 text-xs font-bold text-gray-700 hover:text-sprout-600 rounded-xl transition-all">바로 예약</button>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100/80 flex flex-col justify-between space-y-6 transition-all hover:shadow-xl hover:bg-white group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-white rounded-xl shadow-md border border-gray-100 flex items-center justify-center text-2xl group-hover:bg-sprout-600 group-hover:text-white transition-colors">🏥</div>
                        <div class="space-y-1.5">
                            <h3 class="font-bold text-gray-900 text-base">종합건진센터</h3>
                            <p class="text-xs text-gray-500 leading-relaxed">국민건강보험 공단 검진 및 생애주기별 맞춤 정밀 종합 검진</p>
                        </div>
                    </div>
                    <button onclick="preselectDept('종합건진센터')" class="w-full py-2 bg-white hover:bg-sprout-50 border border-gray-200 text-xs font-bold text-gray-700 hover:text-sprout-600 rounded-xl transition-all">바로 예약</button>
                </div>
            </div>
        </div>
    </section>

    <section id="doctors" class="py-20 bg-slate-50 border-t border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-14">
            <div class="text-center max-w-3xl mx-auto space-y-3">
                <span class="text-xs font-bold text-sprout-600 uppercase tracking-widest">Medical Staff</span>
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">새싹병원의 전문 의료진을 소개합니다</h2>
                <p class="text-sm sm:text-base text-gray-500 font-medium">대학병원 출신의 숙련된 분야별 의료진이 환자 한 분 한 분께 정성을 다합니다.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white rounded-3xl border border-gray-200/60 shadow-sm overflow-hidden flex flex-col">
                    <div class="h-64 bg-slate-100 relative overflow-hidden">
                        <img src="assets/images/doc_pulmo_01" alt="김주은 원장" onerror="handleImageError(this)" class="doctor-photo w-full h-full object-cover">
                        <div class="doctor-fallback hiddne w-full h-full bg-gradient-to-br from-sprout-600 to-emerald-800 flex items-center justify-center text-white text-5xl">🫁</div>
                    </div>
                    <div class="p-6 space-y-4 flex-grow flex flex-col justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-sprout-600 uppercase tracking-wide">호흡기내과 전문의</span>
                            <h3 class="text-lg font-bold text-gray-900">김주은 원장</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pt-1">연세대학교 의과대학 졸업<br>전 세브란스병원 임상교수</p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl text-[11px] text-gray-600 font-medium leading-normal">
                            "평생의 건강 동반자로서 질병 예방부터 만성질환까지 꼼꼼히 살피겠습니다."
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-gray-200/60 shadow-sm overflow-hidden flex flex-col">
                    <div class="h-64 bg-slate-100 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=500&q=80" alt="박건우 과장" onerror="handleImageError(this)" class="doctor-photo w-full h-full object-cover">
                        <div class="doctor-fallback hidden w-full h-full bg-gradient-to-br from-sprout-600 to-emerald-800 flex items-center justify-center text-white text-5xl">🧬</div>
                    </div>
                    <div class="p-6 space-y-4 flex-grow flex flex-col justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-sprout-600 uppercase tracking-wide">소화기내과 분과전문의</span>
                            <h3 class="text-lg font-bold text-gray-900">박건우 진료과장</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pt-1">서울대학교 의과대학 졸업<br>전 서울대병원 전임의</p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl text-[11px] text-gray-600 font-medium leading-normal">
                            "정밀한 영상 검진을 바탕으로 소화기 질환의 조기 발견에 힘쓰겠습니다."
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-gray-200/60 shadow-sm overflow-hidden flex flex-col">
                    <div class="h-64 bg-slate-100 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=500&q=80" alt="최시환 과장" onerror="handleImageError(this)" class="doctor-photo w-full h-full object-cover">
                        <div class="doctor-fallback hidden w-full h-full bg-gradient-to-br from-sprout-600 to-emerald-800 flex items-center justify-center text-white text-5xl">🦴</div>
                    </div>
                    <div class="p-6 space-y-4 flex-grow flex flex-col justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wide">정형외과 전문의 [신규초빙]</span>
                            <h3 class="text-lg font-bold text-gray-900">최시환 진료과장</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pt-1">고려대학교 의과대학 졸업<br>대한정형외과학회 정회원</p>
                        </div>
                        <div class="bg-amber-50/50 p-3 rounded-xl text-[11px] text-amber-900/80 font-medium leading-normal">
                            "수술 부담을 낮추는 비수술 보존적 치료로 관절의 활력을 되찾아 드립니다."
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-gray-200/60 shadow-sm overflow-hidden flex flex-col">
                    <div class="h-64 bg-slate-100 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1594824813573-246434e33963?auto=format&fit=crop&w=500&q=80" alt="김유진 과장" onerror="handleImageError(this)" class="doctor-photo w-full h-full object-cover">
                        <div class="doctor-fallback hidden w-full h-full bg-gradient-to-br from-sprout-600 to-emerald-800 flex items-center justify-center text-white text-5xl">🧠</div>
                    </div>
                    <div class="p-6 space-y-4 flex-grow flex flex-col justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-sprout-600 uppercase tracking-wide">신경과 전문의</span>
                            <h3 class="text-lg font-bold text-gray-900">김유진 진료과장</h3>
                            <p class="text-xs text-gray-500 leading-relaxed pt-1">가톨릭대학교 의과대학 졸업<br>전 서울성모병원 신경과 진료의</p>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl text-[11px] text-gray-600 font-medium leading-normal">
                            "내 부모님을 진료하는 자식의 마음으로 따뜻하고 명쾌하게 진료하겠습니다."
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="schedule" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-14">
            <div class="text-center max-w-3xl mx-auto space-y-3">
                <span class="text-xs font-bold text-sprout-600 uppercase tracking-widest">Medical Hours</span>
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">진료 시간 및 상세 일정안내</h2>
                <p class="text-sm sm:text-base text-gray-500 font-medium">새싹병원은 화요일과 목요일에 직장인들을 위한 야간 연장 진료를 시행합니다.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="bg-slate-900 text-white rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl relative overflow-hidden">
                    <div class="absolute right-[-10%] bottom-[-10%] text-white/5 font-black text-9xl pointer-events-none select-none">Time</div>
                    <h3 class="text-xl font-bold border-b border-white/10 pb-4">⏰ 표준 진료 운영시간</h3>
                    <div class="space-y-3.5 text-sm font-medium text-slate-300">
                        <div class="flex justify-between"><span>평일 (월~금)</span><span class="text-white font-bold">09:00 ~ 18:00</span></div>
                        <div class="flex justify-between items-center">
                            <span>화/목 야간진료</span>
                            <span class="bg-amber-500 text-slate-950 font-extrabold text-[10px] px-2 py-0.5 rounded-md">20:00까지 연장</span>
                        </div>
                        <div class="flex justify-between"><span>토요일 진료</span><span class="text-white font-bold">09:00 ~ 13:00 (오전)</span></div>
                        <div class="flex justify-between border-t border-white/10 pt-3 text-slate-400"><span>점심 시간</span><span>13:00 ~ 14:00 (60분)</span></div>
                        <div class="flex justify-between text-red-400 font-semibold"><span>일요일 / 공휴일</span><span>정기 휴진</span></div>
                    </div>
                    <div class="bg-white/10 p-4 rounded-xl text-xs text-slate-400 leading-normal">
                        ⚠️ 접수 마감은 진료 종료 30분 전까지이오니 원활한 외래 가접수를 위해 미전산 등록 시 서둘러 내원 바랍니다.
                    </div>
                </div>

                <div class="lg:col-span-2 bg-gray-50 border border-gray-200/70 rounded-3xl p-6 overflow-x-auto shadow-sm">
                    <table class="w-full text-center text-xs sm:text-sm border-collapse min-w-[500px]">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-400 font-bold">
                                <th class="py-3 text-left pl-4 font-bold">진료과 / 의료진</th>
                                <th class="py-3 font-bold">월</th>
                                <th class="py-3 font-bold">화</th>
                                <th class="py-3 font-bold">수</th>
                                <th class="py-3 font-bold text-sprout-600">목 (야간)</th>
                                <th class="py-3 font-bold">금</th>
                                <th class="py-3 font-bold text-amber-600">토 (오전)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200/60 font-medium text-gray-700">
                            <tr class="hover:bg-white transition-colors">
                                <td class="py-4 text-left pl-4 font-bold text-gray-900">가정의학과 (김새싹)</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-gray-300">휴진</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">교대</td>
                            </tr>
                            <tr class="hover:bg-white transition-colors">
                                <td class="py-4 text-left pl-4 font-bold text-gray-900">일반내과 (박건우)</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-gray-300">휴진</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">교대</td>
                            </tr>
                            <tr class="hover:bg-white transition-colors">
                                <td class="py-4 text-left pl-4 font-bold text-gray-900">정형외과 (최태양)</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">야간</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-gray-300">휴진</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                            </tr>
                            <tr class="hover:bg-white transition-colors">
                                <td class="py-4 text-left pl-4 font-bold text-gray-900">소아청소년과 (이지민)</td>
                                <td class="py-4 text-gray-300">휴진</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                                <td class="py-4 text-emerald-600 font-bold">●</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section id="notice" class="py-20 bg-slate-50 border-t border-b border-gray-200/60">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-10">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="space-y-2 text-center sm:text-left">
                    <span class="text-xs font-bold text-sprout-600 uppercase tracking-widest">Hospital News</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">새싹 원내 소식 및 공지사항</h2>
                </div>
                <div class="relative max-w-xs w-full mx-auto sm:mx-0">
                    <input type="text" id="notice-search" oninput="filterNotices()" placeholder="공지사항 제목, 내용 검색..." class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-sprout-500 bg-white shadow-sm">
                    <span class="absolute right-3.5 top-3 text-sm grayscale opacity-60">🔍</span>
                </div>
            </div>

            <div id="notice-container" class="bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden divide-y divide-gray-100">
                </div>
        </div>
    </section>

    <section id="booking" class="py-20 sm:py-24 bg-gradient-to-b from-slate-50 to-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-slate-900 px-6 py-8 sm:px-10 text-white text-center space-y-2 relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-sprout-800 to-emerald-950 opacity-90"></div>
                    <div class="relative z-10 space-y-1">
                        <span class="text-[10px] font-bold text-sprout-300 uppercase tracking-widest">Realtime Booking System</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">간편 외래 접수 및 진료 예약</h3>
                        <p class="text-xs text-sprout-100/70">원하시는 진료과와 일정을 선택하시면 원무 전산망에 실시간으로 가접수됩니다.</p>
                    </div>
                </div>

                <form id="booking-form" onsubmit="if(!IS_LOGGED_IN) { event.preventDefault(); alert('🚨 로그인 후 사용이 가능합니다. 로그인 또는 회원가입을 진행해 주세요.'); toggleAuthModal('login'); return false; }" class="p-6 sm:p-10 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2">환자 성함</label>
                            <input type="text" required placeholder="성함을 입력하세요" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500 bg-gray-50/50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2">비상 연락처</label>
                            <input type="tel" required placeholder="010-0000-0000" class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500 bg-gray-50/50">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2">희망 진료 과목</label>
                            <select id="book-dept" required class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500 bg-gray-50/50">
                                <option value="" disabled selected>선택해 주세요</option>
                                <option value="호흡기내과">호흡기내과 (김주은 원장)</option>
                                <option value="내과">내과 (박건우 과장)</option>
                                <option value="정형외과">정형외과 ( 과장)</option>
                                <option value="소아청소년과">신경과 (김유진 과장)</option>
                                <!-- <option value="종합건진센터">종합건진센터 (한무진 소장)</option> -->
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2">진료 희망일</label>
                            <input type="date" id="book-date" required class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500 bg-gray-50/50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2">희망 시간대</label>
                            <select required class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500 bg-gray-50/50">
                                <option value="" disabled selected>시간대 선택</option>
                                <option value="오전 (09:00 ~ 12:30)">오전 (09:00 ~ 12:30)</option>
                                <option value="오후 (14:00 ~ 17:30)">오후 (14:00 ~ 17:30)</option>
                                <option value="야간연장 (18:00 ~ 19:40)">야간연장 (화/목 전용)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">증상 요약 및 전달사항 (선택)</label>
                        <textarea rows="3" placeholder="내원 목적이나 앓고 계신 증상을 간단히 메모해 주시면 진료에 큰 도움이 됩니다." class="w-full px-4 py-3.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-sprout-500 bg-gray-50/50 resize-none"></textarea>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl flex items-start gap-3">
                        <input type="checkbox" id="agree-privacy" required class="mt-0.5 rounded text-sprout-600 focus:ring-sprout-500">
                        <label for="agree-privacy" class="text-xs text-gray-500 leading-normal cursor-pointer select-none">
                            새싹병원 의료시스템 가접수를 위한 <span class="text-gray-800 font-bold">개인정보 수집 및 고유식별정보 처리 동의서</span>의 내용을 충분히 숙지하였으며 이에 동의합니다.
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-sprout-600 hover:bg-sprout-700 text-white font-extrabold py-4 rounded-2xl shadow-lg shadow-sprout-100 tracking-wide transition-all text-sm sm:text-base">
                        🔒 실시간 전산 접수 및 예약 완료하기
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

</main>

<!-- 진료 예약 완료 팝업 모달창 구역 -->
<div id="booking-success-modal" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative border border-gray-100">
        <div class="text-center space-y-4">
            <div class="w-16 h-16 bg-sprout-100 text-sprout-600 rounded-full flex items-center justify-center mx-auto text-3xl">🎉</div>
            <h3 class="text-2xl font-bold text-gray-900">진료 예약 접수 완료</h3>
            <p class="text-sm text-gray-500">진료 신청 예약 내용이 정상 접수되었습니다. 세션 현황판에서 조회가 가능합니다.</p>
            <div class="bg-gray-50 p-4 rounded-xl text-left text-xs space-y-2">
                <div class="flex justify-between"><span>성함:</span><span id="ticket-name" class="font-bold text-gray-900"></span></div>
                <div class="flex justify-between"><span>예약 진료과:</span><span id="ticket-dept" class="font-bold text-gray-900"></span></div>
                <div class="flex justify-between"><span>희망 일정:</span><span id="ticket-date" class="font-bold text-gray-900"></span></div>
            </div>
            <button onclick="document.getElementById('booking-success-modal').classList.add('hidden')" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-xl transition-all text-sm">
                확인 및 닫기
            </button>
        </div>
    </div>
</div>

<?php 
// 전산 연동용 하단 필수 인터랙션 스크립트 모듈 인클루드
include_once 'includes/footer.php'; 
?>