<?php $pageTitle = $data['title'] ?? 'Levels'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Manage Academic Levels</h1>
        <a href="/academic-structure/create-level" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Level
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Level Number</th>
                            <th>Credit Units Range</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['levels'])): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No levels found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['levels'] as $level): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($level['name']) ?></strong></td>
                                    <td><span class="badge badge-primary"><?= $level['level_number'] ?></span></td>
                                    <td><?= $level['min_credit_units'] ?> - <?= $level['max_credit_units'] ?> units</td>
                                    <td><?= (int)$level['student_count'] ?></td>
                                    <td>
                                        <?php if ($level['is_active']): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/academic-structure/edit-level/<?= $level['id'] ?>" class="btn btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/academic-structure/toggle-level/<?= $level['id'] ?>" 
                                               class="btn btn-<?= $level['is_active'] ? 'warning' : 'success' ?>"
                                               onclick="return confirm('Toggle level status?')">
                                                <i class="fas fa-<?= $level['is_active'] ? 'ban' : 'check' ?>"></i>
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
