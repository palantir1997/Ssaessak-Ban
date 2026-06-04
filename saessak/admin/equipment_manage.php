<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>의료 장비 관리 - MediAdmin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php
    // --- [Mock Data / 팀원이 SELECT 쿼리로 변경할 데이터] ---
    $mock_equip_list = [
        ["equip_id" => "EQ-1001", "equip_name" => "MRI 스캐너 3.0T", "category" => "영상진단기기", "purchase_date" => "2023-05-12", "current_status" => "사용 가능", "last_inspection" => "2026-05-20"],
        ["equip_id" => "EQ-1002", "equip_name" => "이동형 X-Ray", "category" => "영상진단기기", "purchase_date" => "2024-01-15", "current_status" => "사용 중", "last_inspection" => "2026-06-01"],
        ["equip_id" => "EQ-2001", "equip_name" => "심전도 측정기", "category" => "생체계측기기", "purchase_date" => "2022-11-30", "current_status" => "사용 가능", "last_inspection" => "2026-04-10"],
        ["equip_id" => "EQ-3005", "equip_name" => "위내시경 장비", "category" => "진료용장비", "purchase_date" => "2025-02-10", "current_status" => "수리 중", "last_inspection" => "2026-05-28"],
    ];

    // 장비 상태에 따라 CSS 클래스를 반환하는 함수
    function get_equip_status_color($status) {
        switch ($status) {
            case '사용 가능': return 'badge badge-available';
            case '사용 중': return 'badge badge-inuse';
            case '수리 중': return 'badge badge-repair';
            default: return 'badge';
        }
    }
    ?>

    <main class="main-content">
        
        <div class="form-container">
            <div class="form-header">
                <h2>신규 의료 장비 등록</h2>
            </div>
            
            <form id="equip_form" action="" method="POST">
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="flex: 2; margin-bottom: 0;">
                        <label for="equip_name">장비명 (모델명)</label>
                        <input type="text" id="equip_name" name="equip_name" class="form-control" placeholder="예: 초음파 영상 진단기">
                    </div>
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label for="category">장비 분류</label>
                        <select id="category" name="category" class="form-control">
                            <option value="">분류 선택</option>
                            <option value="영상진단기기">영상진단기기 (X-Ray, MRI 등)</option>
                            <option value="생체계측기기">생체계측기기 (혈압, 심전도 등)</option>
                            <option value="진료용장비">진료/수술용 장비</option>
                            <option value="기타소모품">기타 의료 비품</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label for="purchase_date">구입 일자</label>
                        <input type="date" id="purchase_date" name="purchase_date" class="form-control">
                    </div>
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label for="current_status">현재 상태</label>
                        <select id="current_status" name="current_status" class="form-control">
                            <option value="사용 가능">사용 가능</option>
                            <option value="사용 중">사용 중</option>
                            <option value="수리 중">점검/수리 중</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="repair_history">수리/점검 이력 메모</label>
                    <textarea id="repair_history" name="repair_history" class="form-control" rows="3" placeholder="특이사항이나 수리 내역을 기록하세요."></textarea>
                </div>

                <button type="submit" class="btn-submit">장비 등록</button>
            </form>
        </div>

        <div class="table-container">
            <div class="form-header" style="padding: 1.5rem 1.5rem 0 1.5rem; border-bottom: none;">
                <h2>보유 장비 현황</h2>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>관리번호</th>
                        <th>장비명</th>
                        <th>분류</th>
                        <th>최근 점검일</th>
                        <th>상태</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mock_equip_list as $equip): ?>
                    <tr>
                        <td><span style="color: #64748b; font-size: 0.875rem;"><?php echo htmlspecialchars($equip['equip_id']); ?></span></td>
                        <td><strong><?php echo htmlspecialchars($equip['equip_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($equip['category']); ?></td>
                        <td><?php echo htmlspecialchars($equip['last_inspection']); ?></td>
                        <td>
                            <span class="<?php echo get_equip_status_color($equip['current_status']); ?>">
                                <?php echo htmlspecialchars($equip['current_status']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="action-btn">수리 기록</button>
                            <button class="action-btn">상태 변경</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </main>

    <script src="../js/equipment.js"></script>
</body>
</html>