<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <h1 class="h3 mb-4">📚 My Courses</h1>

    <!-- Session Info -->
    <?php if ($session): ?>
        <div class="alert alert-info mb-4">
            <h5 class="alert-heading mb-1">
                <i class="fas fa-calendar me-2"></i><?= htmlspecialchars($session['name']) ?>
            </h5>
            <p class="mb-0">
                Academic Year: <strong><?= htmlspecialchars($session['start_year']) ?> - <?= htmlspecialchars($session['end_year']) ?></strong>
            </p>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No active session found.
        </div>
    <?php endif; ?>

    <!-- Assigned Courses -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                Assigned Courses 
                <span class="badge bg-primary"><?= count($courses) ?></span>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($courses)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    You have not been assigned to any courses yet. Please contact your HOD.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <?= htmlspecialchars($course['code']) ?>
                                        <?php if ($course['is_coordinator']): ?>
                                            <span class="badge bg-warning float-end">Coordinator</span>
                                        <?php endif; ?>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title"><?= htmlspecialchars($course['title']) ?></h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <?= htmlspecialchars($course['department_name']) ?><br>
                                            <?= htmlspecialchars($course['level_name']) ?> - 
                                            Semester <?= $course['semester_number'] ?>
                                        </small>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-info"><?= $course['credit_units'] ?> Units</span>
                                        <span class="badge bg-success"><?= $course['student_count'] ?? 0 ?> Students</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="/courses/students/<?= $course['course_id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-users me-1"></i>View Students
                                    </a>
                                    <a href="/grades/entry/<?= $course['course_id'] ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-edit me-1"></i>Grade Entry
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
