<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">📚 <?= htmlspecialchars($course['code']) ?> - <?= htmlspecialchars($course['title']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/courses">Courses</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($course['code']) ?></li>
                </ol>
            </nav>
        </div>
        <?php if (in_array($user['role_name'], ['SUPER_ADMIN', 'HOD'])): ?>
            <div>
                <a href="/courses/edit/<?= $course['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Edit Course
                </a>
                <button 
                    type="button" 
                    class="btn btn-<?= $course['is_active'] ? 'warning' : 'success' ?>"
                    onclick="toggleCourse(<?= $course['id'] ?>)">
                    <i class="fas fa-power-off me-1"></i>
                    <?= $course['is_active'] ? 'Deactivate' : 'Activate' ?>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <!-- Course Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Course Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Course Code:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-primary fs-6"><?= htmlspecialchars($course['code']) ?></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Course Title:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= htmlspecialchars($course['title']) ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Department:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= htmlspecialchars($course['department_code']) ?> - <?= htmlspecialchars($course['department_name']) ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Level:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= htmlspecialchars($course['level_name']) ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Semester:</strong>
                        </div>
                        <div class="col-md-8">
                            <?= $course['semester_number'] == 1 ? 'First' : 'Second' ?> Semester
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Credit Units:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-info"><?= htmlspecialchars($course['credit_units']) ?> Units</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Course Type:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-<?= $course['is_elective'] ? 'secondary' : 'success' ?>">
                                <?= $course['is_elective'] ? 'Elective' : 'Core/Compulsory' ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-<?= $course['is_active'] ? 'success' : 'danger' ?>">
                                <?= $course['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </div>
                    </div>
                    <?php if (!empty($course['description'])): ?>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Description:</strong>
                            </div>
                            <div class="col-md-8">
                                <?= nl2br(htmlspecialchars($course['description'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Prerequisites -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-list-check me-2"></i>Prerequisites</h5>
                    <?php if (in_array($user['role_name'], ['SUPER_ADMIN', 'HOD'])): ?>
                        <button 
                            type="button" 
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#addPrerequisiteModal">
                            <i class="fas fa-plus me-1"></i>Add Prerequisite
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($prerequisites)): ?>
                        <p class="text-muted mb-0">No prerequisites for this course.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($prerequisites as $prereq): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($prereq['prerequisite_code']) ?></strong> - 
                                        <?= htmlspecialchars($prereq['prerequisite_title']) ?>
                                        <span class="badge bg-info ms-2"><?= htmlspecialchars($prereq['credit_units']) ?> Units</span>
                                    </div>
                                    <?php if (in_array($user['role_name'], ['SUPER_ADMIN', 'HOD'])): ?>
                                        <form method="POST" action="/courses/remove-prerequisite/<?= $course['id'] ?>/<?= $prereq['prerequisite_course_id'] ?>" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove this prerequisite?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Total Registrations</h6>
                        <h3><?= $stats['total_students'] ?? 0 ?></h3>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Approved</h6>
                        <h4 class="text-success"><?= $stats['approved_students'] ?? 0 ?></h4>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Pending</h6>
                        <h4 class="text-warning"><?= $stats['pending_students'] ?? 0 ?></h4>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Dropped</h6>
                        <h4 class="text-danger"><?= $stats['dropped_students'] ?? 0 ?></h4>
                    </div>
                </div>
            </div>

            <!-- Assigned Lecturers -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Lecturers</h5>
                    <?php if (in_array($user['role_name'], ['SUPER_ADMIN', 'HOD'])): ?>
                        <button 
                            type="button" 
                            class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#assignLecturerModal">
                            <i class="fas fa-plus me-1"></i>Assign
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($lecturers)): ?>
                        <p class="text-muted mb-0">No lecturers assigned yet.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($lecturers as $lecturer): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong><?= htmlspecialchars($lecturer['lecturer_name']) ?></strong>
                                            <?php if ($lecturer['is_coordinator']): ?>
                                                <span class="badge bg-primary ms-2">Coordinator</span>
                                            <?php endif; ?>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($lecturer['lecturer_email']) ?></small>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Prerequisite Modal -->
<?php if (in_array($user['role_name'], ['SUPER_ADMIN', 'HOD'])): ?>
<div class="modal fade" id="addPrerequisiteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/courses/add-prerequisite/<?= $course['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Add Prerequisite</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="prerequisite_course_id" class="form-label">Select Prerequisite Course</label>
                        <select name="prerequisite_course_id" id="prerequisite_course_id" class="form-select" required>
                            <option value="">Select a course...</option>
                            <?php foreach ($available_prerequisites as $prereq): ?>
                                <option value="<?= $prereq['id'] ?>">
                                    <?= htmlspecialchars($prereq['code']) ?> - <?= htmlspecialchars($prereq['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Prerequisite</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function toggleCourse(courseId) {
    if (confirm('Are you sure you want to change the status of this course?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/courses/toggle/' + courseId;
        
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
