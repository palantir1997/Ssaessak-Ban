<!-- FOOTER -->
    <footer class="bg-gray-900 text-gray-400 py-12 border-t border-gray-800 text-xs sm:text-sm mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                <!-- 로고 및 이념 컬럼 -->
                <div class="md:col-span-4 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🌱</span>
                        <span class="text-white font-bold text-lg">새싹병원</span>
                    </div>
                    <p class="leading-relaxed">
                        새싹병원은 든든한 평생 건강 관리 주치의로서 질병의 근본적 치유부터 철저한 사전 예방관리까지 최상의 가치를 전하는 우리동네 안심 1차 의료기관입니다.
                    </p>
                </div>

                <!-- 병원 주소 및 정보 컬럼 -->
                <div class="md:col-span-5 space-y-2">
                    <h4 class="text-white font-semibold text-sm">새싹병원 의료법인 정보</h4>
                    <p>대표자: 김새싹 | 사업자등록번호: 123-45-67890</p>
                    <p>주소: 서울시 새싹구 건강로 123 새싹빌딩 2층, 3층</p>
                    <p>전화: 1588-0000 | 이메일: support@sprouthospital.co.kr</p>
                </div>

                <!-- 면책조항 및 안내 컬럼 -->
                <div class="md:col-span-3 space-y-2">
                    <h4 class="text-white font-semibold text-sm">이용 약관 및 공지</h4>
                    <p>© 2026 Sprout Hospital. All rights reserved.</p>
                    <p class="text-[11px] leading-relaxed text-gray-500">
                        본 사이트는 포트폴리오용 가상 병원 홈페이지입니다. 실제 의학적 응급상황은 반드시 119 및 실존 의료기관과 연계해 주십시오.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- 공통 로그인 / 회원가입 통합 모달 다이얼로그 -->
    <div id="auth-modal" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative border border-gray-100 transform scale-95 transition-transform duration-300">
            <button onclick="toggleAuthModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            
            <!-- 로그인 폼 영역 -->
            <div id="modal-login-form" class="space-y-6">
                <div class="text-center">
                    <span class="text-3xl">🌱</span>
                    <h3 class="text-2xl font-bold text-gray-900 mt-2">새싹병원 로그인</h3>
                    <p class="text-xs text-gray-500 mt-1">등록된 이메일 계정으로 안전하게 로그인하세요</p>
                </div>
                <form onsubmit="handleLoginSubmit(event)" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">이메일 주소</label>
                        <input type="email" id="login-email" required placeholder="example@email.com" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none focus:ring-2 focus:ring-sprout-100 transition-all text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">비밀번호</label>
                        <input type="password" id="login-pw" required placeholder="비밀번호를 입력하세요" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none focus:ring-2 focus:ring-sprout-100 transition-all text-sm" />
                    </div>
                    <button type="submit" class="w-full bg-sprout-600 hover:bg-sprout-700 text-white font-bold py-3.5 rounded-xl shadow-md shadow-sprout-100 transition-all text-sm">
                        로그인 완료
                    </button>
                </form>
                <div class="text-center text-xs text-gray-500">
                    아직 새싹병원 회원이 아니신가요? 
                    <button onclick="toggleAuthModal('signup')" class="text-sprout-600 font-bold hover:underline">회원가입 하기</button>
                </div>
            </div>

            <!-- 회원가입 폼 영역 -->
            <div id="modal-signup-form" class="space-y-6 hidden">
                <div class="text-center">
                    <span class="text-3xl">🌱</span>
                    <h3 class="text-2xl font-bold text-gray-900 mt-2">쉽고 빠른 회원가입</h3>
                    <p class="text-xs text-gray-500 mt-1">간편 가입으로 간편예약 내역 및 건강 상담 기록을 지키세요</p>
                </div>
                <form onsubmit="handleSignupSubmit(event)" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">이름 <span class="text-red-500">*</span></label>
                        <input type="text" id="signup-name" required placeholder="홍길동" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">이메일 주소 <span class="text-red-500">*</span></label>
                        <input type="email" id="signup-email" required placeholder="example@email.com" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none text-sm" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">비밀번호 <span class="text-red-500">*</span></label>
                            <input type="password" id="signup-pw" required placeholder="8자 이상" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">비밀번호 확인 <span class="text-red-500">*</span></label>
                            <input type="password" id="signup-pw-confirm" required placeholder="동일 입력" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">휴대폰 번호 <span class="text-red-500">*</span></label>
                        <input type="tel" id="signup-phone" required placeholder="010-1234-5678" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-sprout-500 focus:outline-none text-sm" />
                    </div>
                    <button type="submit" class="w-full bg-sprout-600 hover:bg-sprout-700 text-white font-bold py-3.5 rounded-xl shadow-md shadow-sprout-100 transition-all text-sm">
                        회원가입 완료
                    </button>
                </form>
                <div class="text-center text-xs text-gray-500">
                    이미 계정이 있으신가요? 
                    <button onclick="toggleAuthModal('login')" class="text-sprout-600 font-bold hover:underline">로그인 하기</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 스크립트 파일 연동 -->
    <script src="js/script.js"></script>
</body>
</html>