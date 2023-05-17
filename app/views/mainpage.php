<!DOCTYPE html>
<html>
<head>
    <?php require '../app/views/header.php'; ?>
    <title>Main Page</title>
    <link rel="stylesheet" type="text/css" href="/NewsWebSitePhp/public/css/mainpage.css">
</head>
<body>
<h1>Welcome to the News Page</h1>

<!-- Цикл по всем новостям -->
<?php foreach ($news as $newsItem): ?>
    <div class="news-item">
        <h2><?= $newsItem['title']; ?></h2>
        <!-- Отображение картинки -->
        <?php if (!empty($newsItem['media']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/NewsWebSitePhp/public/uploads/" . $newsItem['media'])): ?>
            <img class="news-image" src="/NewsWebSitePhp/public/uploads/<?= $newsItem['media'] ?>" alt="<?= "News Image" ?>">
        <?php endif; ?>
        <!-- Отображение имени автора -->
        <p>Author: <?= $newsItem['author_name']; ?></p>
        <p><?= $newsItem['body']; ?></p>
        <p>Published at: <?= $newsItem['published_at']; ?></p>

        <!-- Отображение комментариев -->
        <h3>Comments:</h3>
        <?php foreach ($newsItem['comments'] as $comment): ?>
            <div class="comment">
                <p><strong><?= $comment['username']; ?></strong> at <?= $comment['created_at']; ?></p>
                <span><?= $comment['comment']; ?></span>
            </div>
        <?php endforeach; ?>

        <!-- Форма для добавления комментариев -->
        <form action="/NewsWebSitePhp/public/mainpage/addComment" method="post">
            <input type="hidden" name="news_id" value="<?= $newsItem['id']; ?>">
            <textarea name="comment" required></textarea>
            <input type="submit" value="Add comment">
        </form>
    </div>
<?php endforeach; ?>
<?php require '../app/views/footer.php'; ?>
</body>
</html>
