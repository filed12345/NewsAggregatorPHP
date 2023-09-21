<?php
// Подключаем модель Logger, чтобы использовать её методы для работы с логами
require_once __DIR__ . '/../models/LoggerModel.php';

class LoggerController
{
    // Объявляем приватную переменную для хранения экземпляра модели Logger
    private $loggerModel;

    // Конструктор, который вызывается при создании объекта этого класса
    public function __construct()
    {
        // Создаем экземпляр модели Logger
        $this->loggerModel = new LoggerModel();

        // Проверяем, авторизован ли пользователь и имеет ли он права администратора
        if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
            // Если пользователь не администратор, перенаправляем его на страницу входа
            header('Location: /NewsWebSitePhp/public/login');
            exit;
        }
    }

    // Метод для отображения страницы с логами
    public function showLogs()
    {
        // Путь к директории с файлами логов
        $logDirPath = 'C:/Games/xampp/htdocs/NewsWebSitePhp/';

        // Инициализируем массив для хранения информации о файлах логов
        $logFiles = [];
        // Список имен файлов логов, которые нам нужно прочитать
        $logFileNames = [
            'deizmd.log', 'avamd.log', 'enewsmd.log', 'ipnmd.log',
            'jurnalmd.log', 'kpmd.log', 'lisamd.log', 'moldpressmd.log',
            'mybussinessmd.log', 'newsmakermd.log', 'noimd.log',
            'noktamd.log', 'riarumd.log', 'rupormd.log'
        ];
        foreach ($logFileNames as $fileName) {
            // Полный путь к файлу лога
            $filePath = $logDirPath . $fileName;

            // Проверяем, существует ли файл лога
            if (file_exists($filePath)) {
                // Читаем содержимое файла лога
                $logContent = file_get_contents($filePath);

                // Инициализируем массив для форматированного содержимого лога
                $formattedContent = [];

                // Разбиваем содержимое лога на строки и обрабатываем каждую строку
                foreach (explode("\n", trim($logContent)) as $line) {
                    // Разбиваем строку лога на части
                    if (preg_match('/^(.*?) > (.*?) > (.*)$/', $line, $matches)) {
                        // Извлекаем дату, уровень и сообщение из строки лога
                        $date = htmlspecialchars($matches[1]);
                        $level = htmlspecialchars($matches[2]);
                        $message = htmlspecialchars($matches[3]);

                        // Определяем CSS класс для уровня лога
                        $levelClass = "log-level-" . strtolower($level);

                        // Форматируем строку лога, добавляя HTML теги и CSS классы для подсветки
                        $formattedLine = "<span class='date'>{$date}</span> > <span class='{$levelClass}'>{$level}</span> > <span class='message'>{$message}</span>";
                    } else {
                        // Если строку не удалось разбить на части, просто экранируем специальные символы
                        $formattedLine = htmlspecialchars($line);
                    }

                    // Добавляем отформатированную строку в массив
                    $formattedContent[] = $formattedLine;
                }

                // Переворачиваем массив, чтобы последние записи в логе отображались первыми
                $formattedContent = array_reverse($formattedContent);

                // Добавляем информацию о файле лога в массив
                $logFiles[] = [
                    'name' => $fileName,
                    'content' => implode("<br>", $formattedContent)
                ];
            } else {
                // Если файл лога не найден, добавляем сообщение об ошибке в массив
                $logFiles[] = [
                    'name' => $fileName,
                    'content' => "File not found: $fileName"
                ];
            }
        }

        // Передаем данные в шаблон для отображения на веб-странице
        include __DIR__ . '/../views/admin/viewLog.php';
    }

    // Метод для отображения отдельного файла лога (в данный момент не используется)
    public function viewLog($filename)
    {
        try {
            // Получаем содержимое файла лога
            $logContent = $this->loggerModel->getLogFileContent($filename);

            // Подключаем шаблон для отображения содержимого файла лога
            require_once __DIR__ . '/../views/logger/viewLog.php';
        } catch (Exception $e) {
            // Обрабатываем возможные исключения, выводя сообщение об ошибке
            echo 'Error: ' . $e->getMessage();
        }
    }
}
