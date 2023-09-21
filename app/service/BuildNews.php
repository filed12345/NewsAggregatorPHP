<?php
require_once __DIR__ . '/parsers/BaseParser.php';
require_once __DIR__ . '/parsers/AvaMdParser.php';
require_once __DIR__ . '/parsers/DiezMdParser.php';
require_once __DIR__ . '/parsers/EnewsMdParser.php';
require_once __DIR__ . '/parsers/IpnMdParser.php';
require_once __DIR__ . '/parsers/KpMdParser.php';
require_once __DIR__ . '/parsers/MoldpressMdParser.php';
require_once __DIR__ . '/parsers/MyBussinessMdParser.php';
require_once __DIR__ . '/parsers/NewsmakerMdParser.php';
require_once __DIR__ . '/parsers/NoiMdParser.php';
require_once __DIR__ . '/parsers/NoktaMdParser.php';
require_once __DIR__ . '/parsers/JurnalMdParser.php';
require_once __DIR__ . '/parsers/LisaMdParser.php';
require_once __DIR__ . '/parsers/RiaRuMdParser.php';
require_once __DIR__ . '/parsers/RuporMdParser.php';
require_once __DIR__ . '/../core/db.php';


class BuildNews
{

    private $avaMdParser;
    private $diezMdParser;
    private $enewsMdParser;
    private $ipnMdParser;
    private $kpMdParser;
    private $moldpressMdParser;
    private $mybussinessMdParser;
    private $newsmakerMdParser;
    private $noiMdParser;
    private $noktaMdParser;
    private $jurnalMdParser;
    private $lisaMdParser;
    private $riaRuMdParser;
    private $ruporMdParser;


    public function __construct()
    {


        $this->avaMdParser = new AvaMdParser("https://ava.md/ru/");
        $this->diezMdParser = new DiezMdParser("https://ru.diez.md/");
        $this->enewsMdParser = new EnewsMdParser("https://enews.md/");
        $this->ipnMdParser = new IpnMdParser("https://www.ipn.md/ru/articles_by_date/");
        $this->kpMdParser = new KpMdParser("https://www.kp.md/online/");
        $this->moldpressMdParser = new MoldpressMdParser("https://www.moldpres.md/ru/news/");
        $this->mybussinessMdParser = new MyBussinessMdParser("https://mybusiness.md/ru/");
        $this->newsmakerMdParser = new NewsmakerMdParser("https://newsmaker.md/");
        $this->noiMdParser = new NoiMdParser("https://noi.md/ru");
        $this->noktaMdParser = new NoktaMdParser("https://nokta.md/novosti/");
        $this->jurnalMdParser = new JurnalMdParser("https://www.jurnal.md/ru/category/novosti");
        $this->lisaMdParser = new LisaMdParser("https://lisa.md/");
        $this->riaRuMdParser = new RiaRuMdParser("https://ria.ru/location_Moldova/");
        $this->ruporMdParser = new RuporMdParser("https://rupor.md/");


    }


    public function execute()
    {
        // Массив колбеков, каждый из которых запускает метод run соответствующего парсера
        $parsersListCallback = [
            function() {  $this->avaMdParser->run();  },
            function() {  $this->diezMdParser->run();  },
            function() {  $this->enewsMdParser->run();  },
            function() {  $this->ipnMdParser->run();  },
            function() {  $this->kpMdParser->run();  },
            function() {  $this->moldpressMdParser->run(); },
            function() {  $this->mybussinessMdParser->run();  },
            function() {  $this->newsmakerMdParser->run(); },
            function() {  $this->noiMdParser->run(); },
            function() {  $this->noktaMdParser->run(); },
            function() {  $this->jurnalMdParser->run(); },
            function() {  $this->lisaMdParser->run(); },
            function() {  $this->riaRuMdParser->run(); },
            function() {  $this->ruporMdParser->run();  },
        ];

        while (true) {
            // Использование array_map и анонимной функции для запуска каждого парсера в отдельном волокне (Fiber)
            array_map(function($parserCallback) {
                $fiber = new Fiber($parserCallback);
                $fiber->start();
            }, $parsersListCallback);

            sleep(10);
        }
    }
}