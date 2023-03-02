<?php
namespace App\Middleware;

use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Psr\Container\ContainerInterface;

class MyMiddelware
{
    private $container;
    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $route = $request->getUri()->getPath();
        if($route != "/files/search"){
            unset($_SESSION['search']);
        }
        if(!isset($_COOKIE['user_id'])) {
            $this->set_user_id();
        }
        $response = $handler->handle($request);
        $existingContent = (string) $response->getBody();

        $response = new Response();
        $response->getBody()->write($existingContent);
        return $response;
    }

    public function check_user_id($id){
        $user_model = new User($this->container);
        $res = $user_model->getUser($id);
        return $res;
    }

    public function set_user_id(){
        $user_id = random_int(1,999);
        $user_exist = count($this->check_user_id($user_id));
        if($user_exist > 0){
            $this->set_user_id();
        }
        else{
            setcookie('user_id',$user_id);
        }
    }
}