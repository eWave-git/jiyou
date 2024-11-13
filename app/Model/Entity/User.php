<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class User {

    public $id;

    public $nome;

    public $email;

    public $senha;

    public static function getUserByEmail($email) {
        return (new Database('usuarios'))->select('email = "'.$email.'"')->fetchObject(self::class);
    }
}