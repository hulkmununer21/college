<?php $pageTitle = $data['title'] ?? 'Faculties'; ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Manage Faculties</h1>
        <a href="/academic-structure/create-faculty" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Faculty
        </a>
    </div>

    <!-- Faculties Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="facultiesTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Dean</th>
                            <th>Departments</th>
                            <th>Students</th>
                            <th>Lecturers</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['faculties'])): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No faculties found. Click "Create Faculty" to add one.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['faculties'] as $faculty): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($faculty['name']) ?></strong>
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
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/academic-structure/view-faculty/<?= $faculty['id'] ?>" 
                                               class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/academic-structure/edit-faculty/<?= $faculty['id'] ?>" 
                                               class="btn btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/academic-structure/toggle-faculty/<?= $faculty['id'] ?>" 
                                               class="btn btn-<?= $faculty['is_active'] ? 'warning' : 'success' ?>"
                                               title="<?= $faculty['is_active'] ? 'Deactivate' : 'Activate' ?>"
                                               onclick="return confirm('Are you sure you want to <?= $faculty['is_active'] ? 'deactivate' : 'activate' ?> this faculty?')">
                                                <i class="fas fa-<?= $faculty['is_active'] ? 'ban' : 'check' ?>"></i>
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

<script>
// Add search/filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control mb-3';
    searchInput.placeholder = 'Search faculties...';
    
    const tableCard = document.querySelector('.card-body');
    tableCard.insertBefore(searchInput, tableCard.firstChild);
    
    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toUpperCase();
        const table = document.getElementById('facultiesTable');
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length - 1; j++) {
                if (cells[j].innerText.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    });
});
</script>
