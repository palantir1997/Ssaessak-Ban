// 새싹병원 전반적인 프론트엔드 인터랙션 제어 스크립트

// 전역 로컬 정적 소식 데이터
const LOCAL_NOTICES = [
    {
        id: 1,
        tag: "중요",
        tagColor: "bg-red-50 text-red-600 border-red-100",
        title: "2026년도 상반기 국가 영유아 무료 예방접종 안내",
        date: "2026-05-15",
        content: "새싹병원 소아청소년과에서 질병관리청 주관 국가 무료 예방접종을 시행합니다. 내원 시 아기수첩 및 가족관계증명서를 지참하시면 빠른 접수가 가능하오니 참조 바랍니다."
    },
    {
        id: 2,
        tag: "휴진",
        tagColor: "bg-amber-50 text-amber-600 border-amber-100",
        title: "현충일(6월 6일) 법정 공휴일 휴진 안내",
        date: "2026-05-20",
        content: "6월 6일 현충일 당일에는 본원 일반 외래 진료를 임시 휴진합니다. 응급 상황 시 인근 대학병원 권역센터를 이용해 주시기 바라며, 다음 월요일부터 모든 진료과가 정상 진료합니다."
    },
    {
        id: 3,
        tag: "소식",
        tagColor: "bg-blue-50 text-blue-600 border-blue-100",
        title: "정형외과 관절 치료 권위자 최태양 과장 신규 초빙",
        date: "2026-05-01",
        content: "척추 비수술 보존 치료 및 도수치료 연계 치료 전문의이신 최태양 과장님이 부임하셨습니다. 정형외과 3진료실 증설에 따라 진료 대기가 단축되어 더욱 원활한 검사가 가능합니다."
    }
];

let activeNoticeId = null;

// 초기 바인딩 실행
window.addEventListener('DOMContentLoaded', () => {
    initRealtimeHours();
    renderNotices(LOCAL_NOTICES);

    // 모바일 전용 헤더 토글 이벤트
    const mbBtn = document.getElementById('mobile-menu-btn');
    const mbMenu = document.getElementById('mobile-menu');
    if (mbBtn && mbMenu) {
        mbBtn.addEventListener('click', () => {
            mbMenu.classList.toggle('hidden');
        });
    }

    // 날짜 선택기 기본 마지노선 구축 (과거 날짜 예약 차단)
    const dtInput = document.getElementById('book-date');
    if (dtInput) {
        const today = new Date().toISOString().split('T')[0];
        dtInput.min = today;
        dtInput.value = today;
    }
});

// 이미지 에러 방어 처리 (로컬 호스트에 업로드한 원장님 사진이 누락되었을 시, 수려한 SVG 일러스트레이터로 완벽 백업)
function handleImageError(imageEl) {
    imageEl.classList.add('hidden');
    const fallbackNode = imageEl.nextElementSibling;
    if (fallbackNode && fallbackNode.classList.contains('doctor-fallback')) {
        fallbackNode.classList.remove('hidden');
    }
}

// 회원가입 클라이언트 예방 유효성 검증
function validateSignupForm() {
    const pw = document.getElementById('signup-pw').value;
    const confirm = document.getElementById('signup-pw-confirm').value;

    if (pw.length < 8) {
        alert("🔒 비밀번호는 안전을 위해 최소 8자 이상이어야 합니다.");
        return false;
    }
    if (pw !== confirm) {
        alert("❌ 비밀번호와 비밀번호 확인 입력값이 일치하지 않습니다.");
        return false;
    }
    return true;
}

// 회원 통합 팝업 제어
function toggleAuthModal(type = null) {
    const modal = document.getElementById('auth-modal');
    const loginForm = document.getElementById('modal-login-form');
    const signupForm = document.getElementById('modal-signup-form');

    if (!type) {
        modal.classList.add('hidden');
        return;
    }

    modal.classList.remove('hidden');
    if (type === 'login') {
        loginForm.classList.remove('hidden');
        signupForm.classList.add('hidden');
    } else {
        loginForm.classList.add('hidden');
        signupForm.classList.remove('hidden');
    }
}

// 실시간 외래 운영상태 판별 계산기 (요일 및 시간대 교차 검증)
function initRealtimeHours() {
    const ping = document.getElementById('status-ping');
    const dot = document.getElementById('status-dot');
    const text = document.getElementById('realtime-status-text');

    if (!ping) return;

    function calc() {
        const now = new Date();
        const day = now.getDay(); // 0:일, 6:토
        const hour = now.getHours();
        const min = now.getMinutes();
        const timeVal = hour * 100 + min;

        let active = false;
        let desc = "";

        if (day === 0) {
            active = false;
            desc = "일요일은 정기 휴진입니다.";
        } else if (day === 6) {
            if (timeVal >= 900 && timeVal < 1300) {
                active = true;
                desc = "토요일 오전 정상 진료 중 (13:00 마감)";
            } else {
                active = false;
                desc = "토요일 외래 진료가 종료되었습니다.";
            }
        } else {
            const isNightDay = (day === 2 || day === 4); // 화, 목 야간
            if (timeVal >= 900 && timeVal < 1300) {
                active = true;
                desc = "평일 오전 외래 진료 중";
            } else if (timeVal >= 1300 && timeVal < 1400) {
                active = false;
                desc = "전체 진료과 점심 휴진 시간 (14:00 진료 재개)";
            } else if (timeVal >= 1400 && timeVal < 1800) {
                active = true;
                desc = "평일 오후 외래 진료 중";
            } else if (isNightDay && timeVal >= 1800 && timeVal < 2000) {
                active = true;
                desc = "화/목 야간 연장 진료 중 (20:00 마감)";
            } else {
                active = false;
                desc = "금일 진료가 모두 마감되었습니다.";
            }
        }

        if (active) {
            ping.className = "animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75";
            dot.className = "relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500";
            text.className = "text-sm font-bold text-emerald-600";
            text.textContent = `● 진료중 | ${desc}`;
        } else {
            ping.className = "hidden";
            dot.className = "relative inline-flex rounded-full h-3.5 w-3.5 bg-red-400";
            text.className = "text-sm font-bold text-red-500";
            text.textContent = `● 진료종료 | ${desc}`;
        }
    }

    calc();
    setInterval(calc, 30000);
}

// 카드 선택 시 예약 폼 자동 타게팅 매칭
function preselectDept(deptName) {
    const select = document.getElementById('book-dept');
    if (select) {
        select.value = deptName;
        // 자연스럽게 예약 카드 방향으로 자동 스크롤 이동
        const target = document.getElementById('booking');
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    }
}

// 아코디언 공지사항 렌더링
function renderNotices(listData) {
    const container = document.getElementById('notice-container');
    if (!container) return;
    container.innerHTML = "";

    if (listData.length === 0) {
        container.innerHTML = `
            <div class="p-10 text-center text-gray-400">
                <span class="text-2xl block">🔍</span>
                <p class="text-xs mt-2">일치하는 공지 또는 검색 결과가 없습니다.</p>
            </div>
        `;
        return;
    }

    listData.forEach(item => {
        const isOpen = activeNoticeId === item.id;
        const div = document.createElement('div');
        div.className = "bg-white transition-colors hover:bg-slate-50/50";

        div.innerHTML = `
            <button onclick="toggleNoticeAccordion(${item.id})" class="w-full px-6 py-5 flex items-center justify-between text-left focus:outline-none">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 flex-grow pr-4">
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold border ${item.tagColor} shrink-0 max-w-[50px] text-center">
                        ${item.tag}
                    </span>
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base line-clamp-1">${item.title}</h3>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-xs text-gray-400">${item.date}</span>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform ${isOpen ? 'rotate-180 text-sprout-600' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </button>
            <div class="transition-all duration-300 overflow-hidden ${isOpen ? 'max-h-[300px] border-t border-gray-100 bg-slate-50/30' : 'max-h-0'}">
                <div class="p-6 text-xs sm:text-sm text-gray-600 leading-relaxed space-y-2">
                    <p class="whitespace-pre-line">${item.content}</p>
                </div>
            </div>
        `;
        container.appendChild(div);
    });
}

function toggleNoticeAccordion(id) {
    activeNoticeId = (activeNoticeId === id) ? null : id;
    filterNotices();
}

function filterNotices() {
    const val = document.getElementById('notice-search').value.toLowerCase().trim();
    const filtered = LOCAL_NOTICES.filter(x => {
        return x.title.toLowerCase().includes(val) || x.content.toLowerCase().includes(val) || x.tag.toLowerCase().includes(val);
    });
    renderNotices(filtered);
}