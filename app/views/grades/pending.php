<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <h1 class="h3 mb-4">⏳ Pending Grade Approvals</h1>

    <!-- Pending Grades -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                Pending for Approval
                <span class="badge bg-warning"><?= count($pending) ?></span>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($pending)): ?>
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    No pending grades for approval at this time.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Department</th>
                                <th>Semester</th>
                                <th>Lecturer</th>
                                <th>Students</th>
                                <th>Submitted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending as $item): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($item['course_code']) ?></strong></td>
                                    <td><?= htmlspecialchars($item['course_title']) ?></td>
                                    <td><?= htmlspecialchars($item['department_name']) ?></td>
                                    <td><?= htmlspecialchars($item['semester_name']) ?></td>
                                    <td><?= htmlspecialchars($item['lecturer_name'] ?? 'N/A') ?></td>
                                    <td><span class="badge bg-info"><?= $item['student_count'] ?> students</span></td>
                                    <td><?= date('M d, Y', strtotime($item['submitted_at'])) ?></td>
                                    <td>
                                        <a href="/grades/review/<?= $item['course_id'] ?>/<?= $item['semester_id'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>Review & Approve
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
