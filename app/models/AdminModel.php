<?php
// app/models/AdminModel.php
//require "../app/core/db.php";

class AdminModel
{
    private $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function createNews($title, $body, $media, $author)
    {
        $sql = "INSERT INTO news (title, body, media, author_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$title, $body, $media, $author]);
    }

    public function getAllNews()
    {
        // Запрос SQL выбирает все поля из таблицы 'news', а также поле 'username' из таблицы 'users' и называет его 'author' для удобства.
        // INNER JOIN используется для объединения таблиц 'news' и 'users' на основе совпадения 'author_id' в 'news' и 'id' в 'users'.
        // Таким образом, мы можем получить имя автора (из таблицы 'users') для каждой новости (из таблицы 'news').
        // Наконец, мы упорядочиваем результаты по дате публикации в порядке убывания.
        $sql = "SELECT news.id, news.title, news.body, news.media, news.published_at, users.username as author
            FROM news
            INNER JOIN users
            ON news.author_id = users.id
            ORDER BY news.published_at DESC";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getNewsById($id)
    {
        $sql = "SELECT news.id, news.title, news.body, news.media, news.published_at, users.username as author
            FROM news
            INNER JOIN users
            ON news.author_id = users.id
            WHERE news.id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function updateNews($id, $title, $body, $media)
    {
        $sql = "UPDATE news SET title = ?, body = ?, media = ? WHERE id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$title, $body, $media, $id]);
    }

    public function deleteNews($id)
    {
        $sql = "DELETE FROM news WHERE id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$id]);
    }

    public function getCommentsByNewsId($newsId)
    {
        $sql = "SELECT * FROM comments WHERE news_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$newsId]);

        return $stmt->fetchAll();
    }

    public function deleteComment($id)
    {
        $sql = "DELETE FROM comments WHERE id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$id]);
    }



}
