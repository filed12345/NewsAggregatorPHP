<?php
session_start();

require "../app/core/init.php";

$router = new routerReader(); // создаем наш ридер маршрутов
$router->load("../app/core/routers.php");// подгружаем наши маргруты
// эта строка кода берет URI текущего запроса, извлекает из него путь и удаляет начальный и конечный слэши.
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
//эта строка кода берет URI текущего запроса, извлекает из него путь и удаляет начальный и конечный слэши, а так же она удаляла поддиректорию из URI
//$uri = str_replace('NewsWebSitePhp/public/', '', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

//try {
//    echo $router->direct($uri);
//} catch (Exception $e) {
////    echo 'Caught exception: ',  $e->getMessage(), "\n";
//    require_once '../app/views/404.php';
//}
echo $router->direct($uri);


