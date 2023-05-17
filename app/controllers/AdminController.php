<?php
// app/controllers/AdminController.php

require "../app/models/AdminModel.php";

class AdminController

{
    public function __construct()
    {
        // Проверяем, залогинен ли пользователь и является ли он админом
        if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
            header('Location: /login');
            exit;
        }
    }

    public function index()
    {
        $adminModel = new AdminModel();
        $news = $adminModel->getAllNews();
        // Добавляем комментарии к каждой новости
        foreach ($news as &$new) {
            $new['comments'] = $adminModel->getCommentsByNewsId($new['id']);
        }
        unset($new); // Это нужно, чтобы предотвратить последующие проблемы с ссылками


        require_once '../app/views/admin/admin.php';
    }

    public function create()
    {
        require_once '../app/views/admin/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminModel = new AdminModel();

            // Санитизация и валидация названия новости
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING); // Получить заголовок из POST-запроса и отфильтровать его как строку
            //FILTER_SANITIZE_STRING - фильтр, который удаляет или экранирует недопустимые символы в строке.
            //filter_input() - функция для получения и фильтрации данных из внешнего источника (в данном случае из POST-запроса).
            if (!$title || strlen($title) > 200) {
                // Если заголовок пустой или его длина превышает 200 символов
                // Вывести ошибку и прервать выполнение скрипта
                die('Invalid title');
            }

            // Санитизация и валидация содержимого новости
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
            if (!$content) {
                // Вывести ошибку и прервать выполнение скрипта
                die('Invalid content');
            }

            $media = null;
            // Проверяем, был ли передан файл 'media' через форму и нет ли ошибок при его загрузке
            if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                // Проверить, что загружаемый файл - это изображение
                //расширение fileinfo используется для определения типа файла на основе его содержимого.
                $fileInfo = finfo_open(FILEINFO_MIME_TYPE); // Создаем объект для определения типа файла на основе его содержимого
                // Определить тип загруженного файла на основе его содержимого
                $detectedType = finfo_file($fileInfo, $_FILES['media']['tmp_name']);
                // Проверить, что определенный тип файла находится в списке допустимых типов (изображений и видео и гиф).
                // Если не находится, вывести сообщение об ошибке и прервать выполнение скрипта.
                $validImageTypes = ['image/gif', 'image/jpeg', 'image/png', 'video/mp4'];
                if (!in_array($detectedType, $validImageTypes)) {
                    die('Invalid file type');
                }
                //Получаем имя файла
                $media = $_FILES['media']['name'];
                //формируем путь для сохранения файла
                $uploadPath = '../public/uploads/' . basename($media);
                //загружаем файл в указанный путь
                move_uploaded_file($_FILES['media']['tmp_name'], $uploadPath);
            }

            // Сохраняем ID текущего пользователя как автора новости
            $author = $_SESSION['user']['id'];

            $adminModel->createNews($title, $content, $media, $author);

            // После сохранения новости перенаправляем пользователя обратно на страницу админа
            header('Location: /NewsWebSitePhp/public/admin');
            exit;
        }
    }

    public function delete($id)
    {
        $adminModel = new AdminModel();
        $adminModel->deleteNews($id);

        // После удаления новости перенаправляем пользователя обратно на страницу админа
        header('Location: /NewsWebSitePhp/public/admin');
        exit();
    }

    public function edit($id)
    {
        $adminModel = new AdminModel();
        $news = $adminModel->getNewsById($id);
        $news['comments'] = $adminModel->getCommentsByNewsId($id);

        require_once '../app/views/admin/edit.php';
    }

    public function update($id)
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminModel = new AdminModel();
            // Получаем и валидируем введенные данные, как мы это делали в методе store
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            if (!$title || strlen($title) > 200) {
                $errors['title'] = 'Invalid title';
            }

            $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_STRING);
            if (!$body) {
                $errors['$body'] = 'Invalid upload content';
            }
            // Здесь мы можем обработать загрузку нового файла, если он был предоставлен,
            // или сохранить старый файл, если он не был изменен.

            $media = null;
            // Проверяем, был ли передан файл 'media' через форму и нет ли ошибок при его загрузке
            if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                // Здесь мы повторяем ту же логику, что и в методе store, чтобы обработать загрузку файла.
                $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                $detectedType = finfo_file($fileInfo, $_FILES['media']['tmp_name']);
                $validImageTypes = ['image/gif', 'image/jpeg', 'image/png', 'video/mp4'];
                if (!in_array($detectedType, $validImageTypes)) {
                    $errors['media'] = 'Invalid file type';
                }

                $media = $_FILES['media']['name'];
                $uploadPath = '../public/uploads/' . basename($media);
                move_uploaded_file($_FILES['media']['tmp_name'], $uploadPath);
            } else {
                // Если новый файл не был предоставлен, мы сохраняем старый файл.
                $news = $adminModel->getNewsById($id);
                $media = $news['media'];
            }

            $adminModel->updateNews($id, $title, $body, $media);

            // После обновления новости перенаправляем пользователя обратно на страницу админа
            header('Location: /NewsWebSitePhp/public/admin');
            exit;
        }
    }
    public function deleteComment($newsId, $commentId)
    {
        $adminModel = new AdminModel();
        $adminModel->deleteComment($commentId);

        // После удаления комментария перенаправляем пользователя обратно на страницу редактирования новости
        header('Location: /NewsWebSitePhp/public/edit/' . $newsId);
        exit();
    }
}
