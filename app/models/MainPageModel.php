<?php

class MainPageModel
{
    private $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function getAllNews()
    {
        $sql = "SELECT news.*, users.username as author_name 
        FROM news 
        LEFT JOIN users ON news.author_id = users.id 
        ORDER BY news.published_at DESC";

        $stmt = $this->db->connect()->query($sql);
        return $stmt->fetchAll();
    }


    public function getCommentsForNews($newsId)
    {
        $sql = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE news_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$newsId]);
        return $stmt->fetchAll();
    }

    public function addComment($userId, $newsId, $comment)
    {
        $sql = "INSERT INTO comments (user_id, news_id, comment) VALUES (?, ?, ?)";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$userId, $newsId, $comment]);
    }
}
