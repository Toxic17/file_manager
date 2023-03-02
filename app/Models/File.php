<?php

namespace App\Models;
use Psr\Container\ContainerInterface;

class File
{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->pdo = $container->get("pdo");
    }

    public function load($name_file, $fake_name_file,$size,$old_type,$new_type){
        $sql = "INSERT INTO file(name, fake_name, size, old_type, new_type,date) VALUES(:name,:fake_name,:size,:old_type,:new_type,:date)";
        $date = date("Y-m-d H:i:s");
        $this->pdo->prepare($sql)->execute(['name' => $name_file, 'fake_name' => $fake_name_file, 'size' => $size, 'old_type' => $old_type, "new_type" => $new_type, 'date' => $date]);
    }

    public function get_files($max_files,$page){
        $sql = "SELECT * FROM file ORDER BY date DESC LIMIT :from, :max_files";
        $from = ($max_files * $page) - $max_files;
        $all_files = $this->pdo->prepare($sql);
        $all_files->bindValue("max_files", $max_files, $this->pdo::PARAM_INT);
        $all_files->bindValue("from", $from, $this->pdo::PARAM_INT);
        $all_files->execute();
        $res = $all_files->fetchAll();
        return $res;
    }

    public function get_count_files(){
        $sql = "SELECT COUNT(*) as `count` FROM file";
        $count = $this->pdo->query($sql)->fetchAll()[0]['count'];
        return $count;
    }

    public function get_file($id){
        $sql = "SELECT * FROM file WHERE id=:id";
        $file = $this->pdo->prepare($sql);
        $file->bindValue("id",$id);
        $file->execute();
        $res = $file->fetchAll();
        return $res[0] ?? null;
    }

    public function sphinx_search($search_text){
        $sphinx_pdo = $this->container->get("sphinx");
        $search = $sphinx_pdo->prepare("Select * from index_file WHERE MATCH(:search_text)");
        $search->execute(['search_text'=>$search_text]);
        $result_index = $search->fetchAll();
        $result_search = [];
        foreach ($result_index as $index){
            $id = $index['id'];
            $sql = "SELECT * FROM file WHERE id=:id";
            $sptm = $this->pdo->prepare($sql);
            $sptm->bindValue("id",$id,$this->pdo::PARAM_INT);
            $sptm->execute();
            $result_search[] = $sptm->fetchAll()[0];
        }
        return $result_search;
    }
}