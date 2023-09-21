<?php
// service/parsers/AvaMdParser.php
require_once 'BaseParser.php';
require_once __DIR__ . '/../ImageUploader.php';

class JurnalMdParser extends BaseParser
{
    /**
     * Конструктор класса.
     *
     * @param string $url URL-адрес для парсинга.
     */
    protected $baseUrl = "https://www.jurnal.md";
    private $imageUploader;

    public function __construct($url)
    {
        parent::__construct($url,"jurnalmd.log");
        $this->imageUploader = new ImageUploader(); // Создание нового объекта ImageUploader
    }

    /**
     * Основной метод для парсинга веб-страницы.
     *
     * @param \Symfony\Component\DomCrawler\Crawler $crawler Объект для парсинга веб-страницы.
     */
    protected function parse(\Symfony\Component\DomCrawler\Crawler $crawler)
    {
        try {
            // Шаг 1: Получите URL новости
            $newsUrlNode = $crawler->filterXPath("(//a[@class='animsition-link'])[3]");
            if ($newsUrlNode->count() === 0) {
                throw new \Exception("News URL not found.");
            }
            $newsUrl = $newsUrlNode->link()->getUri();
            $this->logger->info("News URL found: " . $newsUrl);

            // Шаг 4: Парсите заголовок и тело новости на новой странице
            $newsTitleNode = $crawler->filterXPath("(//a[@class='animsition-link'])[3]");
            if ($newsTitleNode->count() === 0) {
                throw new \Exception("News title not found.");
            }
            $newsTitle = $newsTitleNode->text();
            $this->logger->info("News title found: " . $newsTitle);

            // Шаг 3: Перейдите по URL новости и парсим заголовок
            $crawler = $this->client->request('GET', $newsUrl);

            // Шаг 2: Получите URL логотипа
            $logoUrlNode = $crawler->filterXPath("//meta[@property='og:image']/@content");
            $relativeImagePath = null;
            if ($logoUrlNode->count() > 0) {
                $logoUrl = $logoUrlNode->text();
                $imageUrl = $logoUrl;
                $imagePath = $this->imageUploader->downloadImage($imageUrl);
                $absolutePath = realpath($imagePath);
                $relativeImagePath = basename($absolutePath);
                $this->logger->info("Image URL found and downloaded: " . $relativeImagePath);
            } else {
                $this->logger->info("Image not found, setting to NULL.");
            }


            $bodyNode = $crawler->filterXPath("//div[contains(@class,'article-content')]");

            if ($bodyNode->count() === 0) {
                throw new \Exception("News body not found.");
            }

            $bodyHTML = $bodyNode->html();

            $bodyNode = $crawler->filterXPath("//div[contains(@class,'article-content')]");

            if ($bodyNode->count() === 0) {
                throw new \Exception("News body not found.");
            }

            $bodyHTML = $bodyNode->html();

            $bodyNode = $crawler->filterXPath("//div[contains(@class,'article-content')]");

            if ($bodyNode->count() === 0) {
                throw new \Exception("News body not found.");
            }

            $bodyNode2 = $bodyNode->filter('p')->each(function ($node) {
                // Находим и удаляем все вложенные script теги
                foreach ($node->filter('script') as $script) {
                    $script->parentNode->removeChild($script);
                }
                return $node->text();
            });

            $body = nl2br(implode("\n\n", $bodyNode2));


            // Шаг 5: Назначаем текущее время сервера + 1 час как время публикации
            $date = new DateTime();  // Создание объекта DateTime для текущего времени
            $date->modify('+1 hour');  // Добавление 1 часа к текущему времени
            $newsPublishedTime = $date->format('Y-m-d H:i:s');  // Форматирование обновленного времени в формат MySQL DATETIME

            return [
                'source_url' => $newsUrl,
                'media' => $relativeImagePath,
                'title' => $newsTitle,
                'body' => $body,
                'is_parsed' => true,
                'published_at' => $newsPublishedTime,
            ];
        } catch (\Exception $e) {
            // Логгирование ошибки с сообщением об ошибке
            $this->logger->error("Parsing failed: " . $e->getMessage());
            return null;
        }
    }
}
