<?php
session_start();
use Slim\Factory\AppFactory;
use DI\Container;


require __DIR__ . '/../vendor/autoload.php';

//create Di container
$container = new Container;


$bootstrap = require_once __DIR__."/../app/bootstrap.php";
$bootstrap($container);
//create app
AppFactory::setContainer($container);
$app = AppFactory::create();

//set middelware
$middelware = require_once __DIR__."/../core/middelware.php";
$middelware($app);

//get routes
$routes = require_once __DIR__ . "/../core/routes.php";
$routes($app);


// Run app
$app->run();