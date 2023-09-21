<?php
//по заданынмм url дергать указанный контроллер и его метод
return [
    'NewsWebSitePhp/public' => 'UserController@login',
    'NewsWebSitePhp/public/register' => 'UserController@register',
    'NewsWebSitePhp/public/login' => 'UserController@login',
    'NewsWebSitePhp/public/admin' => 'AdminController@index',
    'NewsWebSitePhp/public/edit/{id}' => 'AdminController@edit',
    'NewsWebSitePhp/public/update/{id}' => 'AdminController@update',
    'NewsWebSitePhp/public/delete/{id}' => 'AdminController@delete',
    'NewsWebSitePhp/public/create' => 'AdminController@create',
    'NewsWebSitePhp/public/store' => 'AdminController@store',
    'NewsWebSitePhp/public/logout' => 'UserController@logout',
    'NewsWebSitePhp/public/news' => 'MainPageController@index',
    'NewsWebSitePhp/public/mainpage/addComment' => 'MainPageController@addComment',
    'NewsWebSitePhp/public/deleteComment/{newsId}/{commentId}' => 'AdminController@deleteComment',
    'NewsWebSitePhp/public/admin/logs' => 'LoggerController@showLogs',
// Другие маршруты...
];