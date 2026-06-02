document.addEventListener('DOMContentLoaded', function() {
    const patientForm = document.getElementById('patient_form');

    if (patientForm) {
        patientForm.addEventListener('submit', function(event) {
            // 기본 제출 동작 임시 막기 (검증을 위해)
            event.preventDefault();

            // 입력값 가져오기
            const patientName = document.getElementById('patient_name').value.trim();
            const patientSsn = document.getElementById('patient_ssn').value.trim();
            const deptName = document.getElementById('dept_name').value;

            // 간단한 유효성 검사 (프론트엔드 단)
            if (patientName === '') {
                alert('환자 이름을 입력해주세요.');
                document.getElementById('patient_name').focus();
                return;
            }

            if (patientSsn.length < 13) {
                alert('주민등록번호 13자리를 정확히 입력해주세요. (- 제외)');
                document.getElementById('patient_ssn').focus();
                return;
            }

            if (deptName === '') {
                alert('진료과를 선택해주세요.');
                document.getElementById('dept_name').focus();
                return;
            }

            // 검증이 완료되면 폼 제출 (나중에 팀원이 만든 insert.php 등으로 넘어감)
            console.log('검증 완료. 서버로 데이터를 전송합니다.');
            // 실제 서비스에서는 아래 코드를 활성화하여 폼을 제출합니다.
            // this.submit();
            alert('환자 접수가 완료되었습니다! (테스트)');
            this.reset(); // 폼 초기화
        });
    }
});