<?php
$pageTitle = $data['title'] ?? 'Academic Structure';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Academic Structure Overview</h1>
    </div>

    <!-- Current Semester Info -->
   <?php if (isset($data['current_semester']) && $data['current_semester']): ?>
    <div class="alert alert-info">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>Current Session:</strong> <?= htmlspecialchars($data['current_semester']['session_name']) ?> &nbsp;|&nbsp;
                <strong>Semester:</strong> <?= htmlspecialchars($data['current_semester']['semester_name']) ?>
            </div>
            <div>
                <?php if ($data['current_semester']['is_registration_open']): ?>
                    <span class="badge badge-success">Registration Open</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Registration Closed</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Faculties</h6>
                            <h2 class="mb-0"><?= count($data['faculties']) ?></h2>
                        </div>
                        <div class="display-4">
                            <i class="fas fa-university"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="/academic-structure/faculties" class="text-white small">View All →</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Departments</h6>
                            <h2 class="mb-0"><?= count($data['departments']) ?></h2>
                        </div>
                        <div class="display-4">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="/academic-structure/departments" class="text-white small">View All →</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Levels</h6>
                            <h2 class="mb-0"><?= count($data['levels']) ?></h2>
                        </div>
                        <div class="display-4">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="/academic-structure/levels" class="text-white small">View All →</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Sessions</h6>
                            <h2 class="mb-0"><?= count($data['sessions']) ?></h2>
                        </div>
                        <div class="display-4">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="/academic-structure/sessions" class="text-white small">View All →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="/academic-structure/create-faculty" class="btn btn-primary btn-block">
                        <i class="fas fa-plus"></i> Create Faculty
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="/academic-structure/create-department" class="btn btn-success btn-block">
                        <i class="fas fa-plus"></i> Create Department
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="/academic-structure/create-level" class="btn btn-info btn-block">
                        <i class="fas fa-plus"></i> Create Level
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="/academic-structure/create-session" class="btn btn-warning btn-block">
                        <i class="fas fa-plus"></i> Create Session
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculties Overview -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Faculties</h5>
            <a href="/academic-structure/faculties" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Dean</th>
                            <th>Departments</th>
                            <th>Students</th>
                            <th>Lecturers</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['faculties'])): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No faculties found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['faculties'] as $faculty): ?>
                                <tr>
                                    <td>
                                        <a href="/academic-structure/view-faculty/<?= $faculty['id'] ?>">
                                            <?= htmlspecialchars($faculty['name']) ?>
                                        </a>
                                    </td>
                                    <td><span class="badge badge-secondary"><?= htmlspecialchars($faculty['code']) ?></span></td>
                                    <td><?= htmlspecialchars($faculty['dean_name'] ?? 'Not assigned') ?></td>
                                    <td><?= (int)$faculty['department_count'] ?></td>
                                    <td><?= (int)$faculty['student_count'] ?></td>
                                    <td><?= (int)$faculty['lecturer_count'] ?></td>
                                    <td>
                                        <?php if ($faculty['is_active']): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Levels Overview -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Academic Levels</h5>
            <a href="/academic-structure/levels" class="btn btn-sm btn-info">Manage Levels</a>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($data['levels'] as $level): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($level['name']) ?></h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        Credit Units: <?= $level['min_credit_units'] ?> - <?= $level['max_credit_units'] ?>
                                    </small>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-primary"><?= (int)$level['student_count'] ?> Students</span>
                                    <?php if ($level['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactive</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.display-4 {
    font-size: 3rem;
    opacity: 0.3;
}

.badge {
    padding: 0.35rem 0.65rem;
}

.table td {
    vertical-align: middle;
}
</style>
