<?php

namespace App\Models;

use Psr\Container\ContainerInterface;

class User
{
    private $container;
    public function __construct(ContainerInterface $container){
        $this->container = $container;
        $this->pdo = $this->container->get('pdo');
    }

    public function getUser($user){
        $sql = "SELECT * FROM comment WHERE user_id=:id";
        $smtm = $this->pdo->prepare($sql);
        $smtm->bindValue("id",$user,$this->pdo::PARAM_INT);
        $smtm->execute();
        $result = $smtm->fetchAll();
        return $result;
    }
}