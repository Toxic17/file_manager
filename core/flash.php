<?php
use Slim\Flash\Messages;
use DI\Container;

return function(Container $container){
    $container->set('flash',function(){
        return new Messages();
    }
    );
};