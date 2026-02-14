<?php $pageTitle = $data['title'] ?? 'Departments'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Manage Departments</h1>
        <a href="/academic-structure/create-department" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Department
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="departmentsTable">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Code</th>
                            <th>Faculty</th>
                            <th>HOD</th>
                            <th>Students</th>
                            <th>Lecturers</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['departments'])): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No departments found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['departments'] as $dept): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($dept['department_name']) ?></strong></td>
                                    <td><span class="badge badge-info"><?= htmlspecialchars($dept['department_code']) ?></span></td>
                                    <td><?= htmlspecialchars($dept['faculty_name']) ?></td>

                                    <td><?= htmlspecialchars($dept['hod_name'] ?? 'Not assigned') ?></td>
                                    <td><?= (int)$dept['student_count'] ?></td>
                                    <td><?= (int)$dept['lecturer_count'] ?></td>
                                    <td>
                                        <?php if ($dept['department_active']): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/academic-structure/view-department/<?= $dept['id'] ?>" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/academic-structure/edit-department/<?= $dept['id'] ?>" class="btn btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/academic-structure/toggle-department/<?= $dept['id'] ?>" 
                                               class="btn btn-<?= $dept['department_active'] ? 'warning' : 'success' ?>"
                                               onclick="return confirm('Toggle department status?')">
                                                <i class="fas fa-<?= $dept['department_active'] ? 'ban' : 'check' ?>"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
