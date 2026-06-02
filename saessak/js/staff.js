document.addEventListener('DOMContentLoaded', function() {
    const staffForm = document.getElementById('staff_form');

    if (staffForm) {
        staffForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const staffName = document.getElementById('staff_name').value.trim();
            const staffRole = document.getElementById('staff_role').value;
            const deptName = document.getElementById('dept_name').value;

            if (staffName === '') {
                alert('의료진 성명을 입력해주세요.');
                document.getElementById('staff_name').focus();
                return;
            }

            if (staffRole === '') {
                alert('직책(의사/간호사)을 선택해주세요.');
                document.getElementById('staff_role').focus();
                return;
            }

            if (deptName === '') {
                alert('담당 진료과를 선택해주세요.');
                document.getElementById('dept_name').focus();
                return;
            }

            console.log('검증 완료. 의료진 데이터 전송.');
            alert('신규 의료진이 등록되었습니다! (테스트)');
            this.reset();
        });
    }
});