<!-- header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- <meta charset="UTF-8"> -->
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <!-- <title>Your Website Title</title> -->
    <link rel="stylesheet" href="/NewsWebSitePhp/public/css/header.css">
</head>
<body>
<header class="header">
    <h1 class="header__title">
        <a href="/NewsWebSitePhp/public/news" class="header__home-link">News Website</a>
    </h1>
    <?php if (isset($_SESSION['user'])): ?>
        <div class="header__user-info">
            <?php if ($_SESSION['user']['user_type'] === 'admin'): ?>
                <a class="header__admin-panel" href="/NewsWebSitePhp/public/admin">Admin Panel</a>
                <!-- Добавляем ссылку на страницу логов для администратора -->
                <a class="header__logs-page" href="/NewsWebSitePhp/public/admin/logs">View Logs</a>
            <?php endif; ?>
            <span class="header__username">Welcome, <?= $_SESSION['user']['username'] ?></span>
            <a class="header__logout" href="/NewsWebSitePhp/public/logout">Log out</a>
        </div>
    <?php endif; ?>
</header>
