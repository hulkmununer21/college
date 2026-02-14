<div class="container">
    <div class="hero-section">
        <h1>Welcome to <?= APP_NAME ?></h1>
        <p class="lead">A comprehensive school management system for tertiary institutions</p>
        <div class="hero-buttons">
            <a href="<?= BASE_URL ?>/auth/login" class="btn btn-primary">View All Login Options</a>
            <a href="<?= BASE_URL ?>/home/about" class="btn btn-secondary">Learn More</a>
        </div>
    </div>

    <div class="features-section">
        <h2>Quick Portal Access</h2>
        <div class="features-grid">
            <div class="feature-card">
                <h3>👨‍💼 Administrator</h3>
                <p>System administrators and admission officers</p>
                <a href="<?= BASE_URL ?>/auth/admin-login" class="btn btn-sm btn-primary">Admin Login →</a>
            </div>
            <div class="feature-card">
                <h3>🎓 Student</h3>
                <p>Access student portal and services</p>
                <a href="<?= BASE_URL ?>/auth/student-login" class="btn btn-sm btn-primary">Student Login →</a>
            </div>
            <div class="feature-card">
                <h3>👨‍🏫 Lecturer</h3>
                <p>Faculty members and course instructors</p>
                <a href="<?= BASE_URL ?>/auth/lecturer-login" class="btn btn-sm btn-primary">Lecturer Login →</a>
            </div>
            <div class="feature-card">
                <h3>👨‍💼 HOD</h3>
                <p>Heads of departments</p>
                <a href="<?= BASE_URL ?>/auth/hod-login" class="btn btn-sm btn-primary">HOD Login →</a>
            </div>
            <div class="feature-card">
                <h3>💰 Bursar</h3>
                <p>Financial office and payments</p>
                <a href="<?= BASE_URL ?>/auth/bursar-login" class="btn btn-sm btn-primary">Bursar Login →</a>
            </div>
        </div>
    </div>

    <div class="features-section">
        <h2>Key Features</h2>
        <div class="features-grid">
            <div class="feature-card">
                <h3>Multi-Role Access</h3>
                <p>Separate portals for Admin, Admission, Bursar, HOD, Lecturer, and Student</p>
            </div>
            <div class="feature-card">
                <h3>Academic Management</h3>
                <p>Manage Faculties, Departments, Levels, Sessions, Semesters, and Courses</p>
            </div>
            <div class="feature-card">
                <h3>Course Registration</h3>
                <p>Smart validation for credit units and prerequisites</p>
            </div>
            <div class="feature-card">
                <h3>Result Engine</h3>
                <p>Automated GPA/CGPA computation based on 5.0 grading scale</p>
            </div>
            <div class="feature-card">
                <h3>Fee Management</h3>
                <p>Complete bursary system with payment tracking</p>
            </div>
            <div class="feature-card">
                <h3>Secure & Scalable</h3>
                <p>Built with security best practices and modern architecture</p>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h2>System Information</h2>
        <p><strong>Version:</strong> <?= $version ?></p>
        <p><strong>Architecture:</strong> MVC (Model-View-Controller)</p>
        <p><strong>Technology:</strong> PHP 8.2+ with MySQL</p>
    </div>
</div>
