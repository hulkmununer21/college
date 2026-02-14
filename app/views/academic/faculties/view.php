<?php $pageTitle = $data['faculty']['name'] ?? 'Faculty Details'; ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= htmlspecialchars($data['faculty']['name']) ?></h1>
        <div>
            <a href="/academic-structure/edit-faculty/<?= $data['faculty']['id'] ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="/academic-structure/faculties" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Faculty Info -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Faculty Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Code:</th>
                            <td><span class="badge badge-secondary"><?= htmlspecialchars($data['faculty']['code']) ?></span></td>
                        </tr>
                        <tr>
                            <th>Dean:</th>
                            <td><?= htmlspecialchars($data['faculty']['dean_name'] ?? 'Not assigned') ?></td>
                        </tr>
                        <?php if (isset($data['faculty']['dean_email'])): ?>
                        <tr>
                            <th>Dean Email:</th>
                            <td><?= htmlspecialchars($data['faculty']['dean_email']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php if ($data['faculty']['is_active']): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?= date('M j, Y', strtotime($data['faculty']['created_at'])) ?></td>
                        </tr>
                    </table>

                    <?php if (!empty($data['faculty']['description'])): ?>
                    <hr>
                    <p class="mb-0"><small><?= nl2br(htmlspecialchars($data['faculty']['description'])) ?></small></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h3 class="mb-0"><?= (int)$data['stats']['department_count'] ?></h3>
                        <small class="text-muted">Departments</small>
                    </div>
                    <div class="mb-3">
                        <h3 class="mb-0"><?= (int)$data['stats']['student_count'] ?></h3>
                        <small class="text-muted">Students</small>
                    </div>
                    <div class="mb-0">
                        <h3 class="mb-0"><?= (int)$data['stats']['lecturer_count'] ?></h3>
                        <small class="text-muted">Lecturers</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departments List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Departments</h5>
                    <a href="/academic-structure/create-department?faculty_id=<?= $data['faculty']['id'] ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add Department
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($data['departments'])): ?>
                        <p class="text-center text-muted py-4">No departments in this faculty yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>HOD</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['departments'] as $dept): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($dept['name']) ?></td>
                                            <td><span class="badge badge-info"><?= htmlspecialchars($dept['code']) ?></span></td>
                                            <td><?= htmlspecialchars($dept['hod_name'] ?? 'Not assigned') ?></td>
                                            <td>
                                                <?php if ($dept['is_active']): ?>
                                                    <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="/academic-structure/view-department/<?= $dept['id'] ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/academic-structure/edit-department/<?= $dept['id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
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
    </div>
</div>
