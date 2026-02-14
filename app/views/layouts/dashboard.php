<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title ?? APP_NAME ?> - <?= APP_NAME ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/images/favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css">
    
    <!-- Additional CSS -->
    <?php if (isset($css)): ?>
        <?php foreach ($css as $cssFile): ?>
            <link rel="stylesheet" href="<?= BASE_URL ?>/css/<?= $cssFile ?>.css">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><?= APP_NAME ?></h2>
            <button class="sidebar-toggle" id="sidebarToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <nav class="sidebar-nav">
            <?php
            $roleCode = $_SESSION['role_code'] ?? '';
            $currentPath = $_SERVER['REQUEST_URI'] ?? '';
            ?>

            <?php if ($roleCode === 'SUPER_ADMIN'): ?>
                <a href="<?= BASE_URL ?>/admin/dashboard" class="nav-item <?= strpos($currentPath, 'admin/dashboard') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>/academic-structure" class="nav-item <?= strpos($currentPath, 'academic-structure') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">🏫</span>
                    <span class="nav-text">Academic Structure</span>
                </a>
                <a href="<?= BASE_URL ?>/courses" class="nav-item <?= strpos($currentPath, 'courses') !== false && strpos($currentPath, 'courses/pending') === false ? 'active' : '' ?>">
                    <span class="nav-icon">📚</span>
                    <span class="nav-text">Course Management</span>
                </a>
                <a href="<?= BASE_URL ?>/courses/pending-approvals" class="nav-item <?= strpos($currentPath, 'courses/pending') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">✅</span>
                    <span class="nav-text">Registration Approvals</span>
                </a>
                <a href="<?= BASE_URL ?>/admin/users" class="nav-item <?= strpos($currentPath, 'admin/users') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">Users</span>
                </a>
                <a href="<?= BASE_URL ?>/admin/roles" class="nav-item <?= strpos($currentPath, 'admin/roles') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">🔐</span>
                    <span class="nav-text">Roles</span>
                </a>
                <a href="<?= BASE_URL ?>/grades/pending" class="nav-item <?= strpos($currentPath, 'grades/pending') !== false || strpos($currentPath, 'grades/review') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">✔️</span>
                    <span class="nav-text">Grade Approvals</span>
                </a>

            <?php elseif ($roleCode === 'STUDENT'): ?>
                <a href="<?= BASE_URL ?>/student/dashboard" class="nav-item <?= strpos($currentPath, 'student/dashboard') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>/student/profile" class="nav-item <?= strpos($currentPath, 'student/profile') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">👤</span>
                    <span class="nav-text">My Profile</span>
                </a>
                <a href="<?= BASE_URL ?>/courses/available" class="nav-item <?= strpos($currentPath, 'courses/available') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">📚</span>
                    <span class="nav-text">Course Registration</span>
                </a>
                <a href="<?= BASE_URL ?>/courses/my-registrations" class="nav-item <?= strpos($currentPath, 'courses/my-registrations') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">📋</span>
                    <span class="nav-text">My Courses</span>
                </a>
                <a href="<?= BASE_URL ?>/grades/my-results" class="nav-item <?= strpos($currentPath, 'grades/my-results') !== false || strpos($currentPath, 'grades/transcript') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">📝</span>
                    <span class="nav-text">My Results</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">💰</span>
                    <span class="nav-text">Payments</span>
                </a>

            <?php elseif ($roleCode === 'LECTURER'): ?>
                <a href="<?= BASE_URL ?>/courses/my-courses" class="nav-item <?= strpos($currentPath, 'courses/my-courses') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">📚</span>
                    <span class="nav-text">My Courses</span>
                </a>
                <a href="<?= BASE_URL ?>/grades" class="nav-item <?= strpos($currentPath, 'grades') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">📝</span>
                    <span class="nav-text">Grade Entry</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">Students</span>
                </a>

            <?php elseif ($roleCode === 'HOD'): ?>
                <a href="<?= BASE_URL ?>/hod/dashboard" class="nav-item <?= strpos($currentPath, 'hod/dashboard') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>/academic-structure" class="nav-item <?= strpos($currentPath, 'academic-structure') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">🏫</span>
                    <span class="nav-text">Academic Structure</span>
                </a>
                <a href="<?= BASE_URL ?>/courses" class="nav-item <?= strpos($currentPath, 'courses') !== false && strpos($currentPath, 'courses/pending') === false ? 'active' : '' ?>">
                    <span class="nav-icon">📚</span>
                    <span class="nav-text">Course Management</span>
                </a>
                <a href="<?= BASE_URL ?>/courses/pending-approvals" class="nav-item <?= strpos($currentPath, 'courses/pending') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">✅</span>
                    <span class="nav-text">Registration Approvals</span>
                </a>
                <a href="<?= BASE_URL ?>/grades/pending" class="nav-item <?= strpos($currentPath, 'grades/pending') !== false || strpos($currentPath, 'grades/review') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">✔️</span>
                    <span class="nav-text">Grade Approvals</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">🏢</span>
                    <span class="nav-text">Department</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">👨‍🏫</span>
                    <span class="nav-text">Lecturers</span>
                </a>

            <?php elseif ($roleCode === 'BURSAR'): ?>
                <a href="<?= BASE_URL ?>/bursar/dashboard" class="nav-item <?= strpos($currentPath, 'bursar/dashboard') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">💰</span>
                    <span class="nav-text">Payments</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">📄</span>
                    <span class="nav-text">Invoices</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">📈</span>
                    <span class="nav-text">Reports</span>
                </a>

            <?php elseif ($roleCode === 'ADMISSION_OFFICER'): ?>
                <a href="<?= BASE_URL ?>/admission/dashboard" class="nav-item <?= strpos($currentPath, 'admission/dashboard') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">📝</span>
                    <span class="nav-text">Applications</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">✅</span>
                    <span class="nav-text">Admissions</span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= BASE_URL ?>/auth/logout" class="nav-item logout">
                <span class="nav-icon">🚪</span>
                <span class="nav-text">Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <nav class="top-nav">
            <div class="top-nav-left">
                <button class="mobile-toggle" id="mobileToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h1 class="page-title"><?= $title ?? 'Dashboard' ?></h1>
            </div>
            <div class="top-nav-right">
                <div class="user-menu">
                    <span class="user-name"><?= $_SESSION['full_name'] ?? 'User' ?></span>
                    <span class="user-role"><?= $_SESSION['role_name'] ?? 'Role' ?></span>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="flash-messages-dashboard">
                <?php foreach ($_SESSION['flash'] as $flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?>">
                        <?= htmlspecialchars($flash['message']) ?>
                        <button class="alert-close">&times;</button>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="content-wrapper">
            <?= $content ?? '' ?>
        </div>

        <!-- Footer -->
        <footer class="dashboard-footer">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved. | Version <?= APP_VERSION ?></p>
        </footer>
    </div>

    <!-- JavaScript -->
    <script src="<?= BASE_URL ?>/js/dashboard.js"></script>
    
    <!-- Additional JavaScript -->
    <?php if (isset($js)): ?>
        <?php foreach ($js as $jsFile): ?>
            <script src="<?= BASE_URL ?>/js/<?= $jsFile ?>.js"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
