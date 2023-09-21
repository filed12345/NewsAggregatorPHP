<?php
require_once __DIR__ . '/../core/db.php';



class NewsWriter
{
    private $db;
    private $pdo;
    private $logger;

    public function __construct($logger)
    {
        $this->db = new DB();
        $this->pdo = $this->db->connect();

        $this->logger = $logger; // <-- Сохраняем объект логгера в свойстве класса
    }

    private function convertDateToMySqlFormat($dateStr)
    {
        // Проверяем, соответствует ли дата уже формату MySQL DATETIME
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $dateStr)) {
            return $dateStr;
        }

        // Удаляем название дня недели и запятую из строки даты, если они там есть
        $dateStr = preg_replace('/^(Понедельник|Вторник|Среда|Четверг|Пятница|Суббота|Воскресенье), /', '', $dateStr);
        $dateStr = str_replace(" г.", "", $dateStr);

        // Заменяем "час" и последующую запятую на двоеточие для обработки времени
        $dateStr = preg_replace('/, час /', ':', $dateStr);

        $months = [
            "января" => "01",
            "февраля" => "02",
            "марта" => "03",
            "апреля" => "04",
            "мая" => "05",
            "июня" => "06",
            "июля" => "07",
            "августа" => "08",
            "сентября" => "09",
            "октября" => "10",
            "ноября" => "11",
            "декабря" => "12",
        ];

        foreach ($months as $ru => $num) {
            $dateStr = str_ireplace($ru, $num, $dateStr);
        }

        if (strpos($dateStr, '|') !== false) {
            list($time, $date) = explode('|', $dateStr);
            $dateStr = trim($date) . ' ' . trim($time);
        }

        // Replace comma between date and time with a space
        $dateStr = str_replace(',', ' ', $dateStr);

        // Define possible date formats
        $formats = [
            'd.m.Y H:i',
            'd.m.Y H:i:s',
            'd m Y H:i',
            'd m Y H:i:s',
            'd.m.Y H i',
            'd m Y H i s',
            'd m Y H i',
            'd F Y H:i',
            'H:i d.m.Y', // Добавили новый формат для даты с временем перед датой
            'd F Y H:i', // Добавили новый формат с полным названием месяца
        ];

        // Try to create a DateTime object using each format
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateStr);
            if ($date !== false) {
                return $date->format('Y-m-d H:i:s');
            }
        }

        throw new Exception('Invalid date format: ' . $dateStr);
    }

    public function writeToDB($newsData)
    {
        // Format the date to MySQL DATETIME format
        try {
            $newsData['published_at'] = $this->convertDateToMySqlFormat($newsData['published_at']);
        } catch (Exception $e) {
            $this->logger->error("Failed to convert date format: " . $e->getMessage());
            return;
        }

        // Set the author_id to 1
        $newsData['author_id'] = 1;

        // Prepare SQL statement
        $sql = "INSERT INTO news (title, body, media, published_at, author_id, source_url, is_parsed) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(1, $newsData['title']);
        $stmt->bindParam(2, $newsData['body']);
        $stmt->bindParam(3, $newsData['media']);
        $stmt->bindParam(4, $newsData['published_at']);
        $stmt->bindParam(5, $newsData['author_id']);
        $stmt->bindParam(6, $newsData['source_url']);
        $stmt->bindParam(7, $newsData['is_parsed']);

//        if ($stmt->execute()) {
//            echo "News inserted successfully from {$newsData['source_url']} \n";
//        } else {
//            echo "Failed to insert news {$newsData['source_url']} \n";
//        }

        if ($stmt->execute()) {
            $this->logger->info("News inserted successfully from {$newsData['source_url']}");;
        } else {
            $this->logger->info("Failed to insert news {$newsData['source_url']}");;
        }
    }
}
