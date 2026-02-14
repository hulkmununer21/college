<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <h1 class="h3 mb-4">📊 My Academic Results</h1>

    <!-- CGPA Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Cumulative GPA</h6>
                    <h1 class="display-4 mb-0"><?= number_format($cgpa['cgpa'], 2) ?></h1>
                    <p class="text-muted mb-0">Out of 5.0</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Credits</h6>
                    <h1 class="display-4 mb-0"><?= $cgpa['cumulative_credits'] ?></h1>
                    <p class="text-muted mb-0">Units Earned</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Courses</h6>
                    <h1 class="display-4 mb-0"><?= $cgpa['total_courses'] ?></h1>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Class of Degree</h6>
                    <h4 class="mb-0"><?= $class_of_degree ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mb-4">
        <a href="/grades/transcript" class="btn btn-primary">
            <i class="fas fa-file-alt me-1"></i>View Full Transcript
        </a>
        <a href="/grades/transcript?print=1" class="btn btn-outline-primary" target="_blank">
            <i class="fas fa-print me-1"></i>Print Transcript
        </a>
    </div>

    <!-- Semester Results -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Semester Results</h5>
        </div>
        <div class="card-body">
            <?php if (empty($semesters)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    No results available yet. Results will appear here once grades are approved.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Semester</th>
                                <th>Session</th>
                                <th>Level</th>
                                <th>Courses</th>
                                <th>Credits</th>
                                <th>GPA</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($semesters as $sem): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($sem['semester_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($sem['session_name']) ?></td>
                                    <td><?= htmlspecialchars($sem['level_name']) ?></td>
                                    <td><?= $sem['total_courses'] ?></td>
                                    <td><span class="badge bg-info"><?= $sem['total_credits'] ?> Units</span></td>
                                    <td>
                                        <strong class="text-<?= $sem['gpa'] >= 4.5 ? 'primary' : ($sem['gpa'] >= 3.5 ? 'success' : ($sem['gpa'] >= 2.4 ? 'warning' : 'danger')) ?>">
                                            <?= number_format($sem['gpa'], 2) ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <a href="/grades/semester-results/<?= $sem['semester_id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
