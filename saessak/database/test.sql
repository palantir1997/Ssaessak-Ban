CREATE DATABASE IF NOT EXISTS saessak CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE saessak;

-- 외래키 체크 임시 해제 후 기존 테이블 초기화 (안전한 재빌드용)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS security_scan_logs;
DROP TABLE IF EXISTS intrusion_logs;
DROP TABLE IF EXISTS medical_equipments;
DROP TABLE IF EXISTS medical_records;
DROP TABLE IF EXISTS receptions;
DROP TABLE IF EXISTS medical_staffs;
DROP TABLE IF EXISTS notices;
DROP TABLE IF EXISTS staff_accounts;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS admin_users;
SET FOREIGN_KEY_CHECKS = 1;


-- =================================================================
-- [1] 계정 및 회원 관리 영역 (Authentication & Users)
-- =================================================================

-- 1) 최고 관리자 테이블 (test.sql 원본 유지)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='최고 관리자 계정';

-- 2) 환자 회원 테이블 (test.sql 원본 유지)
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    login_id VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='홈페이지 가입 환자 회원';

-- 3) 병원 직원 시스템 계정 테이블 (staff_account.php 구조 반영)
CREATE TABLE IF NOT EXISTS staff_accounts (
    account_no VARCHAR(20) PRIMARY KEY COMMENT '계정번호 (예: AC-1001)',
    user_id VARCHAR(50) NOT NULL UNIQUE COMMENT '로그인 아이디',
    password VARCHAR(255) NOT NULL COMMENT '비밀번호',
    name VARCHAR(50) NOT NULL COMMENT '성명',
    role ENUM('admin', '의사', '간호사', '행정직') NOT NULL COMMENT '권한 분류',
    dept_name VARCHAR(50) NOT NULL COMMENT '담당 부서/진료과',
    last_login DATETIME COMMENT '마지막 로그인 일시',
    status ENUM('활성', '비활성') DEFAULT '활성' COMMENT '계정 상태',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='원내 ERP 시스템 권한 직원 계정';


-- =================================================================
-- [2] 의료 자원 및 원무 관리 영역 (Medical Resources & Administrative)
-- =================================================================

-- 4) 의료진 인사 관리 테이블 (mdppl.php 구조 반영)
CREATE TABLE IF NOT EXISTS medical_staffs (
    staff_no VARCHAR(20) PRIMARY KEY COMMENT '직원번호 (예: DR-1001, NR-2001)',
    name VARCHAR(50) NOT NULL COMMENT '성명',
    position ENUM('의사', '간호사') NOT NULL COMMENT '직책',
    dept_name VARCHAR(50) NOT NULL COMMENT '담당 진료과 (내과, 정형외과 등)',
    phone VARCHAR(20) NOT NULL COMMENT '연락처',
    hire_date DATE NOT NULL COMMENT '입사일',
    status ENUM('재직중', '휴직중', '퇴사') DEFAULT '재직중' COMMENT '근무 상태',
    work_schedule VARCHAR(100) DEFAULT '미정' COMMENT '근무 스케줄',
    account_id VARCHAR(50) NULL COMMENT 'ERP 시스템 연동 아이디',
    FOREIGN KEY (account_id) REFERENCES staff_accounts(user_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='의료진 세부 프로필 및 인사 정보';

-- 5) 신규 접수 및 외래 예약 테이블 (reception.php & 외부 예약 폼 구조 반영)
CREATE TABLE IF NOT EXISTS receptions (
    reception_no VARCHAR(20) PRIMARY KEY COMMENT '접수번호 (예: RS-1001)',
    patient_name VARCHAR(50) NOT NULL COMMENT '환자 성명',
    reception_type ENUM('예약', '현장접수') NOT NULL COMMENT '접수 유형',
    dept_name VARCHAR(50) NOT NULL COMMENT '희망 진료과',
    target_date DATE NOT NULL COMMENT '희망/예약 날짜',
    target_time VARCHAR(10) NOT NULL COMMENT '희망/예약 시간 (예: 10:30)',
    symptoms_memo TEXT COMMENT '증상 메모 및 전달사항',
    status ENUM('대기중', '진료중', '확정', '완료', '취소') DEFAULT '대기중' COMMENT '진료 및 예약 상태',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='실시간 전산 외래 접수 및 예약 내역';

-- 6) 진료 기록 및 전자 차트 테이블 (chart.php 구조 반영)
CREATE TABLE IF NOT EXISTS medical_records (
    chart_no VARCHAR(20) PRIMARY KEY COMMENT '차트번호 (예: CH-1001)',
    reception_no VARCHAR(20) NULL COMMENT '연동된 접수 번호',
    patient_name VARCHAR(50) NOT NULL COMMENT '환자명',
    age INT NOT NULL COMMENT '나이',
    dept_name VARCHAR(50) NOT NULL COMMENT '진료과',
    doctor_name VARCHAR(50) NOT NULL COMMENT '담당의 성명',
    record_date DATE NOT NULL COMMENT '진료일',
    diagnosis VARCHAR(255) NOT NULL COMMENT '진단명',
    prescription TEXT COMMENT '처방전 내용 (예: 암로디핀 5mg)',
    notes TEXT COMMENT '비고 및 세부 증상 정보 (예: 혈압 145/90)',
    FOREIGN KEY (reception_no) REFERENCES receptions(reception_no) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='환자 전자 차트 및 진료 기록';

-- 7) 의료 장비 관리 테이블 (equipment_manage.php 구조 반영)
CREATE TABLE IF NOT EXISTS medical_equipments (
    equipment_no VARCHAR(20) PRIMARY KEY COMMENT '관리번호 (예: EQ-1001)',
    equipment_name VARCHAR(150) NOT NULL COMMENT '장비명 (모델명)',
    category ENUM('영상진단기기', '생체계측기기', '진료용장비', '기타분류') NOT NULL COMMENT '장비 분류',
    purchase_date DATE NOT NULL COMMENT '구입 일자',
    last_check_date DATE NOT NULL COMMENT '최근 점검일',
    status ENUM('사용 가능', '사용 중', '수리 중', '폐기') DEFAULT '사용 가능' COMMENT '현재 상태',
    maintenance_memo TEXT COMMENT '수리/점검 이력 메모',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='원내 보유 의료 장비 및 유지보수 이력';


-- =================================================================
-- [3] 소통 및 보안 관제 영역 (Notice & Security SIEM Logs)
-- =================================================================

-- 8) 공지사항 게시판 테이블 (notice.php 구조 반영)
CREATE TABLE IF NOT EXISTS notices (
    notice_no VARCHAR(20) PRIMARY KEY COMMENT '공지사항 번호 (예: NT-1001)',
    category ENUM('공지', '휴진', '소식', '안내') NOT NULL COMMENT '카테고리 분류',
    title VARCHAR(255) NOT NULL COMMENT '제목',
    content TEXT NOT NULL COMMENT '내용',
    author_name VARCHAR(50) NOT NULL DEFAULT '김관리' COMMENT '작성자 이름',
    created_at DATE NOT NULL COMMENT '작성일',
    is_important TINYINT(1) DEFAULT 0 COMMENT '중요 공지 설정 여부 (1: 중요, 0: 일반)'
) ENGINE=InnoDB COMMENT='원내/원외 통합 공지사항';

-- 9) 침입 탐지 로그 테이블 (security_logs.php 구조 반영)
CREATE TABLE IF NOT EXISTS intrusion_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY COMMENT '로그 일련번호',
    detection_time DATETIME NOT NULL COMMENT '탐지 시간',
    attack_type VARCHAR(100) NOT NULL COMMENT '공격 유형',
    source_ip VARCHAR(45) NOT NULL COMMENT '출처 IP',
    risk_level ENUM('고위험', '중위험', '저위험') NOT NULL COMMENT '위험도',
    status ENUM('처리대기', '처리완료') DEFAULT '처리대기' COMMENT '조치 상태'
) ENGINE=InnoDB COMMENT='보안 시스템 침입 탐지 관제 로그';

-- 10) 보안 점검 도구 실행 기록 테이블 (security_tools.php 구조 반영)
CREATE TABLE IF NOT EXISTS security_scan_logs (
    scan_id INT AUTO_INCREMENT PRIMARY KEY COMMENT '점검 일련번호',
    tool_name ENUM('취약점 스캔', '네트워크 점검', '로그 분석') NOT NULL COMMENT '도구 이름',
    executor_id VARCHAR(50) NOT NULL DEFAULT 'admin' COMMENT '실행자 ID',
    execution_time DATETIME NOT NULL COMMENT '마지막 실행 일시',
    total_items INT DEFAULT 128 COMMENT '총 점검 항목 수',
    passed_items INT DEFAULT 125 COMMENT '이상 없음 항목 수',
    warning_items INT DEFAULT 3 COMMENT '주의 필요 항목 수'
) ENGINE=InnoDB COMMENT='보안 점검 도구 작동 결과 로그';




######################################


-- 1) 최고 관리자 기본 데이터
INSERT INTO admin_users (user_id, password, role) VALUES ('admin', '1234', 'admin');

-- 2) 직원 시스템 계정 데이터 (staff_account.php 화면 일치)
INSERT INTO staff_accounts (account_no, user_id, password, name, role, dept_name, last_login, status) VALUES
('AC-1001', 'admin', '1234', '김관리', 'admin', '관리부', '2026-06-02 09:15:00', '활성'),
('AC-1002', 'dr_park', '1234', '박건우', '의사', '일반내과', '2026-06-02 08:50:00', '활성'),
('AC-1003', 'dr_choi', '1234', '최태양', '의사', '정형외과', '2026-06-01 17:30:00', '활성'),
('AC-1004', 'nr_han', '1234', '한소희', '간호사', '종합건진센터', '2026-06-02 09:00:00', '활성'),
('AC-1005', 'nr_jung', '1234', '정다은', '간호사', '일반내과', '2026-05-20 14:00:00', '비활성');

-- 3) 의료진 세부 인사 프로필 데이터 (mdppl.php 화면 일치)
INSERT INTO medical_staffs (staff_no, name, position, dept_name, phone, hire_date, status, account_id) VALUES
('DR-1001', '김새싹', '의사', '가정의학과', '010-1234-5678', '2023-03-01', '재직중', NULL),
('DR-1002', '박건우', '의사', '일반내과', '010-2345-6789', '2023-05-15', '재직중', 'dr_park'),
('DR-1003', '최태양', '의사', '정형외과', '010-3456-7890', '2026-01-10', '재직중', 'dr_choi'),
('DR-1004', '이지민', '의사', '소아청소년과', '010-4567-8901', '2024-02-20', '재직중', NULL),
('NR-2001', '한소희', '간호사', '종합건진센터', '010-5678-9012', '2022-07-01', '재직중', 'nr_han'),
('NR-2002', '정다은', '간호사', '일반내과', '010-6789-0123', '2023-09-01', '휴직중', 'nr_jung');


-- 4) 신규 외래 접수 현황 데이터 (reception.php 화면 일치)
INSERT INTO receptions (reception_no, patient_name, reception_type, dept_name, target_date, target_time, symptoms_memo, status) VALUES
('RS-1001', '김철수', '예약', '내과', '2026-06-02', '10:30', '혈압 체크 필요', '확정'),
('RS-1002', '이영희', '현장접수', '이비인후과', '2026-06-02', '10:45', '초진', '대기중'),
('RS-1003', '박지민', '예약', '정형외과', '2026-06-02', '11:00', 'X-Ray 촬영 완료', '확정'),
('RS-1004', '최동훈', '현장접수', '내과', '2026-06-02', '11:30', '처방전 발급', '취소');

-- 5) 진료 기록 및 차트 데이터 (chart.php 화면 일치)
INSERT INTO medical_records (chart_no, reception_no, patient_name, age, dept_name, doctor_name, record_date, diagnosis, prescription, notes) VALUES
('CH-1001', 'RS-1001', '김철수', 45, '내과', '박건우', '2026-06-02', '고혈압', '암로디핀 5mg', '혈압 145/90, 1개월 후 재진'),
('CH-1002', 'RS-1002', '이영희', 32, '이비인후과', '김새싹', '2026-06-02', '급성 편도염', '항생제 5일치', '초진, 발열 38.2도'),
('CH-1003', 'RS-1003', '박지민', 28, '정형외과', '최태양', '2026-06-01', '요추 추간판 탈출증', '소염진통제', 'X-Ray 촬영 완료, MRI 권고'),
('CH-1004', 'RS-1004', '최동훈', 61, '내과', '박건우', '2026-06-01', '당뇨 2형', '메트포르민 500mg', '혈당 186, 식이요법 안내');

-- 6) 의료 장비 보유 현황 데이터 (equipment_manage.php 화면 일치)
INSERT INTO medical_equipments (equipment_no, equipment_name, category, purchase_date, last_check_date, status, maintenance_memo) VALUES
('EQ-1001', 'MRI 스캐너 3.0T', '영상진단기기', '2024-01-15', '2026-05-20', '사용 가능', '정기 코일 캘리브레이션 완료'),
('EQ-1002', '이동형 X-Ray', '영상진단기기', '2025-03-10', '2026-06-01', '사용 중', '배터리 모듈 교체 완료'),
('EQ-2001', '심전도 측정기', '생체계측기기', '2023-08-22', '2026-04-10', '사용 가능', '센서 리드선 전면 교체'),
('EQ-3005', '위내시경 장비', '진료용장비', '2024-11-05', '2026-05-28', '수리 중', '광원 램프 교체 및 세척 진행 중');

-- 7) 공지사항 데이터 (notice.php 화면 일치)
INSERT INTO notices (notice_no, category, title, content, author_name, created_at, is_important) VALUES
('NT-1001', '공지', '2026년 상반기 정기 소독 일정 안내', '원내 전체 소독...', '김관리', '2026-06-01', 1),
('NT-1002', '휴진', '현충일(6월 6일) 휴진 안내', '현충일 당일 휴진합니다...', '김관리', '2026-05-28', 1),
('NT-1003', '소식', '정형외과 최태양 과장 신규 부임 안내', '새로운 의료진을 소개합니다...', '김관리', '2026-05-20', 0),
('NT-1004', '안내', '원내 주차 공간 이용 안내', '외래 환자 2시간 무료 주차...', '김관리', '2026-05-15', 0),
('NT-1005', '공지', '전자 차트 시스템 업데이트 예정 안내', '백엔드 전산 정기 점검...', '김관리', '2026-05-10', 0);

-- 신규 테스트 데이터 (1~10번)
INSERT INTO receptions (reception_no, patient_name, reception_type, dept_name, target_date, target_time, symptoms_memo, status) VALUES
('RS-1001', '박지혁', '예약', '내과', '2026-06-04', '09:30', '정기검진 만성질환 상담', '대기중');
('RS-1002', '이지은', '예약', '이비인후과', '2026-06-04', '10:00', '만성 비염 및 감기 증상', '대기중');
('RS-1003', '한성우', '예약', '정형외과', '2026-06-04', '11:30', '오른쪽 손목 통증 물리치료', '대기중');
('RS-1004', '최민준', '예약', '소아청소년과', '2026-06-04', '14:30', '영유아 건강검진 및 상담', '대기중'),
('RS-1005', '김도현', '예약', '가정의학과', '2026-06-04', '16:00', '피로 누적 영양제 처방', '대기중'); 
('RS-1006', '홍길동', '예약', '내과', '2026-06-04', '09:00', '건강검진', '대기중'),
('RS-1007', '김철수', '현장접수', '정형외과', '2026-06-04', '09:30', '무릎 통증', '대기중'),
('RS-1008', '이영희', '예약', '가정의학과', '2026-06-04', '10:00', '영양제 상담', '대기중'),
('RS-1009', '박지민', '현장접수', '이비인후과', '2026-06-04', '10:30', '목감기', '대기중'),
('RS-1010', '최수호', '예약', '소아청소년과', '2026-06-04', '11:00', '예방접종', '대기중');

--