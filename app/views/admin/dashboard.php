<div class="dashboard-container">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <span>👥</span>
            </div>
            <div class="stat-content">
                <h3><?= $stats['total_users'] ?? 0 ?></h3>
                <p>Total Users</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #48bb78 0%, #38b2ac 100%);">
                <span>✓</span>
            </div>
            <div class="stat-content">
                <h3><?= $stats['active_users'] ?? 0 ?></h3>
                <p>Active Users</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ed8936 0%, #f56565 100%);">
                <span>🔐</span>
            </div>
            <div class="stat-content">
                <h3><?= $stats['total_roles'] ?? 0 ?></h3>
                <p>User Roles</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);">
                <span>📊</span>
            </div>
            <div class="stat-content">
                <h3>100%</h3>
                <p>System Health</p>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="content-card">
        <div class="card-header">
            <h2>Recent Users</h2>
            <a href="<?= BASE_URL ?>/admin/users" class="btn btn-primary btn-sm">View All</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_users)): ?>
                        <?php foreach ($recent_users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($user['role_name']) ?></span></td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
