<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-card">
        <h1>Welcome back, <?= htmlspecialchars($user['name']) ?>!</h1>
        <p>Here's what's happening with your account today.</p>
    </div>

    <!-- Quick Actions -->
    <div class="stats-grid">
        <div class="stat-card clickable">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <span>📚</span>
            </div>
            <div class="stat-content">
                <h3>0</h3>
                <p>Registered Courses</p>
            </div>
        </div>

        <div class="stat-card clickable">
            <div class="stat-icon" style="background: linear-gradient(135deg, #48bb78 0%, #38b2ac 100%);">
                <span>📝</span>
            </div>
            <div class="stat-content">
                <h3>0.00</h3>
                <p>Current CGPA</p>
            </div>
        </div>

        <div class="stat-card clickable">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ed8936 0%, #f56565 100%);">
                <span>💰</span>
            </div>
            <div class="stat-content">
                <h3>₦0</h3>
                <p>Outstanding Fees</p>
            </div>
        </div>

        <div class="stat-card clickable">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);">
                <span>📊</span>
            </div>
            <div class="stat-content">
                <h3>0</h3>
                <p>Semesters</p>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="content-card">
        <div class="card-header">
            <h2>Recent Activity</h2>
        </div>
        <div class="activity-list">
            <div class="activity-item">
                <div class="activity-icon" style="background: #667eea;">👋</div>
                <div class="activity-content">
                    <p><strong>Welcome to the portal!</strong></p>
                    <span class="activity-time">Just now</span>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon" style="background: #48bb78;">✓</div>
                <div class="activity-content">
                    <p>Account created successfully</p>
                    <span class="activity-time"><?= date('M d, Y', strtotime($student['created_at'] ?? 'now')) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="content-card">
        <div class="card-header">
            <h2>Quick Links</h2>
        </div>
        <div class="quick-links">
            <a href="<?= BASE_URL ?>/student/profile" class="quick-link-item">
                <span class="quick-link-icon">👤</span>
                <span class="quick-link-text">View Profile</span>
            </a>
            <a href="#" class="quick-link-item">
                <span class="quick-link-icon">📚</span>
                <span class="quick-link-text">Register Courses</span>
            </a>
            <a href="#" class="quick-link-item">
                <span class="quick-link-icon">📝</span>
                <span class="quick-link-text">Check Results</span>
            </a>
            <a href="#" class="quick-link-item">
                <span class="quick-link-icon">💰</span>
                <span class="quick-link-text">Make Payment</span>
            </a>
        </div>
    </div>
</div>
