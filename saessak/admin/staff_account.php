<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>의료진 스케줄 관리 - MediAdmin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php
    // --- [Mock Data / 나중에 DB SELECT 문으로 교체할 부분] ---
    $mock_staff_list = [
        ["emp_id" => "D001", "name" => "김닥터", "role" => "의사", "dept" => "내과", "schedule" => "주간 (09:00 - 18:00)", "status" => "근무중"],
        ["emp_id" => "N001", "name" => "이널스", "role" => "간호사", "dept" => "내과", "schedule" => "야간 (18:00 - 09:00)", "status" => "휴무"],
        ["emp_id" => "D002", "name" => "박외과", "role" => "의사", "dept" => "외과", "schedule" => "주간 (09:00 - 18:00)", "status" => "수술중"],
        ["emp_id" => "N002", "name" => "최간호", "role" => "간호사", "dept" => "응급실", "schedule" => "교대 (14:00 - 22:00)", "status" => "근무중"],
    ];

    function get_role_badge($role) {
        return ($role === '의사') ? 'badge badge-doctor' : 'badge badge-nurse';
    }
    ?>

    <main class="main-content">
        <div class="form-container">
            <div class="form-header">
                <h2>신규 의료진/스케줄 등록</h2>
            </div>
            
            <form id="staff_form" action="" method="POST">
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label for="staff_name">성명</label>
                        <input type="text" id="staff_name" name="staff_name" class="form-control" placeholder="예: 김닥터">
                    </div>
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label for="staff_role">직책</label>
                        <select id="staff_role" name="staff_role" class="form-control">
                            <option value="">선택</option>
                            <option value="의사">의사</option>
                            <option value="간호사">간호사</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label for="dept_name">담당 과</label>
                        <select id="dept_name" name="dept_name" class="form-control">
                            <option value="">선택</option>
                            <option value="내과">내과</option>
                            <option value="외과">외과</option>
                            <option value="응급실">응급실</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="work_schedule">근무 형태 (스케줄)</label>
                    <select id="work_schedule" name="work_schedule" class="form-control">
                        <option value="주간">주간 (09:00 - 18:00)</option>
                        <option value="교대">오후 교대 (14:00 - 22:00)</option>
                        <option value="야간">야간 (22:00 - 09:00)</option>
                        <option value="휴무">휴무 (Off)</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">의료진 등록</button>
            </form>
        </div>

        <div class="table-container">
            <div class="form-header" style="padding: 1.5rem 1.5rem 0 1.5rem; border-bottom: none;">
                <h2>현재 의료진 스케줄 현황</h2>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>사번</th>
                        <th>성명</th>
                        <th>직책</th>
                        <th>담당 과</th>
                        <th>현재 스케줄</th>
                        <th>상태</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mock_staff_list as $staff): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($staff['emp_id']); ?></td>
                        <td><strong><?php echo htmlspecialchars($staff['name']); ?></strong></td>
                        <td>
                            <span class="<?php echo get_role_badge($staff['role']); ?>">
                                <?php echo htmlspecialchars($staff['role']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($staff['dept']); ?></td>
                        <td><?php echo htmlspecialchars($staff['schedule']); ?></td>
                        <td><?php echo htmlspecialchars($staff['status']); ?></td>
                        <td>
                            <button class="action-btn">수정</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </main>

    <script src="../js/staff.js"></script>
</body>
</html>