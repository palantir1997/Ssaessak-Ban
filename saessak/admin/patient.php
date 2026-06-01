<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>환자 접수/예약 - MediAdmin</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>

    <main class="main-content">
        <div class="form-container">
            <div class="form-header">
                <h2>신규 환자 접수 및 예약</h2>
            </div>
            
            <form id="patient_form" action="" method="POST">
                <div class="form-group">
                    <label for="patient_name">환자 성명</label>
                    <input type="text" id="patient_name" name="patient_name" class="form-control" placeholder="예: 홍길동">
                </div>

                <div class="form-group">
                    <label for="patient_ssn">주민등록번호 (- 제외)</label>
                    <input type="text" id="patient_ssn" name="patient_ssn" class="form-control" placeholder="숫자 13자리 입력" maxlength="13">
                </div>

                <div class="form-group">
                    <label for="contact_number">연락처</label>
                    <input type="tel" id="contact_number" name="contact_number" class="form-control" placeholder="예: 010-1234-5678">
                </div>

                <div class="form-group">
                    <label for="dept_name">진료과 선택</label>
                    <select id="dept_name" name="dept_name" class="form-control">
                        <option value="">-- 진료과를 선택하세요 --</option>
                        <option value="내과">내과</option>
                        <option value="외과">외과</option>
                        <option value="이비인후과">이비인후과</option>
                        <option value="정형외과">정형외과</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="book_time">예약 일시 (선택)</label>
                    <input type="datetime-local" id="book_time" name="book_time" class="form-control">
                </div>

                <div class="form-group">
                    <label for="special_note">특이사항 (증상 등)</label>
                    <textarea id="special_note" name="special_note" class="form-control" rows="4" placeholder="환자의 현재 증상이나 특이사항을 입력하세요."></textarea>
                </div>

                <button type="submit" class="btn-submit">접수 등록</button>
            </form>
        </div>
    </main>

    <script src="../js/patient.js"></script>
</body>
</html>