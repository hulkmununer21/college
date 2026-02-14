<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/dashboard.php'; ?>

<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">👥 Students - <?= htmlspecialchars($course['code']) ?></h1>
            <p class="text-muted mb-0"><?= htmlspecialchars($course['title']) ?></p>
        </div>
        <a href="/courses/my-courses" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to My Courses
        </a>
    </div>

    <!-- Semester Info -->
    <?php if ($semester): ?>
        <div class="alert alert-info mb-4">
            <h6 class="alert-heading mb-1">
                <i class="fas fa-calendar me-2"></i><?= htmlspecialchars($semester['name']) ?>
            </h6>
        </div>
    <?php endif; ?>

    <!-- Students Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                Registered Students 
                <span class="badge bg-primary"><?= count($students) ?></span>
            </h5>
            <div>
                <button class="btn btn-sm btn-success" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Print List
                </button>
                <button class="btn btn-sm btn-primary" onclick="exportToExcel()">
                    <i class="fas fa-file-excel me-1"></i>Export to Excel
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($students)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    No students have been approved for this course yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="studentsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Matric Number</th>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Level</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $index => $student): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($student['matric_number']) ?></strong></td>
                                    <td><?= htmlspecialchars($student['student_name']) ?></td>
                                    <td><?= htmlspecialchars($student['email']) ?></td>
                                    <td><?= htmlspecialchars($student['level_name']) ?></td>
                                    <td><?= date('M d, Y', strtotime($student['registration_date'])) ?></td>
                                    <td>
                                        <span class="badge bg-success">Approved</span>
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

<script>
function exportToExcel() {
    // Simple CSV export
    const table = document.getElementById('studentsTable');
    let csv = [];
    
    // Headers
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent);
    csv.push(headers.join(','));
    
    // Rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td')).map(td => `"${td.textContent.trim()}"`);
        csv.push(cells.join(','));
    });
    
    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = '<?= htmlspecialchars($course['code']) ?>_students.csv';
    a.click();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
