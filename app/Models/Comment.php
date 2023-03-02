<?php

namespace App\Models;

use Psr\Container\ContainerInterface;

class Comment
{
    private $container;
    public function __construct(ContainerInterface $container){
        $this->container = $container;
        $this->pdo = $this->container->get("pdo");
    }

    public function get_comment($file_id){
        $sql = "SELECT * FROM comment WHERE file_id=:file_id";
        $stmp = $this->pdo->prepare($sql);
        $stmp->bindValue("file_id",$file_id,$this->pdo::PARAM_INT);
        $stmp->execute();
        $comments = $stmp->fetchAll();
        return $comments;
    }

    public function get_count_comments(){
        $sql = "SELECT Count(*) as `count`,comment.file_id FROM comment INNER JOIN file ON comment.file_id = file.id group by comment.file_id";
        $stmp = $this->pdo->prepare($sql);
        $stmp->execute();
        $comments = $stmp->fetchAll();
        return $comments;
    }

    public function add($date,$value,$user_id,$file_id){
        $sql = "INSERT INTO comment(date,value,user_id,file_id) VALUES (:date,:value,:user_id,:file_id)";
        $result = $this->pdo->prepare($sql);
        $result->execute(['date'=>$date,"value"=>$value,"user_id"=>$user_id,"file_id"=>$file_id]);
    }
}