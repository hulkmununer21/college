<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">📋 <?= htmlspecialchars($semester['name']) ?> Results</h1>
            <p class="text-muted mb-0"><?= htmlspecialchars($semester['session_name'] ?? '') ?></p>
        </div>
        <a href="/grades/my-results" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to All Results
        </a>
    </div>

    <!-- GPA Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Semester GPA</h6>
                    <h2 class="mb-0"><?= number_format($gpa['gpa'], 2) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Credits</h6>
                    <h2 class="mb-0"><?= $gpa['total_credits'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Courses</h6>
                    <h2 class="mb-0"><?= $gpa['total_courses'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">CGPA</h6>
                    <h2 class="mb-0"><?= number_format($cgpa['cgpa'], 2) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Results -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Course Results</h5>
        </div>
        <div class="card-body">
            <?php if (empty($results)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    No results available for this semester.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Credits</th>
                                <th>CA Score</th>
                                <th>Exam Score</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Grade Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($result['course_code']) ?></strong></td>
                                    <td><?= htmlspecialchars($result['course_title']) ?></td>
                                    <td><span class="badge bg-info"><?= $result['credit_units'] ?></span></td>
                                    <td><?= number_format($result['ca_score'], 2) ?></td>
                                    <td><?= number_format($result['exam_score'], 2) ?></td>
                                    <td><strong><?= number_format($result['total_score'], 2) ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?= $result['grade'] == 'A' ? 'primary' : ($result['grade'] == 'B' ? 'info' : ($result['grade'] == 'C' ? 'success' : ($result['grade'] == 'D' ? 'warning' : ($result['grade'] == 'E' ? 'secondary' : 'danger')))) ?>">
                                            <?= $result['grade'] ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($result['grade_point'], 1) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="2"><strong>Summary</strong></td>
                                <td><strong><?= $gpa['total_credits'] ?> Units</strong></td>
                                <td colspan="4"></td>
                                <td><strong>GPA: <?= number_format($gpa['gpa'], 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Legend -->
                <div class="mt-3">
                    <h6>Grading Scale:</h6>
                    <span class="badge bg-primary me-2">A: 70-100 (5.0)</span>
                    <span class="badge bg-info me-2">B: 60-69 (4.0)</span>
                    <span class="badge bg-success me-2">C: 50-59 (3.0)</span>
                    <span class="badge bg-warning me-2">D: 45-49 (2.0)</span>
                    <span class="badge bg-secondary me-2">E: 40-44 (1.0)</span>
                    <span class="badge bg-danger">F: 0-39 (0.0)</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
