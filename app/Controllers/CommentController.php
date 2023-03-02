<?php

namespace App\Controllers;


use App\Models\Comment;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class CommentController
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function show(Request $request, Response $response,$args){
        $file_id = $args['id'];
        $comment_model = new Comment($this->container);
        $comments = json_encode($comment_model->get_comment($file_id));
        $response->getBody()->write($comments);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function add(Request $request, Response $response,$args){
        $file_id = $args['id'];
        $parsedBody = $request->getParsedBody();
        $date = date("Y-m-d H:i:s");
        $comment = !empty(trim($parsedBody['comment_text'])) ? $parsedBody['comment_text'] : null;
        $user_id = $_COOKIE['user_id'];
        $errors = [];
        if($comment == null){
            $errors['value'] = "Input text is empty";
        }
        elseif(strlen($comment) > 200){
            $errors['value'] = "Max length text comment 200 charsets";
        }

        if(empty($errors)) {
            $comment_model = new Comment($this->container);
            $comment_model->add($date, trim($comment), $user_id, $file_id);
            $status = json_encode(['status'=>'success',"value"=>$comment]);
            $response->getBody()->write($status);
            return $response;
        }
        else{
            $status = json_encode(['status'=>['error'=> $errors['value']]]);
            $response->getBody()->write($status);
            return $response;
        }
    }
}