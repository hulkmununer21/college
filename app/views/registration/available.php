<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <h1 class="h3 mb-4">📚 Course Registration</h1>

    <!-- Semester Info -->
    <?php if ($semester): ?>
        <div class="alert alert-info mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="alert-heading mb-1">
                        <i class="fas fa-calendar me-2"></i><?= htmlspecialchars($semester['semester_name']) ?>
                    </h5>
                    <p class="mb-0">
                        Session: <strong><?= htmlspecialchars($semester['session_name']) ?></strong>
                    </p>
                </div>
                <div class="text-end">
                    <?php if ($registration_status['status'] === 'open'): ?>
                        <span class="badge bg-success fs-6">📝 Registration Open</span>
                        <br>
                        <small class="text-muted"><?= $registration_status['days_remaining'] ?> days remaining</small>
                    <?php elseif ($registration_status['status'] === 'closed'): ?>
                        <span class="badge bg-danger fs-6">🔒 Registration Closed</span>
                    <?php else: ?>
                        <span class="badge bg-warning fs-6">⏳ Registration Not Yet Open</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No active semester found. Please contact the administrator.
        </div>
    <?php endif; ?>

    <!-- Registration Summary -->
    <?php if ($semester && !empty($summary)): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Total Courses</h6>
                        <h2><?= $summary['total_courses'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Total Credit Units</h6>
                        <h2 class="<?= $summary['is_within_limit'] ? 'text-success' : 'text-danger' ?>">
                            <?= $summary['total_credits'] ?>
                        </h2>
                        <small class="text-muted"><?= $summary['min_credits'] ?> - <?= $summary['max_credits'] ?> allowed</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Approved</h6>
                        <h2 class="text-success"><?= $summary['approved_count'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Pending</h6>
                        <h2 class="text-warning"><?= $summary['pending_count'] ?></h2>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="mb-3">
        <a href="/courses/my-registrations" class="btn btn-primary">
            <i class="fas fa-list me-1"></i>View My Registrations
        </a>
    </div>

    <!-- Available Courses -->
    <?php if ($semester && $registration_status['status'] === 'open'): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">📋 Available Courses for Registration</h5>
            </div>
            <div class="card-body">
                <?php if (empty($courses)): ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        No courses available for registration at this time.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Course Title</th>
                                    <th>Units</th>
                                    <th>Type</th>
                                    <th>Prerequisites</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($course['code']) ?></strong></td>
                                        <td><?= htmlspecialchars($course['title']) ?></td>
                                        <td><span class="badge bg-info"><?= $course['credit_units'] ?></span></td>
                                        <td>
                                            <span class="badge bg-<?= $course['is_elective'] ? 'secondary' : 'success' ?>">
                                                <?= $course['is_elective'] ? 'Elective' : 'Core' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($course['prerequisite_count'] > 0): ?>
                                                <span class="badge bg-warning"><?= $course['prerequisite_count'] ?> Required</span>
                                            <?php else: ?>
                                                <span class="text-muted">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($course['is_registered']): ?>
                                                <?php if ($course['registration_status'] === 'approved'): ?>
                                                    <span class="badge bg-success">✓ Approved</span>
                                                <?php elseif ($course['registration_status'] === 'pending'): ?>
                                                    <span class="badge bg-warning">⏳ Pending</span>
                                                <?php elseif ($course['registration_status'] === 'rejected'): ?>
                                                    <span class="badge bg-danger">✗ Rejected</span>
                                                <?php endif; ?>
                                            <?php elseif ($course['can_register']): ?>
                                                <span class="badge bg-secondary">Available</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Prerequisites Not Met</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!$course['is_registered'] && $course['can_register']): ?>
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-primary"
                                                    onclick="registerCourse(<?= $course['id'] ?>, '<?= htmlspecialchars($course['code']) ?>', <?= $course['credit_units'] ?>)">
                                                    <i class="fas fa-plus me-1"></i>Register
                                                </button>
                                            <?php elseif ($course['is_registered'] && $course['registration_status'] === 'pending'): ?>
                                                <span class="text-muted">Awaiting approval</span>
                                            <?php elseif (!$course['can_register']): ?>
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-secondary"
                                                    onclick="showPrerequisites(<?= $course['id'] ?>)"
                                                    disabled>
                                                    Prerequisites Not Met
                                                </button>
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
    <?php elseif ($semester): ?>
        <div class="alert alert-warning">
            <i class="fas fa-lock me-2"></i>
            <?= $registration_status['message'] ?>
        </div>
    <?php endif; ?>
</div>

<script>
function registerCourse(courseId, courseCode, creditUnits) {
    if (confirm(`Register for ${courseCode} (${creditUnits} units)?`)) {
        fetch('/courses/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `course_id=${courseId}&csrf_token=<?= $_SESSION['csrf_token'] ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
            console.error('Error:', error);
        });
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
