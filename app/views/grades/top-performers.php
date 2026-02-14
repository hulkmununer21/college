<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">🏆 Top Performers - <?= htmlspecialchars($semester['name']) ?></h1>
            <p class="text-muted mb-0"><?= htmlspecialchars($semester['session_name']) ?></p>
        </div>
        <div>
            <form method="GET" class="d-inline">
                <select name="semester_id" class="form-select d-inline w-auto" onchange="this.form.submit()">
                    <?php foreach ($all_semesters as $sem): ?>
                        <option value="<?= $sem['id'] ?>" <?= $sem['id'] == $semester_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sem['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <!-- Top Performers List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                Top <?= count($performers) ?> Students by GPA
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($performers)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    No results available for this semester yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">Rank</th>
                                <th>Matric Number</th>
                                <th>Student Name</th>
                                <th>Department</th>
                                <th>Faculty</th>
                                <th>Level</th>
                                <th>Total Courses</th>
                                <th>Credits Earned</th>
                                <th>GPA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($performers as $index => $student): ?>
                                <tr class="<?= $index < 3 ? 'table-warning' : '' ?>">
                                    <td>
                                        <?php if ($index === 0): ?>
                                            <span class="badge bg-warning text-dark" style="font-size: 1.2em;">
                                                🥇 #1
                                            </span>
                                        <?php elseif ($index === 1): ?>
                                            <span class="badge bg-secondary" style="font-size: 1.1em;">
                                                🥈 #2
                                            </span>
                                        <?php elseif ($index === 2): ?>
                                            <span class="badge bg-secondary" style="font-size: 1.0em;">
                                                🥉 #3
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark">#<?= $index + 1 ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($student['matric_number']) ?></strong></td>
                                    <td><?= htmlspecialchars($student['student_name']) ?></td>
                                    <td><?= htmlspecialchars($student['department_name']) ?></td>
                                    <td><?= htmlspecialchars($student['faculty_name']) ?></td>
                                    <td><?= htmlspecialchars($student['level_name']) ?></td>
                                    <td><?= $student['total_courses'] ?></td>
                                    <td><span class="badge bg-info"><?= $student['total_credits'] ?> Units</span></td>
                                    <td>
                                        <strong class="text-<?= $student['gpa'] >= 4.5 ? 'primary' : ($student['gpa'] >= 3.5 ? 'success' : 'warning') ?>">
                                            <?= number_format($student['gpa'], 2) ?>
                                        </strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- GPA Distribution Summary -->
                <div class="mt-4">
                    <h6>Performance Distribution</h6>
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="text-muted">First Class (4.5+)</h6>
                                    <h3 class="text-primary">
                                        <?= count(array_filter($performers, fn($p) => $p['gpa'] >= 4.5)) ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="text-muted">Second Class Upper (3.5-4.49)</h6>
                                    <h3 class="text-success">
                                        <?= count(array_filter($performers, fn($p) => $p['gpa'] >= 3.5 && $p['gpa'] < 4.5)) ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="text-muted">Second Class Lower (2.4-3.49)</h6>
                                    <h3 class="text-warning">
                                        <?= count(array_filter($performers, fn($p) => $p['gpa'] >= 2.4 && $p['gpa'] < 3.5)) ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="text-muted">Third Class & Below (<2.4)</h6>
                                    <h3 class="text-danger">
                                        <?= count(array_filter($performers, fn($p) => $p['gpa'] < 2.4)) ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
