<?php
// service/parsers/AvaMdParser.php
require_once 'BaseParser.php';
require_once __DIR__ . '/../ImageUploader.php';

class DiezMdParser extends BaseParser
{
    /**
     * Конструктор класса.
     *
     * @param string $url URL-адрес для парсинга.
     */
    private $imageUploader;

    public function __construct($url)
    {
        parent::__construct($url,"deizmd.log");
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
            $newsUrlNode = $crawler->filterXPath("//div[@class='overflow-scroll']//a[2]");
            if ($newsUrlNode->count() === 0) {
                throw new \Exception("News URL not found.");
            }
            $newsUrl = $newsUrlNode->link()->getUri();
            $this->logger->info("News URL found: " . $newsUrl);

            // Шаг 3: Перейдите по URL новости и парсим заголовок
            $crawler = $this->client->request('GET', $newsUrl);

            // Шаг 2: Получите URL логотипа
            $logoUrlNode = $crawler->filterXPath("//img[@class='w-auto mx-auto']");
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
            $newsTitleNode = $crawler->filterXPath("//a[@rel='bookmark']");
            if ($newsTitleNode->count() === 0) {
                throw new \Exception("News title not found.");
            }
            $newsTitle = $newsTitleNode->text();
            $this->logger->info("News title found: " . $newsTitle);

            $bodyNode1 = $crawler->filterXPath("//div[@class='entry-content']/div/p");
            if ($bodyNode1->count() === 0) {
                throw new \Exception("News body not found.");
            }

            $bodyNode = $crawler->filterXPath("//div[@class='entry-content']/p");
            if ($bodyNode->count() === 0) {
                throw new \Exception("News body not found.");
            }
            $bodyNode2 = $bodyNode->each(function ($node) {
                return $node->text();
            });


            $body = nl2br($bodyNode1->text()."\n\n".implode("\n\n", $bodyNode2));


            // Шаг 5: Парсим время публикации статьи
            $newsPublishedTimeNode = $crawler->filterXPath("//time[@itemprop='datePublished']");
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
