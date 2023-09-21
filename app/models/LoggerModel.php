<?php
// Определяем класс модели Logger, который будет содержать методы для работы с логами
class LoggerModel {
    // Приватная переменная, содержащая путь к директории, где хранятся файлы логов
    private $logDir = __DIR__ . '/../NewsWebSitePhp/';

    // Метод для получения списка всех файлов логов в директории
    public function getLogFiles() {
        // Используем функцию scandir для получения всех файлов в директории с логами
        $files = scandir($this->logDir);

        // Используем array_filter и анонимную функцию для фильтрации файлов,
        // оставляя только те, которые имеют расширение .log
        $logFiles = array_filter($files, function($file) {
            return preg_match('/\.log$/', $file);
        });

        // Возвращаем отфильтрованный список файлов
        return $logFiles;
    }

    // Метод для получения содержимого конкретного файла лога
    public function getLogFileContent($filename) {
        // Формируем полный путь к файлу, используя путь к директории и имя файла
        $filePath = $this->logDir . $filename;

        // Проверяем, существует ли файл по указанному пути
        if (!file_exists($filePath)) {
            // Если файл не найден, выбрасываем исключение с сообщением об ошибке
            throw new Exception("File not found");
        }

        // Если файл существует, читаем его содержимое и возвращаем его
        return file_get_contents($filePath);
    }
}
