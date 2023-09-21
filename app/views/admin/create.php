<!DOCTYPE html>
<html>
<?php require '../app/views/header.php'; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Create News</title>
    <link rel="stylesheet" href="/NewsWebSitePhp/public/css/create.css">
</head>
<body>
<main>
    <h2>Create News</h2>
    <form method="POST" action="/NewsWebSitePhp/public/store" enctype="multipart/form-data">
        <div>
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div>
            <label for="content">Content</label>
            <textarea id="content" name="content" cols="100" required></textarea>
        </div>

        <div>
            <label for="media">Upload Image or Video (optional)</label>
            <input type="file" id="media" name="media">
        </div>

        <div>
            <input type="submit" class="create-btn" value="Create News">
        </div>
    </form>
</main>
<?php require '../app/views/footer.php'; ?>
</body>
</html>
