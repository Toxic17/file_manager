<?php

use App\Middleware\MyMiddelware;
use Slim\App;
use App\Controllers\FileController;
use App\Controllers\CommentController;

return function(App $app){
    $app->get('/', [FileController::class,"index"])->add(new MyMiddelware($app->getContainer()));

    $app->any('/loadFile', [FileController::class,"load"]);

    $app->get('/files',[FileController::class,'all_files'])->add(new MyMiddelware($app->getContainer()));

    $app->get('/files/load/{id}',[FileController::class,'dowload'])->add(new MyMiddelware($app->getContainer()));

    $app->get('/files/comments/{id}',[CommentController::class,'show'])->add(new MyMiddelware($app->getContainer()));

    $app->post('/files/comments/add/{id}',[CommentController::class,'add'])->add(new MyMiddelware($app->getContainer()));

    $app->get('/files/search',[FileController::class,'search'])->setName("search")->add(new MyMiddelware($app->getContainer()));
};