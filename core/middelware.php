<?php
use Slim\App;

return function(App $app){
    $settings = $app->getContainer()->get("settings");;
    $app->addErrorMiddleware($settings['errors']['displayErrorDetails'],$settings['errors']['logErrors'],$settings['errors']['logErrorDetails']);
};