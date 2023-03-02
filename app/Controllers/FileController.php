<?php

namespace App\Controllers;
use App\Models\Comment;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Models\File;

class FileController
{

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->file_model = new File($this->container);
    }
    public function index(Request $request, Response $response)
    {
        return $this->container->get("view")->render($response, 'main.html');
    }

    public function load(Request $request, Response $response)
    {
        $max_file_size = $this->container->get("settings")['files']['max_size'];
        $file = $_FILES['file'];
            if (!empty($file)) {
                if (empty($_FILES['file']['error'])) {
                    if ($file['size'] <= $max_file_size) {
                        //change file
                        $new_type = ".txt";
                        $name_file = strrev(mb_substr(strstr(strrev($file['name']), "."), 1));
                        $old_type = "." . strrev(strstr(strrev($file['name']), ".", true));
                        $fake_name_file = base64_encode(random_bytes(10));
                        move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/../app/files/" . $name_file . $new_type);
                        rename($_SERVER['DOCUMENT_ROOT'] . "/../app/files/" . $name_file . $new_type, $_SERVER['DOCUMENT_ROOT'] . "/../app/files/" . $fake_name_file . $new_type);
                        //insert dates about file

                        $this->file_model->load($name_file, $fake_name_file, $file['size'], $old_type, $new_type);
                        //set flash message
                        $this->container->get('flash')->addMessage('file_load', true);
                        return $response->withStatus(302)->withHeader('Location', "/");

                    } else {
                        $this->container->get('flash')->addMessage('error', "File must be less than 200 mb");
                        return $response->withStatus(302)->withHeader('Location', "/");
                    }
                } else {
                    $this->container->get('flash')->addMessage('error', $_FILES['file']['error']);
                    return $response->withStatus(302)->withHeader('Location', "/");
                }
            } else {
                $this->container->get('flash')->addMessage('error', "File upload error");
                return $response->withStatus(302)->withHeader('Location', "/");
            }
        }

    public function all_files(Request $request, Response $response)
    {
        $max_files_page = 5;
        $page = intval($_GET['page'] ?? 1);
        $errors = [];

        $count_files = $this->file_model->get_count_files();
        $count_pages = ceil($count_files / $max_files_page);
        $all_files = [];
        if(($page <= 0 || is_string($page) || $page > $count_pages) && $count_files > 0){
            $page = 1;
            $errors['file_pag'] = "Please select an available file page";
        }
        if($count_files > 0) {
            $all_files = $this->file_model->get_files($max_files_page, $page);
        }
        $comment_model = new Comment($this->container);
        $comments =  $comment_model->get_count_comments();
        //trash
        foreach ($all_files as $key => $file){
            $all_files[$key]['count_comments'] = 0;
            foreach ($comments as $comment){
                if($comment['file_id'] == $file['id']){
                    $all_files[$key]['count_comments'] = $comment['count'];
                }
            }
        }
        return $this->container->get("view")->render($response, 'files.html', ['files' => $all_files, 'count_pages' => $count_pages, 'page' => $page,"errors"=>$errors]);
    }

    public function dowload(Request $request, Response $response,$args){
        $id = $args['id'];
        $file_info = $this->file_model->get_file($id);
        if( !is_null($file_info)) {
            rename($_SERVER['DOCUMENT_ROOT'] . "/../app/files/" . $file_info['fake_name'] . $file_info['new_type'], $_SERVER['DOCUMENT_ROOT'] . "/../app/files/" . $file_info['name'] . $file_info['old_type']);

            $file = $_SERVER['DOCUMENT_ROOT'] . "/../app/files/" . $file_info['name'] . $file_info['old_type'];
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            rename($_SERVER['DOCUMENT_ROOT'] . "/../app/files/" . $file_info['name'] . $file_info['old_type'], $_SERVER['DOCUMENT_ROOT'] . "/../app/files/" . $file_info['fake_name'] . $file_info['new_type']);
            return $response;
        }
        else{
            $this->container->get('flash')->addMessage('error', "This file dont find");
            return $response->withStatus(302)->withHeader('Location', "/files");
        }
        }
    public function search(Request $request, Response $response,$args){
        $search_text = empty(trim(isset($_GET['search_text']))) ? null : $_GET['search_text'];
        $comment_model = new Comment($this->container);
        $comments =  $comment_model->get_count_comments();
        if( is_null($search_text)){
            return $this->container->get("view")->render($response, 'search.html');
        }
        else {
            $_SESSION['search'] = $search_text;
            $search_result = $this->file_model->sphinx_search($search_text);
            $search_result_length = count($search_result);
            foreach ($search_result as $key => $file){
                $search_result[$key]['count_comments'] = 0;
                foreach ($comments as $comment){
                    if($comment['file_id'] == $file['id']){
                        $search_result[$key]['count_comments'] = $comment['count'];
                    }
                }
            }
            return $this->container->get("view")->render($response, 'search.html',['search_count'=>$search_result_length,"search_results"=>$search_result]);
        }
    }
}