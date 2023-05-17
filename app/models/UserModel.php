<?php

//require "../app/core/db.php";

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function createUser($username, $email, $password)
    {
        //хэшируем пароль
        $password = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $this->db->connect()->prepare($sql); // обращаемся к методу connect в bd.php затем результат кидываем в метод prepare и возрвщаем объект PDOStatement
        $stmt->execute([$username, $email, $password]); // закидываем значения в бд
    }

    // ищем юзер по его емайлу
    public function findUserByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$email]); // закидываем значения в бд

        return $stmt->fetch();
    }
    // ищем юзера по его username
    public function findUserByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$username]); // закидываем значения в бд

        return $stmt->fetch();
    }
}

