<?php
// run_parser.php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../BuildNews.php';

$newsBuilder=new BuildNews();
$newsBuilder->execute();

