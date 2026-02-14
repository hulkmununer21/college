<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-card">
        <h1>Welcome, <?= htmlspecialchars($user['name']) ?>!</h1>
        <p>Admission Officer Dashboard</p>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <span>📝</span>
            </div>
            <div class="stat-content">
                <h3>0</h3>
                <p>New Applications</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #48bb78 0%, #38b2ac 100%);">
                <span>✓</span>
            </div>
            <div class="stat-content">
                <h3>0</h3>
                <p>Approved</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ed8936 0%, #f56565 100%);">
                <span>⏳</span>
            </div>
            <div class="stat-content">
                <h3>0</h3>
                <p>Pending Review</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);">
                <span>📊</span>
            </div>
            <div class="stat-content">
                <h3>0</h3>
                <p>Total Admissions</p>
            </div>
        </div>
    </div>

    <!-- Content will be added in future modules -->
    <div class="content-card">
        <div class="card-header">
            <h2>Coming Soon</h2>
        </div>
        <p>Admission management features will be available in the next module.</p>
    </div>
</div>
