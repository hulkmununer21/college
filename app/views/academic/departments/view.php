<?php $pageTitle = $data['department']['department_name'] ?? 'Department'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= htmlspecialchars($data['department']['department_name']) ?></h1>
        <div>
            <a href="/academic-structure/edit-department/<?= $data['department']['id'] ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="/academic-structure/departments" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Department Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Code:</th>
                            <td><span class="badge badge-info"><?= htmlspecialchars($data['department']['department_code']) ?></span></td>
                        </tr>
                        <tr>
                            <th>Faculty:</th>
                            <td><?= htmlspecialchars($data['department']['faculty_name']) ?></td>
                        </tr>
                        <tr>
                            <th>HOD:</th>
                            <td><?= htmlspecialchars($data['department']['hod_name'] ?? 'Not assigned') ?></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php if ($data['department']['department_active']): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>

                    <?php if (!empty($data['department']['department_description'])): ?>
                    <hr>
                    <p class="mb-0"><small><?= nl2br(htmlspecialchars($data['department']['department_description'])) ?></small></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
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

        <div class="col-md-8">
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#students">Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#lecturers">Lecturers</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="students">
                    <div class="card">
                        <div class="card-body">
                            <?php if (empty($data['students'])): ?>
                                <p class="text-center text-muted py-4">No students in this department</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Matric Number</th>
                                                <th>Name</th>
                                                <th>Level</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['students'] as $student): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($student['matric_number']) ?></td>
                                                    <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                                    <td><?= htmlspecialchars($student['level_name'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <?php if ($student['is_active']): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Inactive</span>
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

                <div class="tab-pane fade" id="lecturers">
                    <div class="card">
                        <div class="card-body">
                            <?php if (empty($data['lecturers'])): ?>
                                <p class="text-center text-muted py-4">No lecturers in this department</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Staff ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['lecturers'] as $lecturer): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($lecturer['staff_id'] ?? 'N/A') ?></td>
                                                    <td><?= htmlspecialchars($lecturer['first_name'] . ' ' . $lecturer['last_name']) ?></td>
                                                    <td><?= htmlspecialchars($lecturer['email']) ?></td>
                                                    <td>
                                                        <?php if ($lecturer['is_active']): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Inactive</span>
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
            </div>
        </div>
    </div>
</div>
