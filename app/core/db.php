<?php
# расширение PHP под названием PDO (PHP Data Objects), которое предоставляет набор функций для работы с базами данных в безопасном и удобном виде.
require 'config.php'; #__DIR__ абсолютный путь к текущей директории файла, в котором она используется.
Class DB
{
    private $pdo;

    public function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // включить обработку ошибок в виде исключений
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // установить режим выборки по умолчанию в ассоциативный массив
            PDO::ATTR_EMULATE_PREPARES => false, // отключить эмуляцию подготовленных запросов для безопасности
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function connect()
    {
        return $this->pdo;
    }
}