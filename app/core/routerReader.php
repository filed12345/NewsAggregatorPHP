<?php
//маршрутизатор
// core/routerReader.php

class routerReader {
    //дефолтное значение при откртии localhost

    public function load($file) {
        $routes = require $file;
        $this->define($routes);
    }

    public function define($routes) {
        $this->routes = $routes;
    }

    #Данный метод direct($uri) выполняет непосредственное направление (routing) запроса
    #на соответствующее действие (action) в контроллере,
    # основываясь на переданном URI.
    public function direct($uri) {
        // Перебираем все маршруты из массива routes
        foreach ($this->routes as $route => $controllerAction) {
            // Если текущий маршрут соответствует URI (проверка с помощью регулярного выражения)
            if (preg_match($this->convertRouteToRegex($route), $uri, $matches)) {
                try {
                    // Разделяем строку контроллера и действия на две отдельные переменные
                    $controllerAction = explode('@', $controllerAction);
                    $controller = $controllerAction[0];
                    $action = $controllerAction[1];
                    // Получаем все совпадения из регулярного выражения (кроме полного совпадения)
                    $params = array_slice($matches, 1);
                    // Вызываем соответствующее действие контроллера с параметрами
                    return $this->callAction($controller, $action, $params);
                } catch (Exception $e) {
                    // Обработка возможных исключений
                }
            }
        }
//        // Если ни один маршрут не соответствует данному URI, генерируем исключение
//        throw new Exception('No route defined for this URI.');
        // Если ни один маршрут не соответствует данному URI, отображаем страницу 404
        require_once '../app/views/404.php';
    }

    protected function convertRouteToRegex($route) {
        // Заменяем слэши и параметры маршрута на соответствующие элементы регулярного выражения
        return '@^' . preg_replace(['@/@', '@\{\w+\}@'], ['\/', '(\d+)'], $route) . '$@';
    }


    protected function callAction($controller, $action, $params = []) {
        // Создаем новый экземпляр контроллера
        $controller = new $controller;
        // Проверяем, существует ли метод (действие) в контроллере
        if (! method_exists($controller, $action)) {
            // Если метод не существует, генерируем исключение
            throw new Exception(
                "{$controller} does not respond to the {$action} action."
            );
        }
        // Вызываем действие контроллера с параметрами
        return $controller->$action(...$params);
    }

}

