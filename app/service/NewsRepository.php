<?php
// Путь до вашего файла может отличаться

require_once __DIR__ . '/../core/db.php';


class NewsRepository
{
    private $logger;
    private $db;

    public function __construct($logger)
    {
        $this->db = new DB();
        $this->pdo = $this->db->connect();

        $this->logger = $logger; // <-- Сохраняем объект логгера в свойстве класса

    }

    /**
     * Проверяет, существует ли новость с заданным source_url в базе данных.
     *
     * @param string $source_url URL-источник новости для проверки.
     * @return bool Возвращает true, если новость существует, иначе false.
     */
    public function isNewsExists($source_url)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM news WHERE source_url = ?');
        $stmt->execute([$source_url]);
        $newsCount = $stmt->fetchColumn();

        if ($newsCount > 0) {
            $this->logger->info("News with source URL: $source_url already exists in the database.");
            return true;
        }

        $this->logger->info("No duplicates found for source URL: $source_url.");
        return false;
    }

}
