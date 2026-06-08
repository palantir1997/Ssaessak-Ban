<?php
// admin/mr_update.php
header('Content-Type: application/json');

include 'include/db.php';

try {
    $chart_no    = mysqli_real_escape_string($conn, $_POST['chart_no']);
    $diagnosis   = mysqli_real_escape_string($conn, $_POST['diagnosis']);
    $prescription = mysqli_real_escape_string($conn, $_POST['prescription']);
    $notes       = mysqli_real_escape_string($conn, $_POST['notes']);

    if (empty($chart_no) || empty($diagnosis)) {
        echo json_encode(['success' => false, 'message' => '필수 항목이 누락되었습니다.']);
        exit;
    }

    $query = "UPDATE medical_records 
              SET diagnosis = '$diagnosis', prescription = '$prescription', notes = '$notes' 
              WHERE chart_no = '$chart_no'";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => '정상적으로 수정되었습니다.']);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>