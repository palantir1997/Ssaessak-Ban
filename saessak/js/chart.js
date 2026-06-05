// 1. 진단서 모달 열기 및 데이터 매핑
function openCertificate(id, name, date, diagnosis, prescription, note) {
    // jQuery 데이터 매핑
    $('#cert_id').text(id);
    $('#cert_name').text(name);
    $('#cert_date').text(date);
    $('#cert_diagnosis').text(diagnosis ? diagnosis : '미입력');
    $('#cert_prescription').text(prescription ? prescription : '처방 내역 없음');
    $('#cert_note').text(note ? note : '특이사항 없음');
    
    // 오늘 날짜 자동 생성해서 진단서 발행일로 표기
    const today = new Date();
    const formattedDate = `${today.getFullYear()}년 ${String(today.getMonth() + 1).padStart(2, '0')}월 ${String(today.getDate()).padStart(2, '0')}일`;
    $('#cert_today_date').text(formattedDate);

    // ⭐ hidden 클래스를 지우는 대신, display 스타일을 flex로 강제 지정하여 팝업을 띄웁니다.
    $('#certificateModal').css('display', 'flex').removeClass('hidden');
}

// 2. 진단서 모달 닫기
function closeCertModal() {
    // ⭐ 모달을 숨깁니다.
    $('#certificateModal').css('display', 'none').addClass('hidden');
}

// 3. 진단서 출력 기능
function printCertificate() {
    window.print();
}