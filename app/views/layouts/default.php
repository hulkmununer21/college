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
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    
    <!-- Additional CSS -->
    <?php if (isset($css)): ?>
        <?php foreach ($css as $cssFile): ?>
            <link rel="stylesheet" href="<?= BASE_URL ?>/css/<?= $cssFile ?>.css">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <nav class="navbar">
            <div class="container">
                <a href="<?= BASE_URL ?>" class="logo">
                    <h1><?= APP_NAME ?></h1>
                </a>
                <ul class="nav-menu">
                    <li><a href="<?= BASE_URL ?>">Home</a></li>
                    <li><a href="<?= BASE_URL ?>/home/about">About</a></li>
                    <li><a href="<?= BASE_URL ?>/home/contact">Contact</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
                        <li><a href="<?= BASE_URL ?>/auth/logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL ?>/auth/login">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="flash-messages">
            <?php foreach ($_SESSION['flash'] as $flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
            <p>Version <?= APP_VERSION ?></p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="<?= BASE_URL ?>/js/main.js"></script>
    
    <!-- Additional JavaScript -->
    <?php if (isset($js)): ?>
        <?php foreach ($js as $jsFile): ?>
            <script src="<?= BASE_URL ?>/js/<?= $jsFile ?>.js"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
