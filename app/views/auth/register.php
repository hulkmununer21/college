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
        <div class="auth-box auth-box-wide">
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

            <form method="POST" action="<?= BASE_URL ?>/auth/process-register" class="auth-form" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input 
                            type="text" 
                            id="first_name" 
                            name="first_name" 
                            class="form-control" 
                            placeholder="Enter your first name"
                            required 
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input 
                            type="text" 
                            id="last_name" 
                            name="last_name" 
                            class="form-control" 
                            placeholder="Enter your last name"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-control" 
                        placeholder="Choose a username (min. 4 characters)"
                        required
                        minlength="4"
                        pattern="[a-zA-Z0-9_]+"
                        title="Username can only contain letters, numbers, and underscores"
                    >
                    <small class="form-text">Only letters, numbers, and underscores allowed</small>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="Enter your email address"
                        required
                    >
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Create a password"
                            required
                            minlength="8"
                        >
                        <small class="form-text">Min. 8 characters with uppercase, lowercase, and number</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            class="form-control" 
                            placeholder="Confirm your password"
                            required
                        >
                    </div>
                </div>

                <div class="password-strength" id="passwordStrength"></div>

                <button type="submit" class="btn btn-primary btn-block">Create Account</button>

                <div class="auth-links">
                    <a href="<?= BASE_URL ?>/auth/login">Already have an account? Login</a>
                </div>

                <div class="auth-footer">
                    <a href="<?= BASE_URL ?>">← Back to Home</a>
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
        const form = document.getElementById('registerForm');
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
