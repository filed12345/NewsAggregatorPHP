<?php
//namespace app\controllers;

require "../app/models/UserModel.php";

class UserController
{
    public function register()
    {
        $email_error = null;
        $username_error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new UserModel();
            $existingUserByEmail = $user->findUserByEmail($_POST['email']);
            $existingUserByUsername = $user->findUserByUsername($_POST['username']);

            // Проверяем, существует ли пользователь с таким email
            if ($existingUserByEmail) {
                $email_error = 'This email is already registered.';
            }

            // Проверяем, существует ли пользователь с таким username
            if ($existingUserByUsername) {
                $username_error = 'This username is already in use.';
            }

            // Если ошибок нет, создаем нового пользователя
            if (!$email_error && !$username_error) {
                $user->createUser($_POST['username'], $_POST['email'], $_POST['password']);
                header('Location: /NewsWebSitePhp/public/login');  // Перенаправляем на страницу логина
                exit;
            }
        }

        require_once '../app/views/register.php';
    }

    public function login()
    {
        $error = null; // Инициализируем переменную ошибки
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new UserModel();
            $existingUser = $user->findUserByEmail($_POST['email']);
            //password_verify - проверяемс оответствует ли указанный пароль его хэшу
            if ($existingUser && password_verify($_POST['password'], $existingUser['password'])) {
                // Запоминаем пользователя в сессии
                $_SESSION['user'] = $existingUser;

//                // проверяем, является ли пользователь администратором
//                if ($existingUser['user_type'] === 'admin') {
//                    header('Location: /NewsWebSitePhp/public/admin'); // перенаправляем администратора на его страницу
//                } else {
//                    header('Location: /NewsWebSitePhp/public/news'); // перенаправляем обычного пользователя на главную страницу
//                }
                header('Location: /NewsWebSitePhp/public/news'); // перенаправляем пользователя на главную страницу
                exit;
            } else {
                $error = 'Неверный адрес электронной почты или пароль'; // Записываем сообщение об ошибке
            }
        }

        require_once '../app/views/login.php';
    }


    public function logout()
    {
        // Удаляем все данные сессии
//        $_SESSION = array();

//        // Если установлены cookies для сессии, удаляем и их навсякий случай, хотя я не уставнавливал куки
//        if (ini_get("session.use_cookies")) {
//            $params = session_get_cookie_params();
//            setcookie(session_name(), '', time() - 42000,
//                $params["path"], $params["domain"],
//                $params["secure"], $params["httponly"]
//            );
//        }

        // Уничтожаем сессию
        session_unset();
        session_destroy();

        // Редирект на главную страницу
        header('Location: /NewsWebSitePhp/public');
        exit;
    }

}
