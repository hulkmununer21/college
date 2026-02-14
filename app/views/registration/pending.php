<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <h1 class="h3 mb-4">⏳ Pending Course Registrations</h1>

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

    <!-- Pending Registrations -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                Pending Approvals 
                <span class="badge bg-warning"><?= count($registrations) ?></span>
            </h5>
            <?php if (!empty($registrations)): ?>
                <button type="button" class="btn btn-sm btn-success" onclick="bulkApprove()">
                    <i class="fas fa-check-double me-1"></i>Approve Selected
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (empty($registrations)): ?>
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    No pending registrations at this time. All registrations have been processed.
                </div>
            <?php else: ?>
                <form id="bulkApprovalForm" method="POST" action="/courses/bulk-approve">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
                                    </th>
                                    <th>Student</th>
                                    <th>Matric Number</th>
                                    <th>Course Code</th>
                                    <th>Course Title</th>
                                    <th>Credit Units</th>
                                    <th>Level</th>
                                    <th>Type</th>
                                    <th>Registration Date</th>
                                    <th>Total Credits</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registrations as $reg): ?>
                                    <tr>
                                        <td>
                                            <input 
                                                type="checkbox" 
                                                name="registration_ids[]" 
                                                value="<?= $reg['registration_id'] ?>"
                                                class="registration-checkbox">
                                        </td>
                                        <td><?= htmlspecialchars($reg['student_name']) ?></td>
                                        <td><?= htmlspecialchars($reg['matric_number']) ?></td>
                                        <td><strong><?= htmlspecialchars($reg['code']) ?></strong></td>
                                        <td><?= htmlspecialchars($reg['title']) ?></td>
                                        <td><span class="badge bg-info"><?= $reg['credit_units'] ?></span></td>
                                        <td><?= htmlspecialchars($reg['level_name']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $reg['is_elective'] ? 'secondary' : 'success' ?>">
                                                <?= $reg['is_elective'] ? 'Elective' : 'Core' ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($reg['registration_date'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $reg['total_credits'] > $reg['max_credits'] ? 'danger' : 'primary' ?>">
                                                <?= $reg['total_credits'] ?> / <?= $reg['max_credits'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-success"
                                                    onclick="approveSingle(<?= $reg['registration_id'] ?>)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-danger"
                                                    onclick="rejectSingle(<?= $reg['registration_id'] ?>)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.registration-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

function bulkApprove() {
    const selected = document.querySelectorAll('.registration-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select at least one registration to approve.');
        return;
    }
    
    if (confirm(`Approve ${selected.length} registration(s)?`)) {
        document.getElementById('bulkApprovalForm').submit();
    }
}

function approveSingle(registrationId) {
    if (confirm('Approve this registration?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/courses/approve/' + registrationId;
        
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = 'csrf_token';
        token.value = '<?= $_SESSION['csrf_token'] ?>';
        form.appendChild(token);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectSingle(registrationId) {
    if (confirm('Reject this registration?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/courses/reject/' + registrationId;
        
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
