<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">📚 Course Management</h1>
        <?php if (in_array($user['role_name'], ['SUPER_ADMIN', 'HOD'])): ?>
            <a href="/courses/create" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add New Course
            </a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="/courses" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-select">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>" <?= isset($filters['department_id']) && $filters['department_id'] == $dept['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dept['code']) ?> - <?= htmlspecialchars($dept['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="level_id" class="form-label">Level</label>
                        <select name="level_id" id="level_id" class="form-select">
                            <option value="">All Levels</option>
                            <?php foreach ($levels as $level): ?>
                                <option value="<?= $level['id'] ?>" <?= isset($filters['level_id']) && $filters['level_id'] == $level['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($level['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="semester_number" class="form-label">Semester</label>
                        <select name="semester_number" id="semester_number" class="form-select">
                            <option value="">All Semesters</option>
                            <option value="1" <?= isset($filters['semester_number']) && $filters['semester_number'] == 1 ? 'selected' : '' ?>>First Semester</option>
                            <option value="2" <?= isset($filters['semester_number']) && $filters['semester_number'] == 2 ? 'selected' : '' ?>>Second Semester</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="is_elective" class="form-label">Course Type</label>
                        <select name="is_elective" id="is_elective" class="form-select">
                            <option value="">All Types</option>
                            <option value="0" <?= isset($filters['is_elective']) && $filters['is_elective'] === 0 ? 'selected' : '' ?>>Core/Compulsory</option>
                            <option value="1" <?= isset($filters['is_elective']) && $filters['is_elective'] === 1 ? 'selected' : '' ?>>Elective</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="is_active" class="form-label">Status</label>
                        <select name="is_active" id="is_active" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1" <?= isset($filters['is_active']) && $filters['is_active'] === 1 ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= isset($filters['is_active']) && $filters['is_active'] === 0 ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                Courses 
                <span class="badge bg-secondary"><?= count($courses) ?></span>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($courses)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    No courses found. <?php if (in_array($user['role_name'], ['SUPER_ADMIN', 'HOD'])): ?>
                        <a href="/courses/create" class="alert-link">Create one now</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Department</th>
                                <th>Level</th>
                                <th>Semester</th>
                                <th>Credits</th>
                                <th>Type</th>
                                <th>Prerequisite</th>
                                <th>Registrations</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($course['code']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($course['title']) ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($course['department_code']) ?>
                                        </small>
                                    </td>
                                    <td><?= $course['level_number'] ?> Level</td>
                                    <td>
                                        <span class="badge bg-<?= $course['semester_number'] == 1 ? 'info' : 'warning' ?>">
                                            Semester <?= $course['semester_number'] ?>
                                        </span>
                                    </td>
                                    <td><?= $course['credit_units'] ?> units</td>
                                    <td>
                                        <?php if ($course['is_elective']): ?>
                                            <span class="badge bg-secondary">Elective</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Core</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($course['prerequisite_count'] > 0): ?>
                                            <span class="badge bg-warning text-dark">
                                                <?= $course['prerequisite_count'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <?= $course['registration_count'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($course['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/courses/<?= $course['id'] ?>" class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (in_array($user['role_name'], ['SUPER_ADMIN', 'HOD'])): ?>
                                                <a href="/courses/<?= $course['id'] ?>/edit" class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="/courses/<?= $course['id'] ?>/toggle" class="d-inline" onsubmit="return confirm('Are you sure you want to toggle this course status?')">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <button type="submit" class="btn btn-outline-<?= $course['is_active'] ? 'danger' : 'success' ?>" title="<?= $course['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                                        <i class="fas fa-power-off"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
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
