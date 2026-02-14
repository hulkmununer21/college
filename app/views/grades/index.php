<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <h1 class="h3 mb-4">📝 Grade Entry Dashboard</h1>

    <!-- Active Semester Info -->
    <?php if (!empty($active_semester)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Active Semester:</strong> <?= htmlspecialchars($active_semester['name']) ?> - 
            <?= htmlspecialchars($active_semester['session_name']) ?>
        </div>
    <?php endif; ?>

    <!-- Assigned Courses -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">My Assigned Courses</h5>
        </div>
        <div class="card-body">
            <?php if (empty($courses)): ?>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No courses assigned to you for grade entry.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Credits</th>
                                <th>Department</th>
                                <th>Semester</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($course['course_code']) ?></strong></td>
                                    <td><?= htmlspecialchars($course['course_title']) ?></td>
                                    <td><span class="badge bg-info"><?= $course['credit_units'] ?> Units</span></td>
                                    <td><?= htmlspecialchars($course['department_name']) ?></td>
                                    <td><?= htmlspecialchars($course['semester_name']) ?></td>
                                    <td><?= $course['student_count'] ?? 0 ?></td>
                                    <td>
                                        <?php
                                        $status = $course['grade_status'] ?? 'not_started';
                                        $badges = [
                                            'not_started' => ['class' => 'secondary', 'text' => 'Not Started'],
                                            'draft' => ['class' => 'warning', 'text' => 'In Progress'],
                                            'submitted' => ['class' => 'info', 'text' => 'Submitted'],
                                            'approved' => ['class' => 'success', 'text' => 'Approved']
                                        ];
                                        $badge = $badges[$status] ?? $badges['not_started'];
                                        ?>
                                        <span class="badge bg-<?= $badge['class'] ?>">
                                            <?= $badge['text'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($status !== 'approved'): ?>
                                            <a href="/grades/entry/<?= $course['course_id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit me-1"></i>
                                                <?= $status === 'not_started' ? 'Start Entry' : 'Continue Entry' ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="/grades/entry/<?= $course['course_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Stats -->
    <?php if (!empty($stats)): ?>
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Total Courses</h6>
                        <h2><?= $stats['total_courses'] ?? 0 ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Completed</h6>
                        <h2 class="text-success"><?= $stats['completed'] ?? 0 ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title text-muted">In Progress</h6>
                        <h2 class="text-warning"><?= $stats['in_progress'] ?? 0 ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Not Started</h6>
                        <h2 class="text-secondary"><?= $stats['not_started'] ?? 0 ?></h2>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
