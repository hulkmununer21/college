<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">📊 Course Statistics - <?= htmlspecialchars($course['code']) ?></h1>
            <p class="text-muted mb-0"><?= htmlspecialchars($course['title']) ?></p>
        </div>
        <a href="/grades" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Courses
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Students</h6>
                    <h2><?= $stats['total_students'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Average Score</h6>
                    <h2><?= number_format($stats['average_score'], 2) ?>%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Pass Rate</h6>
                    <h2 class="text-success"><?= number_format($stats['pass_rate'], 2) ?>%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Passed</h6>
                    <h2 class="text-success"><?= $stats['passed'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Failed</h6>
                    <h2 class="text-danger"><?= $stats['failed'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title text-muted">Avg GP</h6>
                    <h2><?= number_format($stats['average_gp'], 2) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Distribution -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Highest Score</h6>
                    <h2 class="text-success"><?= number_format($stats['max_score'], 2) ?>%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Average Score</h6>
                    <h2><?= number_format($stats['average_score'], 2) ?>%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Lowest Score</h6>
                    <h2 class="text-danger"><?= number_format($stats['min_score'], 2) ?>%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Distribution -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Grade Distribution</h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <?php foreach ($distribution as $dist): ?>
                    <div class="col-md-2">
                        <div class="card border-<?= $dist['grade'] == 'A' ? 'primary' : ($dist['grade'] == 'B' ? 'info' : ($dist['grade'] == 'C' ? 'success' : ($dist['grade'] == 'D' ? 'warning' : ($dist['grade'] == 'E' ? 'secondary' : 'danger')))) ?>">
                            <div class="card-body">
                                <h5 class="card-title">Grade <?= $dist['grade'] ?></h5>
                                <h2><?= $dist['count'] ?></h2>
                                <p class="text-muted mb-0"><?= number_format($dist['percentage'], 1) ?>%</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Visual Bar Chart -->
            <div class="mt-4">
                <?php foreach ($distribution as $dist): ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span><strong>Grade <?= $dist['grade'] ?>:</strong> <?= $dist['count'] ?> students</span>
                            <span><?= number_format($dist['percentage'], 1) ?>%</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-<?= $dist['grade'] == 'A' ? 'primary' : ($dist['grade'] == 'B' ? 'info' : ($dist['grade'] == 'C' ? 'success' : ($dist['grade'] == 'D' ? 'warning' : ($dist['grade'] == 'E' ? 'secondary' : 'danger')))) ?>" 
                                 role="progressbar" 
                                 style="width: <?= $dist['percentage'] ?>%" 
                                 aria-valuenow="<?= $dist['percentage'] ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <?= number_format($dist['percentage'], 1) ?>%
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Grading Scale Reference -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Grading Scale</h6>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Score Range</th>
                        <th>Grade Point</th>
                        <th>Interpretation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge bg-primary">A</span></td>
                        <td>70 - 100</td>
                        <td>5.0</td>
                        <td>Excellent</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-info">B</span></td>
                        <td>60 - 69</td>
                        <td>4.0</td>
                        <td>Very Good</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-success">C</span></td>
                        <td>50 - 59</td>
                        <td>3.0</td>
                        <td>Good</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-warning">D</span></td>
                        <td>45 - 49</td>
                        <td>2.0</td>
                        <td>Fair</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-secondary">E</span></td>
                        <td>40 - 44</td>
                        <td>1.0</td>
                        <td>Pass</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-danger">F</span></td>
                        <td>0 - 39</td>
                        <td>0.0</td>
                        <td>Fail</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
