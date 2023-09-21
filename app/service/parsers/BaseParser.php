<?php
// service/parsers/BaseParser.php

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

require_once __DIR__ . '/../NewsWriter.php';
require_once __DIR__ . '/../NewsRepository.php';
require_once __DIR__ . '/../LoggerService.php';

class BaseParser
{
    // Клиент для отправки HTTP-запросов
    protected $stream;
    protected $client;

    // Логгер для ведения журнала событий парсера
    protected $logger;

    // URL-адрес веб-страницы, которую надо спарсить
    protected $url;

    /**
     * Конструктор класса.
     *
     * @param string $url URL-адрес для парсинга.
     */
    public function __construct($url, $logFileName = 'parsers.log')
    {

        // Инициализируем клиент для веб-скрапинга
        $this->client = new HttpBrowser(HttpClient::create(['verify_peer' => false, 'verify_host' => false]));

        // Сохраняем URL, который будем парсить
        $this->url = $url;

        // Устанавливаем логгер для записи информации о работе парсера
        $this->logger = LoggerService::getLogger($logFileName);

        $this->newsWriter = new NewsWriter($this->logger);
        $this->newsRepository = new NewsRepository($this->logger); // Инициализируйте объект NewsRepository, передав PDO и объект logger
    }

    /**
     * Метод для запуска парсера.
     */
    public function run()
    {
        // Записываем в лог начало работы парсера
        $this->logger->info("Starting the parser for: " . $this->url);

        try {
            // Отправляем запрос на указанный URL и получаем объект Crawler для парсинга страницы
            $crawler = $this->client->request('GET', $this->url);

            // Логируем HTTP статус ответа
            $response = $this->client->getInternalResponse();
            $this->logger->info('HTTP Response Status Code', ['status_code' => $response->getStatusCode()]);

            // Вызываем метод parse, который должен быть реализован в дочернем классе
            $result = $this->parse($crawler);
            if (!$this->newsRepository->isNewsExists($result['source_url'])) {
                $this->newsWriter->writeToDB($result);
            } else {
                ImageUploader::deleteImageByName($result['media']);
            }
            // Логируем успешное завершение парсинга с результатами
            $this->logger->info('Parsing successful', ['result' => $result]);

            return $result;
        } catch (\Exception $e) {
            // Если возникла ошибка, записываем её в лог с полной трассировкой стека
            $this->logger->error("Failed to parse the URL " . $this->url . " due to: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            return null;
        }
    }

    /**
     * Основной метод для парсинга веб-страницы.
     * Должен быть реализован в дочерних классах.
     *
     * @param \Symfony\Component\DomCrawler\Crawler $crawler Объект для парсинга веб-страницы.
     * @throws \Exception Если метод не реализован.
     */
    protected function parse(\Symfony\Component\DomCrawler\Crawler $crawler)
    {
        throw new \Exception("Method parse is not implemented");
    }
}
