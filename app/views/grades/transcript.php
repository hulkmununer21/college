<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php 
$isPrint = isset($_GET['print']) && $_GET['print'] == 1;
if (!$isPrint): 
    require_once __DIR__ . '/../layouts/dashboard.php'; 
endif;
?>

<div class="container<?= $isPrint ? '' : '-fluid px-4' ?>">
    <!-- Header -->
    <div class="text-center mb-4">
        <h2 class="mb-1">ACADEMIC TRANSCRIPT</h2>
        <h5 class="text-muted"><?= htmlspecialchars($transcript['student']['university_name'] ?? 'University Name') ?></h5>
        <h6 class="text-muted"><?= htmlspecialchars($transcript['student']['faculty_name']) ?></h6>
        <h6 class="text-muted"><?= htmlspecialchars($transcript['student']['department_name']) ?></h6>
    </div>

    <!-- Student Information -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?= htmlspecialchars($transcript['student']['name']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Matric Number:</strong></td>
                            <td><?= htmlspecialchars($transcript['student']['matric_number']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Level:</strong></td>
                            <td><?= htmlspecialchars($transcript['student']['level_name']) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Department:</strong></td>
                            <td><?= htmlspecialchars($transcript['student']['department_name']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Faculty:</strong></td>
                            <td><?= htmlspecialchars($transcript['student']['faculty_name']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Date Generated:</strong></td>
                            <td><?= date('F d, Y') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Semester Results -->
    <?php foreach ($transcript['semesters'] as $semester): ?>
        <div class="card mb-3">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between">
                    <strong><?= htmlspecialchars($semester['semester_name']) ?> - <?= htmlspecialchars($semester['session_name']) ?></strong>
                    <span>Level: <?= htmlspecialchars($semester['level_name']) ?></span>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Credits</th>
                            <th>Score</th>
                            <th>Grade</th>
                            <th>GP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($semester['courses'] as $course): ?>
                            <tr>
                                <td><?= htmlspecialchars($course['course_code']) ?></td>
                                <td><?= htmlspecialchars($course['course_title']) ?></td>
                                <td><?= $course['credit_units'] ?></td>
                                <td><?= number_format($course['total_score'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $course['grade'] == 'A' ? 'primary' : ($course['grade'] == 'B' ? 'info' : ($course['grade'] == 'C' ? 'success' : ($course['grade'] == 'D' ? 'warning' : ($course['grade'] == 'E' ? 'secondary' : 'danger')))) ?>">
                                        <?= $course['grade'] ?>
                                    </span>
                                </td>
                                <td><?= number_format($course['grade_point'], 1) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2"><strong>Semester Summary</strong></td>
                            <td><strong><?= $semester['total_credits'] ?> Units</strong></td>
                            <td colspan="2"></td>
                            <td><strong>GPA: <?= number_format($semester['gpa'], 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Overall Summary -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Overall Academic Performance</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <h6 class="text-muted">Cumulative GPA</h6>
                    <h2 class="text-primary"><?= number_format($transcript['overall']['cgpa'], 2) ?></h2>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Total Credits</h6>
                    <h2><?= $transcript['overall']['cumulative_credits'] ?></h2>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Total Courses</h6>
                    <h2><?= $transcript['overall']['total_courses'] ?></h2>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted">Class of Degree</h6>
                    <h2><?= htmlspecialchars($transcript['overall']['class_of_degree']) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Grading System -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Grading System</h6>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Score Range</th>
                        <th>Grade</th>
                        <th>Grade Point</th>
                        <th>Interpretation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>70 - 100</td>
                        <td><span class="badge bg-primary">A</span></td>
                        <td>5.0</td>
                        <td>Excellent</td>
                    </tr>
                    <tr>
                        <td>60 - 69</td>
                        <td><span class="badge bg-info">B</span></td>
                        <td>4.0</td>
                        <td>Very Good</td>
                    </tr>
                    <tr>
                        <td>50 - 59</td>
                        <td><span class="badge bg-success">C</span></td>
                        <td>3.0</td>
                        <td>Good</td>
                    </tr>
                    <tr>
                        <td>45 - 49</td>
                        <td><span class="badge bg-warning">D</span></td>
                        <td>2.0</td>
                        <td>Fair</td>
                    </tr>
                    <tr>
                        <td>40 - 44</td>
                        <td><span class="badge bg-secondary">E</span></td>
                        <td>1.0</td>
                        <td>Pass</td>
                    </tr>
                    <tr>
                        <td>0 - 39</td>
                        <td><span class="badge bg-danger">F</span></td>
                        <td>0.0</td>
                        <td>Fail</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Print Actions -->
    <?php if (!$isPrint): ?>
        <div class="text-center mb-4">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-1"></i>Print Transcript
            </button>
            <a href="/grades/my-results" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Results
            </a>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="text-center mt-4 mb-4">
        <small class="text-muted">
            This is an official transcript generated on <?= date('F d, Y') ?> at <?= date('h:i A') ?>
        </small>
    </div>
</div>

<style>
@media print {
    .btn, .navbar, .sidebar { display: none !important; }
    body { background: white !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>

<?php if (!$isPrint): ?>
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<?php endif; ?>
