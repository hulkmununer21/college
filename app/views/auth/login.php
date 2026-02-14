<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
    <style>
        .role-selection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .role-card {
            background: #fff;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .role-card:hover {
            border-color: #4CAF50;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .role-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .role-card h3 {
            margin: 10px 0;
            color: #333;
            font-size: 18px;
        }
        .role-card p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box" style="max-width: 900px;">
            <div class="auth-header">
                <h1><?= APP_NAME ?></h1>
                <p>Select Your Login Portal</p>
            </div>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="flash-messages-auth">
                    <?php foreach ($_SESSION['flash'] as $flash): ?>
                        <div class="alert alert-<?= $flash['type'] ?>">
                            <?= htmlspecialchars($flash['message']) ?>
                        </div>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['flash']); ?>
                </div>
            <?php endif; ?>

            <div class="role-selection-grid">
                <a href="<?= BASE_URL ?>/auth/admin-login" class="role-card">
                    <div class="role-icon">👨‍💼</div>
                    <h3>Administrator</h3>
                    <p>System Admin & Staff</p>
                </a>

                <a href="<?= BASE_URL ?>/auth/student-login" class="role-card">
                    <div class="role-icon">🎓</div>
                    <h3>Student</h3>
                    <p>Student Portal</p>
                </a>

                <a href="<?= BASE_URL ?>/auth/lecturer-login" class="role-card">
                    <div class="role-icon">👨‍🏫</div>
                    <h3>Lecturer</h3>
                    <p>Faculty Portal</p>
                </a>

                <a href="<?= BASE_URL ?>/auth/hod-login" class="role-card">
                    <div class="role-icon">👨‍💼</div>
                    <h3>Head of Department</h3>
                    <p>HOD Portal</p>
                </a>

                <a href="<?= BASE_URL ?>/auth/bursar-login" class="role-card">
                    <div class="role-icon">💰</div>
                    <h3>Bursar</h3>
                    <p>Financial Office</p>
                </a>
            </div>

            <div class="auth-footer" style="margin-top: 30px;">
                <a href="<?= BASE_URL ?>">← Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
