// ================= [1] 진단서 상세 보기 모달 열기 및 데이터 매핑 =================
function openCertificate(id, name, doctor, date) {
    const targetRow = $(`tr[data-id="${id}"]`);
    const currentDiagnosis = targetRow.find('td').eq(6).text();
    const currentPrescription = targetRow.find('td').eq(7).text();
    const currentNotes = targetRow.find('td').eq(8).text();

    $('#cert_id').text(id);
    $('#cert_name').text(name);
    $('#cert_doctor').text(doctor ? doctor : '미지정'); 
    $('#cert_date').text(date);
    
    $('#cert_diagnosis').text(currentDiagnosis ? currentDiagnosis : '미입력');
    $('#cert_prescription').text(currentPrescription ? currentPrescription : '처방 내역 없음');
    $('#cert_notes').text(currentNotes ? currentNotes : '특이사항 없음');
    
    const today = new Date();
    const formattedDate = `${today.getFullYear()}년 ${String(today.getMonth() + 1).padStart(2, '0')}월 ${String(today.getDate()).padStart(2, '0')}일`;
    $('#cert_today_date').text(formattedDate);

    $('#certificateModal').css('display', 'flex').removeClass('hidden');
}

function closeCertModal() {
    $('#certificateModal').css('display', 'none').addClass('hidden');
}

function printCertificate() {
    window.print();
}

// ================= [2] 진료기록 수정(정정) 모달 제어 =================
function openEditModal(id, name, dept, doctor, date) {
    const targetRow = $(`tr[data-id="${id}"]`);

    $('#edit_id').val(id);
    $('#edit_name').val(name);
    $('#edit_date').val(date);
    $('#edit_doctor_dept').val(`${doctor} (${dept})`); 
    
    $('#edit_diagnosis').val(targetRow.find('td').eq(6).text());
    $('#edit_prescription').val(targetRow.find('td').eq(7).text());
    $('#edit_notes').val(targetRow.find('td').eq(8).text());

    $('#editChartModal').css('display', 'flex').removeClass('hidden');
}

function closeEditModal() {
    $('#editChartModal').css('display', 'none').addClass('hidden');
    $('#editChartForm')[0].reset();
}

// ================= [3] 우분투 MySQL AJAX 비동기 전송 처리 =================
$(document).ready(function() {
    $('#editChartForm').on('submit', function(event) {
        event.preventDefault();
        
        const chartNo = $('#edit_id').val();
        const updatedDiagnosis = $('#edit_diagnosis').val();
        const updatedPrescription = $('#edit_prescription').val();
        const updatedNotes = $('#edit_notes').val();
        
        $.ajax({
            url: 'mr_update.php',
            type: 'POST',
            dataType: 'json',
            data: {
                chart_no: chartNo,
                diagnosis: updatedDiagnosis,
                prescription: updatedPrescription,
                notes: updatedNotes
            },
            success: function(response) {
                if (response.success) {
                    const targetRow = $(`tr[data-id="${chartNo}"]`);
                    targetRow.find('td').eq(6).text(updatedDiagnosis);
                    targetRow.find('td').eq(7).text(updatedPrescription);
                    targetRow.find('td').eq(8).text(updatedNotes);

                    alert(`[보안 서명 및 우분투 DB 반영 완료]\n차트번호 [ ${chartNo} ]의 기록 정정이 처리되었습니다.`);
                    closeEditModal();
                } else {
                    alert('저장 실패: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('서버 통신 중 오류가 발생했습니다. DB 연동 상태를 점검하세요.');
            }
        });
    });
});