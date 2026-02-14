<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">📝 <?= htmlspecialchars($course['code']) ?> - Grade Entry</h1>
            <p class="text-muted mb-0"><?= htmlspecialchars($course['title']) ?></p>
        </div>
        <div>
            <button type="button" class="btn btn-success" onclick="bulkSave()">
                <i class="fas fa-save me-1"></i>Save All Grades
            </button>
            <button type="button" class="btn btn-primary" onclick="submitForApproval()">
                <i class="fas fa-paper-plane me-1"></i>Submit for Approval
            </button>
        </div>
    </div>

    <!-- Semester Info -->
    <?php if ($semester): ?>
        <div class="alert alert-info mb-4">
            <h6 class="alert-heading mb-1">
                <i class="fas fa-calendar me-2"></i><?= htmlspecialchars($semester['name']) ?>
            </h6>
        </div>
    <?php endif; ?>

    <!-- Statistics Card -->
    <?php if (!empty($stats) && $stats['total_students'] > 0): ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2">
                        <h6 class="text-muted">Total Students</h6>
                        <h4><?= $stats['total_students'] ?></h4>
                    </div>
                    <div class="col-md-2">
                        <h6 class="text-muted">Average Score</h6>
                        <h4><?= number_format($stats['average_score'] ?? 0, 2) ?>%</h4>
                    </div>
                    <div class="col-md-2">
                        <h6 class="text-muted">Pass Rate</h6>
                        <h4 class="text-success"><?= number_format($stats['pass_rate'] ?? 0, 2) ?>%</h4>
                    </div>
                    <div class="col-md-2">
                        <h6 class="text-muted">Passed</h6>
                        <h4 class="text-success"><?= $stats['passed'] ?? 0 ?></h4>
                    </div>
                    <div class="col-md-2">
                        <h6 class="text-muted">Failed</h6>
                        <h4 class="text-danger"><?= $stats['failed'] ?? 0 ?></h4>
                    </div>
                    <div class="col-md-2">
                        <h6 class="text-muted">Avg Grade Point</h6>
                        <h4><?= number_format($stats['average_gp'] ?? 0, 2) ?></h4>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Grade Entry Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                Student Grades
                <span class="badge bg-primary"><?= count($students) ?></span>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($students)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    No students registered for this course.
                </div>
            <?php else: ?>
                <form id="gradeEntryForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="semester_id" value="<?= $semester['id'] ?>">

                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="gradeTable">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Matric Number</th>
                                    <th>Student Name</th>
                                    <th width="100">CA Score <small class="text-muted">(0-40)</small></th>
                                    <th width="100">Exam Score <small class="text-muted">(0-60)</small></th>
                                    <th width="100">Total</th>
                                    <th width="80">Grade</th>
                                    <th width="80">Grade Point</th>
                                    <th width="100">Status</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $index => $student): ?>
                                    <tr data-registration-id="<?= $student['id'] ?>" data-student-id="<?= $student['student_id'] ?>">
                                        <td><?= $index + 1 ?></td>
                                        <td><strong><?= htmlspecialchars($student['matric_number']) ?></strong></td>
                                        <td><?= htmlspecialchars($student['student_name']) ?></td>
                                        <td>
                                            <input 
                                                type="number" 
                                                class="form-control form-control-sm ca-score" 
                                                name="ca_score_<?= $student['id'] ?>"
                                                value="<?= $student['ca_score'] ?? '' ?>"
                                                min="0" 
                                                max="40" 
                                                step="0.01"
                                                onchange="calculateTotal(this)">
                                        </td>
                                        <td>
                                            <input 
                                                type="number" 
                                                class="form-control form-control-sm exam-score" 
                                                name="exam_score_<?= $student['id'] ?>"
                                                value="<?= $student['exam_score'] ?? '' ?>"
                                                min="0" 
                                                max="60" 
                                                step="0.01"
                                                onchange="calculateTotal(this)">
                                        </td>
                                        <td>
                                            <strong class="total-score"><?= $student['total_score'] ?? 0 ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary grade-letter"><?= $student['grade'] ?? '-' ?></span>
                                        </td>
                                        <td>
                                            <span class="grade-point"><?= $student['grade_point'] ?? '-' ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($student['status'])): ?>
                                                <span class="badge bg-<?= $student['status'] == 'approved' ? 'success' : ($student['status'] == 'submitted' ? 'warning' : 'secondary') ?>">
                                                    <?= ucfirst($student['status']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark">Not Entered</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-primary save-btn"
                                                onclick="saveGrade(<?= $student['id'] ?>, <?= $student['student_id'] ?>)">
                                                <i class="fas fa-save"></i> Save
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>

                <!-- Legend -->
                <div class="mt-3">
                    <h6>Grading Scale:</h6>
                    <span class="badge bg-primary me-2">A: 70-100 (5.0)</span>
                    <span class="badge bg-info me-2">B: 60-69 (4.0)</span>
                    <span class="badge bg-success me-2">C: 50-59 (3.0)</span>
                    <span class="badge bg-warning me-2">D: 45-49 (2.0)</span>
                    <span class="badge bg-secondary me-2">E: 40-44 (1.0)</span>
                    <span class="badge bg-danger">F: 0-39 (0.0)</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Calculate grade from total score
function calculateGrade(totalScore) {
    if (totalScore >= 70) return { letter: 'A', point: 5.0 };
    if (totalScore >= 60) return { letter: 'B', point: 4.0 };
    if (totalScore >= 50) return { letter: 'C', point: 3.0 };
    if (totalScore >= 45) return { letter: 'D', point: 2.0 };
    if (totalScore >= 40) return { letter: 'E', point: 1.0 };
    return { letter: 'F', point: 0.0 };
}

// Calculate total and update grade when scores change
function calculateTotal(inputElement) {
    const row = inputElement.closest('tr');
    const caScore = parseFloat(row.querySelector('.ca-score').value) || 0;
    const examScore = parseFloat(row.querySelector('.exam-score').value) || 0;
    const totalScore = caScore + examScore;
    
    const grade = calculateGrade(totalScore);
    
    row.querySelector('.total-score').textContent = totalScore.toFixed(2);
    row.querySelector('.grade-letter').textContent = grade.letter;
    row.querySelector('.grade-point').textContent = grade.point.toFixed(1);
    
    // Update badge color
    const gradeBadge = row.querySelector('.grade-letter');
    gradeBadge.className = 'badge';
    if (grade.letter === 'A') gradeBadge.classList.add('bg-primary');
    else if (grade.letter === 'B') gradeBadge.classList.add('bg-info');
    else if (grade.letter === 'C') gradeBadge.classList.add('bg-success');
    else if (grade.letter === 'D') gradeBadge.classList.add('bg-warning');
    else if (grade.letter === 'E') gradeBadge.classList.add('bg-secondary');
    else gradeBadge.classList.add('bg-danger');
}

// Save individual grade
function saveGrade(registrationId, studentId) {
    const row = document.querySelector(`tr[data-registration-id="${registrationId}"]`);
    const caScore = row.querySelector('.ca-score').value;
    const examScore = row.querySelector('.exam-score').value;
    
    if (!caScore || !examScore) {
        alert('Please enter both CA and Exam scores.');
        return;
    }
    
    const formData = new FormData();
    formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?>');
    formData.append('registration_id', registrationId);
    formData.append('student_id', studentId);
    formData.append('course_id', <?= $course['id'] ?>);
    formData.append('semester_id', <?= $semester['id'] ?>);
    formData.append('ca_score', caScore);
    formData.append('exam_score', examScore);
    
    fetch('/grades/save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Grade saved successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the grade.');
    });
}

// Bulk save all grades
function bulkSave() {
    const rows = document.querySelectorAll('#gradeTable tbody tr');
    const grades = [];
    
    rows.forEach(row => {
        const registrationId = row.dataset.registrationId;
        const studentId = row.dataset.studentId;
        const caScore = row.querySelector('.ca-score').value;
        const examScore = row.querySelector('.exam-score').value;
        
        if (caScore && examScore) {
            grades.push({
                registration_id: parseInt(registrationId),
                student_id: parseInt(studentId),
                course_id: <?= $course['id'] ?>,
                semester_id: <?= $semester['id'] ?>,
                ca_score: parseFloat(caScore),
                exam_score: parseFloat(examScore)
            });
        }
    });
    
    if (grades.length === 0) {
        alert('No grades to save. Please enter scores first.');
        return;
    }
    
    if (!confirm(`Save grades for ${grades.length} student(s)?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?>');
    formData.append('grades', JSON.stringify(grades));
    
    fetch('/grades/bulk-save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Successfully saved ${data.count} grade(s)!`);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving grades.');
    });
}

// Submit for approval
function submitForApproval() {
    if (!confirm('Submit all grades for approval? You will not be able to edit them after submission.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/grades/submit/<?= $course['id'] ?>';
    
    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = 'csrf_token';
    token.value = '<?= $_SESSION['csrf_token'] ?>';
    form.appendChild(token);
    
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
