<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">📚 My Course Registrations</h1>
        <a href="/courses/available" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Register New Courses
        </a>
    </div>

    <!-- Semester Info -->
    <?php if ($semester): ?>
        <div class="alert alert-info mb-4">
            <h5 class="alert-heading mb-1">
               <i class="fas fa-calendar me-2"></i><?= htmlspecialchars($semester['semester_name']) ?>
            </h5>
            <p class="mb-0">
                Session: <strong><?= htmlspecialchars($semester['session_name']) ?></strong>
            </p>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No active semester found.
        </div>
    <?php endif; ?>

    <!-- Registration Summary -->
    <?php if ($semester && !empty($summary)): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Courses</h6>
                        <h2><?= $summary['total_courses'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center <?= $summary['is_within_limit'] ? 'bg-success' : 'bg-danger' ?> text-white">
                    <div class="card-body">
                        <h6>Total Credit Units</h6>
                        <h2><?= $summary['total_credits'] ?></h2>
                        <small>Allowed: <?= $summary['min_credits'] ?> - <?= $summary['max_credits'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h6>Approved</h6>
                        <h2><?= $summary['approved_count'] ?></h2>
                        <small><?= $summary['approved_credits'] ?> units</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-warning text-dark">
                    <div class="card-body">
                        <h6>Pending Approval</h6>
                        <h2><?= $summary['pending_count'] ?></h2>
                        <small><?= $summary['pending_credits'] ?> units</small>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$summary['is_within_limit']): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Warning:</strong> Your total credit units (<?= $summary['total_credits'] ?>) 
                <?= $summary['total_credits'] < $summary['min_credits'] ? 'are below' : 'exceed' ?> 
                the allowed range (<?= $summary['min_credits'] ?> - <?= $summary['max_credits'] ?> units).
                Please adjust your course selection.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Registered Courses -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Registered Courses</h5>
        </div>
        <div class="card-body">
            <?php if (empty($registrations)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    You have not registered for any courses yet.
                    <a href="/courses/available" class="alert-link">Register now</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Credit Units</th>
                                <th>Type</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $reg): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($reg['code']) ?></strong></td>
                                    <td><?= htmlspecialchars($reg['title']) ?></td>
                                    <td><span class="badge bg-info"><?= $reg['credit_units'] ?></span></td>
                                    <td>
                                        <span class="badge bg-<?= $reg['is_elective'] ? 'secondary' : 'success' ?>">
                                            <?= $reg['is_elective'] ? 'Elective' : 'Core' ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($reg['registration_date'])) ?></td>
                                    <td>
                                        <?php if ($reg['status'] === 'approved'): ?>
                                            <span class="badge bg-success">✓ Approved</span>
                                        <?php elseif ($reg['status'] === 'pending'): ?>
                                            <span class="badge bg-warning">⏳ Pending</span>
                                        <?php elseif ($reg['status'] === 'rejected'): ?>
                                            <span class="badge bg-danger">✗ Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($reg['status'] === 'pending' && !$reg['dropped']): ?>
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-danger"
                                                onclick="dropCourse(<?= $reg['registration_id'] ?>, '<?= htmlspecialchars($reg['code']) ?>')">
                                                <i class="fas fa-times me-1"></i>Drop
                                            </button>
                                        <?php elseif ($reg['status'] === 'approved'): ?>
                                            <span class="text-muted">Cannot drop approved course</span>
                                        <?php elseif ($reg['status'] === 'rejected'): ?>
                                            <span class="text-muted">Rejected</span>
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
</div>

<script>
function dropCourse(registrationId, courseCode) {
    if (confirm(`Are you sure you want to drop ${courseCode}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/courses/drop/' + registrationId;
        
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = 'csrf_token';
        token.value = '<?= $_SESSION['csrf_token'] ?>';
        form.appendChild(token);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
