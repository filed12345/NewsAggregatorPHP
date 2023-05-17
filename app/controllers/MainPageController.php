<?php
require "../app/models/MainPageModel.php";

class MainPageController
{

    public function index()
    {
        $newsModel = new MainPageModel();
        $news = $newsModel->getAllNews();
        //использование ссылки & в цикле foreach. Это позволяет нам изменять элементы массива $news прямо в цикле.
        foreach ($news as &$newsItem) {
            $newsItem['comments'] = $newsModel->getCommentsForNews($newsItem['id']);
        }
        unset($newsItem);
        require_once '../app/views/mainpage.php';
    }

    public function addComment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newsModel = new MainPageModel();
            $newsModel->addComment($_SESSION['user']['id'], $_POST['news_id'], $_POST['comment']);
            header('Location: /NewsWebSitePhp/public/news');  // Возвращаем пользователя на страницу новостей
            exit;
        }
    }

}
