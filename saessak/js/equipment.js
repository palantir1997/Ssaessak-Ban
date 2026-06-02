document.addEventListener('DOMContentLoaded', function() {
    const equipForm = document.getElementById('equip_form');

    if (equipForm) {
        equipForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const equipName = document.getElementById('equip_name').value.trim();
            const category = document.getElementById('category').value;
            const currentStatus = document.getElementById('current_status').value;

            if (equipName === '') {
                alert('장비명을 입력해주세요. (예: MRI, 초음파 기기)');
                document.getElementById('equip_name').focus();
                return;
            }

            if (category === '') {
                alert('장비 분류를 선택해주세요.');
                document.getElementById('category').focus();
                return;
            }

            if (currentStatus === '') {
                alert('현재 장비 상태를 선택해주세요.');
                document.getElementById('current_status').focus();
                return;
            }

            console.log('장비 데이터 검증 완료.');
            alert('신규 의료 장비가 등록되었습니다! (테스트)');
            this.reset();
        });
    }
});