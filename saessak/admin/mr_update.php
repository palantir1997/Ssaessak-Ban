<?php
// 비동기 JSON 통신 규격 선언
header('Content-Type: application/json; charset=utf-8');

// 1. 우분투 MySQL DB 연결 설정
$db_host = '192.168.45.213'; 
$db_user = 'hj';   
$db_pass = '1234'; 
$db_name = 'saessak';   

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB 연결 실패: ' . $e->getMessage()]);
    exit;
}

// 2. JS에서 전송된 데이터 수신 및 필터링
$chart_no = isset($_POST['chart_no']) ? trim($_POST['chart_no']) : '';
$diagnosis = isset($_POST['diagnosis']) ? trim($_POST['diagnosis']) : '';
$prescription = isset($_POST['prescription']) ? trim($_POST['prescription']) : '';
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

if (empty($chart_no) || empty($diagnosis)) {
    echo json_encode(['success' => false, 'message' => '필수 항목(차트번호, 진단명)이 입력되지 않았습니다.']);
    exit;
}

try {
    // 3. 의료법에 준거하여 수정 권한 필드(진단, 처방, 비고)만 안전하게 UPDATE 진행
    $sql = "UPDATE medical_records 
            SET diagnosis = :diagnosis, 
                prescription = :prescription, 
                notes = :notes 
            WHERE chart_no = :chart_no";
            
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':diagnosis'    => $diagnosis,
        ':prescription' => $prescription,
        ':notes'        => $notes,
        ':chart_no'     => $chart_no
    ]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => '진료 기록 정정이 성공적으로 반영되었습니다.']);
    } else {
        echo json_encode(['success' => false, 'message' => '변경된 내용이 없거나 수정 처리에 실패했습니다.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '쿼리 실행 오류: ' . $e->getMessage()]);
}
exit;