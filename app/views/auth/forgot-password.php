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
                <p>Enter your email address and we'll send you instructions to reset your password.</p>
            </div>

            <form method="POST" action="<?= BASE_URL ?>/auth/process-forgot-password" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="Enter your email address"
                        required 
                        autofocus
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>

                <div class="auth-links">
                    <a href="<?= BASE_URL ?>/auth/login">← Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
