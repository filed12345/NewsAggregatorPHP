<!DOCTYPE html>
<html>
<head>
    <?php require '../app/views/header.php'; ?>
    <title>Admin - News Dashboard</title>
    <link rel="stylesheet" href="/NewsWebSitePhp/public/css/admin.css">
</head>
<body>
<div class="page-container">
    <div class="content-wrap">
        <div class="container">
            <h2 class="title">News Dashboard</h2>
            <a class="create-btn" href="/NewsWebSitePhp/public/create">Create new news</a>
            <table class="news-table">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Published At</th>
                    <th>Published By</th>
                    <th>Body</th>
                    <th>Media</th>
                    <th>Comments</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($news as $new) : ?>
                    <tr>
                        <td><?= $new['title'] ?></td>
                        <td><?= $new['published_at'] ?></td>
                        <td><?= $new['author'] ?></td>
                        <td><?= $new['body'] ?></td>
                        <td><img src="/NewsWebSitePhp/public/uploads/<?= $new['media'] ?>" alt="<?= $new['title'] ?>"
                                 style="width:100px; height:auto;"></td>
                        <td>
                            <!-- Here we iterate over each comment and display it -->
                            <?php foreach ($new['comments'] as $comment) : ?>
                                <p><?= $comment['comment'] ?></p>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <a class="edit-btn" href="/NewsWebSitePhp/public/edit/<?= $new['id'] ?>">Edit</a> |
                            <!--передаем ид в наш скрипт по кнопке-->
                            <button class="delete-btn" onclick="confirmDelete('<?= $new['id'] ?>')">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Основной контейнер модального окна, который также служит затемненным фоном.
     Стиль "display: none" означает, что по умолчанию окно скрыто. -->
            <div id="deleteModal" class="modal-mask" style="display: none;">
                <!-- Обертка содержимого модального окна, помогает в стилизации и позиционировании. -->
                <div class="modal-wrapper">
                    <!-- Само содержимое модального окна. -->
                    <div class="modal-content">
                        <!-- Заголовок модального окна. -->
                        <h2>Are you sure you want to delete this news?</h2>
                        <!-- Форма подтверждения удаления новости. Метод POST используется для отправки данных на сервер. -->
                        <form id="deleteForm" method="POST">
                            <!-- Кнопка подтверждения удаления. При нажатии отправляет форму. -->
                            <button type="submit">Confirm Delete</button>
                            <!-- Кнопка отмены. Тип "button" предотвращает отправку формы.
                                 Функция "closeModal()" вызывается при нажатии кнопки и закрывает модальное окно. -->
                            <button type="button" onclick="closeModal()">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require '../app/views/footer.php'; ?>
</body>
<script>
    // Получаем элемент модального окна по его ID.
    let deleteModal = document.getElementById('deleteModal');
    // Получаем форму из модального окна по её ID.
    let deleteForm = document.getElementById('deleteForm');

    // Функция confirmDelete вызывается при нажатии на кнопку "Delete".
    // Принимает id удаляемой новости в качестве параметра.
    function confirmDelete(id) {
        // Устанавливаем действие формы на URL для удаления новости.
        // URL формируется динамически, исходя из id новости.
        deleteForm.action = "/NewsWebSitePhp/public/delete/" + id;
        // Показываем модальное окно, устанавливая свойство display в "flex".
        deleteModal.style.display = "flex";
    }

    // Функция closeModal вызывается при нажатии на кнопку "Cancel" в модальном окне.
    function closeModal() {
        // Скрываем модальное окно, устанавливая свойство display в "none".
        deleteModal.style.display = "none";
    }
</script>
</html>