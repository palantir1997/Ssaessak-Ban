// ================= [1] 진단서 모달 열기 및 데이터 매핑 =================
function openCertificate(id, name, doctor, date, diagnosis, prescription, note) {
    // jQuery 데이터 매핑
    $('#cert_id').text(id);
    $('#cert_name').text(name);
    $('#cert_doctor').text(doctor ? doctor : '미지정'); // ⭐ 의사 데이터 매핑 추가
    $('#cert_date').text(date);
    $('#cert_diagnosis').text(diagnosis ? diagnosis : '미입력');
    $('#cert_prescription').text(prescription ? prescription : '처방 내역 없음');
    $('#cert_note').text(note ? note : '특이사항 없음');
    
    // 오늘 날짜 자동 생성해서 진단서 발행일로 표기
    const today = new Date();
    const formattedDate = `${today.getFullYear()}년 ${String(today.getMonth() + 1).padStart(2, '0')}월 ${String(today.getDate()).padStart(2, '0')}일`;
    $('#cert_today_date').text(formattedDate);

    // hidden 클래스를 지우는 대신, display 스타일을 flex로 강제 지정하여 팝업을 띄웁니다.
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

// ================= [2] 진료기록 수정(정정) 모달 제어 및 서브밋 =================

// 수정 모달 열기 및 데이터 바인딩
function openEditModal(id, name, dept, doctor, date, diagnosis, prescription, note) {
    // 1. 보안 영역 데이터 매핑 (Readonly 필드)
    $('#edit_id').val(id);
    $('#edit_name').val(name);
    $('#edit_date').val(date);
    $('#edit_doctor_dept').val(`${doctor} (${dept})`); // '홍길동 (내과)' 포맷으로 표기
    
    // 2. 수정 가능 진료 영역 데이터 매핑
    $('#edit_diagnosis').val(diagnosis);
    $('#edit_prescription').val(prescription);
    $('#edit_note').val(note);

    // 3. 모달 팝업 가시화
    $('#editChartModal').css('display', 'flex').removeClass('hidden');
}

// 수정 모달 닫기
function closeEditModal() {
    $('#editChartModal').css('display', 'none').addClass('hidden');
    $('#editChartForm')[0].reset(); // 입력 폼 초기화
}

// 수정 폼 제출 처리 (DB 연동 시뮬레이션 완료 알림)
function submitEditForm(event) {
    event.preventDefault(); // 기본 submit 페이지 새로고침 방지
    
    const chartId = $('#edit_id').val();
    const updatedDiagnosis = $('#edit_diagnosis').val();
    
    // 실제 운영 시 이 시점에 $.ajax나 $.post를 활용해 DB 업데이트 처리(SQL UPDATE)를 수행합니다.
    alert(`[보안 인증 서명 완료]\n차트번호 [ ${chartId} ]의 진단명('${updatedDiagnosis}') 외 기록 정정이 성공적으로 저장되었습니다.\n\n(※ 현재는 모크데이터 환경이므로 페이지 새로고침 시 초기화됩니다.)`);
    
    closeEditModal();
}