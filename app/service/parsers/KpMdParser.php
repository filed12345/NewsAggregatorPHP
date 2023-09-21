<?php
// service/parsers/AvaMdParser.php
require_once 'BaseParser.php';
require_once __DIR__ . '/../ImageUploader.php';

class KpMdParser extends BaseParser
{
    /**
     * Конструктор класса.
     *
     * @param string $url URL-адрес для парсинга.
     */
    protected $baseUrl = "https://www.kp.md";
    private $imageUploader;

    public function __construct($url)
    {
        parent::__construct($url,"kpmd.log");
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
            $newsUrlNode = $crawler->filterXPath("(//a[contains(@class, 'sc-1tputnk-2')])[1]");
            if ($newsUrlNode->count() === 0) {
                throw new \Exception("News URL not found.");
            }
            $newsUrl = $newsUrlNode->link()->getUri();
            $this->logger->info("News URL found: " . $newsUrl);

            // Шаг 3: Перейдите по URL новости и парсим заголовок
            $crawler = $this->client->request('GET', $newsUrl);

            // Шаг 2: Получите URL логотипа
            $logoUrlNode = $crawler->filterXPath("//img[contains(@class, 'sc-foxktb-1') and contains(@class, 'cYprnQ')]");
            $relativeImagePath = null;
            if ($logoUrlNode->count() > 0) {
                $logoUrl = $logoUrlNode->attr("src");

                // Преобразовываем относительный URL в абсолютный
                if (substr($logoUrl, 0, 2) === "//") {
                    $logoUrl = "https:" . $logoUrl;
                }

                $imagePath = $this->imageUploader->downloadImage($logoUrl);
                $absolutePath = realpath($imagePath);
                $relativeImagePath = basename($absolutePath);
                $this->logger->info("Image URL found and downloaded: " . $relativeImagePath);
            } else {
                $this->logger->info("Image not found, setting to NULL.");
            }



            // Шаг 4: Парсите заголовок и тело новости на новой странице
            $newsTitleNode = $crawler->filterXPath("//h1[contains(@class,'sc-j7em19-3')]");
            if ($newsTitleNode->count() === 0) {
                throw new \Exception("News title not found.");
            }
            $newsTitle = $newsTitleNode->text();
            $this->logger->info("News title found: " . $newsTitle);

            $bodyNode = $crawler->filterXPath("(//div[contains(@class,'sc-1wayp1z-0')]/p)[1]");
            if ($bodyNode->count() === 0) {
                throw new \Exception("News body not found.");
            }
            $body = $bodyNode->text();

            // Шаг 5: Парсим время публикации статьи
            $newsPublishedTimeNode = $crawler->filterXPath("(//span[contains(@class,'sc-j7em19-1')])[1]");
            if ($newsPublishedTimeNode->count() === 0) {
                throw new \Exception("News published time not found.");
            }
            $newsPublishedTime = $newsPublishedTimeNode->text();

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
