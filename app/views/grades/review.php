<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">✅ Review Grades - <?= htmlspecialchars($course['code']) ?></h1>
            <p class="text-muted mb-0"><?= htmlspecialchars($course['title']) ?></p>
        </div>
        <a href="/grades/pending" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Pending
        </a>
    </div>

    <!-- Statistics -->
    <?php if (!empty($stats)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Course Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2">
                        <h6 class="text-muted">Total Students</h6>
                        <h4><?= $stats['total_students'] ?></h4>
                    </div>
                    <div class="col-md-2">
                        <h6 class="text-muted">Average Score</h6>
                        <h4><?= number_format($stats['average_score'], 2) ?>%</h4>
                    </div>
                    <div class="col-md-2">
                        <h6 class="text-muted">Pass Rate</h6>
                        <h4 class="text-success"><?= number_format($stats['pass_rate'], 2) ?>%</h4>
                    </div>
                    <div class="col-md-2">
                        <h6 class="text-muted">Passed</h6>
                        <h4 class="text-success"><?= $stats['passed'] ?></h4>
                    </div>
                    <div class="col-md-2">
                        <h6 class="text-muted">Failed</h6>
                        <h4 class="text-danger"><?= $stats['failed'] ?></h4>
                    </div>
                    <div class="col-md-2">
                        <h6 class="text-muted">Avg Grade Point</h6>
                        <h4><?= number_format($stats['average_gp'], 2) ?></h4>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Grade Distribution -->
    <?php if (!empty($distribution)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Grade Distribution</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <?php foreach ($distribution as $dist): ?>
                        <div class="col-md-2">
                            <div class="badge bg-<?= $dist['grade'] == 'A' ? 'primary' : ($dist['grade'] == 'B' ? 'info' : ($dist['grade'] == 'C' ? 'success' : ($dist['grade'] == 'D' ? 'warning' : ($dist['grade'] == 'E' ? 'secondary' : 'danger')))) ?> w-100 mb-2">
                                Grade <?= $dist['grade'] ?>
                            </div>
                            <h4><?= $dist['count'] ?></h4>
                            <small class="text-muted"><?= number_format($dist['percentage'], 1) ?>%</small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Detailed Grades -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Student Grades</h5>
            <button type="button" class="btn btn-success" onclick="approveAll()">
                <i class="fas fa-check-double me-1"></i>Approve All Grades
            </button>
        </div>
        <div class="card-body">
            <form id="approvalForm" method="POST" action="/grades/approve">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
                                </th>
                                <th>Matric Number</th>
                                <th>Student Name</th>
                                <th>CA Score</th>
                                <th>Exam Score</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Grade Point</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grades as $grade): ?>
                                <?php if ($grade['status'] == 'submitted'): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="grade_ids[]" value="<?= $grade['id'] ?>" class="grade-checkbox">
                                        </td>
                                        <td><strong><?= htmlspecialchars($grade['matric_number']) ?></strong></td>
                                        <td><?= htmlspecialchars($grade['student_name']) ?></td>
                                        <td><?= number_format($grade['ca_score'], 2) ?></td>
                                        <td><?= number_format($grade['exam_score'], 2) ?></td>
                                        <td><strong><?= number_format($grade['total_score'], 2) ?></strong></td>
                                        <td>
                                            <span class="badge bg-<?= $grade['grade'] == 'A' ? 'primary' : ($grade['grade'] == 'B' ? 'info' : ($grade['grade'] == 'C' ? 'success' : ($grade['grade'] == 'D' ? 'warning' : ($grade['grade'] == 'E' ? 'secondary' : 'danger')))) ?>">
                                                <?= $grade['grade'] ?>
                                            </span>
                                        </td>
                                        <td><?= number_format($grade['grade_point'], 1) ?></td>
                                        <td>
                                            <span class="badge bg-warning">Submitted</span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.grade-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

function approveAll() {
    const selected = document.querySelectorAll('.grade-checkbox:checked');
    
    if (selected.length === 0) {
        alert('Please select at least one grade to approve.');
        return;
    }
    
    if (confirm(`Approve ${selected.length} grade(s)? This action cannot be undone.`)) {
        document.getElementById('approvalForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
