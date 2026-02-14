<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1><?= APP_NAME ?></h1>
                <p><?= $title ?></p>
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

            <div class="auth-info">
                <p>Enter your new password below.</p>
            </div>

            <form method="POST" action="<?= BASE_URL ?>/auth/process-reset-password" class="auth-form" id="resetForm">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="token" value="<?= $token ?>">

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Enter new password"
                        required 
                        minlength="8"
                        autofocus
                    >
                    <small class="form-text">Min. 8 characters with uppercase, lowercase, and number</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-control" 
                        placeholder="Confirm new password"
                        required
                    >
                </div>

                <div class="password-strength" id="passwordStrength"></div>

                <button type="submit" class="btn btn-primary btn-block">Reset Password</button>

                <div class="auth-links">
                    <a href="<?= BASE_URL ?>/auth/login">← Back to Login</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password strength indicator
        const password = document.getElementById('password');
        const strengthDiv = document.getElementById('passwordStrength');

        password.addEventListener('input', function() {
            const val = this.value;
            let strength = 0;

            if (val.length >= 8) strength++;
            if (/[a-z]/.test(val)) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[^a-zA-Z0-9]/.test(val)) strength++;

            const strengths = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
            const colors = ['#f56565', '#ed8936', '#ecc94b', '#48bb78', '#38b2ac'];

            if (val.length > 0) {
                strengthDiv.textContent = 'Password Strength: ' + strengths[strength - 1];
                strengthDiv.style.color = colors[strength - 1];
            } else {
                strengthDiv.textContent = '';
            }
        });

        // Confirm password validation
        const form = document.getElementById('resetForm');
        const confirmPassword = document.getElementById('confirm_password');

        form.addEventListener('submit', function(e) {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match!');
                confirmPassword.focus();
            }
        });
    </script>
</body>
</html>
