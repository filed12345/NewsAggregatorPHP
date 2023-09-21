<?php
// service/parsers/AvaMdParser.php
require_once 'BaseParser.php';
require_once __DIR__ . '/../ImageUploader.php';

class RuporMdParser extends BaseParser
{
    /**
     * Конструктор класса.
     *
     * @param string $url URL-адрес для парсинга.
     */
    protected $baseUrl = "https://rupor.md/";
    private $imageUploader;

    public function __construct($url)
    {
        parent::__construct($url,"rupormd.log");
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
            $newsUrlNode = $crawler->filterXPath("(//div[@class='item-content'])[1]/a");
            if ($newsUrlNode->count() === 0) {
                throw new \Exception("News URL not found.");
            }
            $newsUrl = $newsUrlNode->link()->getUri();
            $this->logger->info("News URL found: " . $newsUrl);

            // Шаг 3: Перейдите по URL новости и парсим заголовок
            $crawler = $this->client->request('GET', $newsUrl);

            // Шаг 2: Получите URL логотипа
            $logoUrlNode = $crawler->filterXPath("//div[@class='elementor-widget-container']/img");
            $relativeImagePath = null;
            if ($logoUrlNode->count() > 0) {
                $logoUrl = $logoUrlNode->attr("src");
                $imageUrl = $logoUrl;
                $imagePath = $this->imageUploader->downloadImage($imageUrl);
                $absolutePath = realpath($imagePath);
                $relativeImagePath = basename($absolutePath);
                $this->logger->info("Image URL found and downloaded: " . $relativeImagePath);
            } else {
                $this->logger->info("Image not found, setting to NULL.");
            }


            // Шаг 4: Парсите заголовок и тело новости на новой странице
            $newsTitleNode = $crawler->filterXPath("//h1[@class='elementor-heading-title elementor-size-default']");
            if ($newsTitleNode->count() === 0) {
                throw new \Exception("News title not found.");
            }
            $newsTitle = $newsTitleNode->text();
            $this->logger->info("News title found: " . $newsTitle);

            $bodyNode = $crawler->filterXPath("//div[contains(@class, 'elementor-element') and contains(@class, 'elementor-widget-theme-post-content')]//p");
            if ($bodyNode->count() === 0) {
                throw new \Exception("News body not found.");
            }

            $bodyNode->each(function ($node) {
                // Удаляем все элементы script и iframe внутри текущего узла p
                foreach ($node->filter('script, iframe') as $toRemove) {
                    $toRemove->parentNode->removeChild($toRemove);
                }
            });

            $bodyNode2 = $bodyNode->each(function ($node) {
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
