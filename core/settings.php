<?php
use \Psr\Container\ContainerInterface;

return function(ContainerInterface $container){
    $container->set('settings',function (){
      return ['errors'=>[
              'displayErrorDetails'=>false,
              'logErrors'=>false,
              'logErrorDetails'=>false
          ],
          'db'=>[
                'host'=>'localhost',
                'user'=>'root',
                'pass'=>'root',
                'dbname'=>'file_manager'
          ],
          'files'=>[
              'max_size'=> 200 * (10**6)
          ]
          ];
  });
};