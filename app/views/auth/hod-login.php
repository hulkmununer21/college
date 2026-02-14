<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Login - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1><?= APP_NAME ?></h1>
                <p class="role-badge hod-badge">Head of Department Portal</p>
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

            <form method="POST" action="<?= BASE_URL ?>/auth/process-hod-login" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="form-group">
                    <label for="staff_id">Staff ID or Email</label>
                    <input 
                        type="text" 
                        id="staff_id" 
                        name="username" 
                        class="form-control" 
                        placeholder="Enter your staff ID or email"
                        required 
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <div class="form-group-checkbox">
                    <label>
                        <input type="checkbox" name="remember_me" value="1">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login to HOD Portal</button>

                <div class="auth-links">
                    <a href="<?= BASE_URL ?>/auth/forgot-password">Forgot Password?</a>
                </div>

                <div class="auth-footer">
                    <a href="<?= BASE_URL ?>/auth/login">← Other Login Options</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
