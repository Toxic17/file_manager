<?php
use Slim\Views\Twig;

return function($container){
    //set flash
    $flash = require_once __DIR__."/../core/flash.php";
    $flash($container);

    //set twig view
    $container->set('view', function($container) {
        $twig = Twig::create('../app/views', ['cache' => false,"debug"=>true]);
        $environment = $twig->getEnvironment();
        $environment->addGlobal("flash",$container->get('flash'));
        $environment->addGlobal("session",$_SESSION);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        return $twig;
    });

    //get settings
    $settings = require_once __DIR__."/../core/settings.php";
    $settings($container);

    //connection to data base
    $container->set('pdo',function($container){
        $db_seettings = $container->get('settings')['db'];
        $pdo = new PDO('mysql:host=' . $db_seettings['host'] . ';dbname=' . $db_seettings['dbname'],
            $db_seettings['user'], $db_seettings['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    });

    //connect sphinx database
    $container->set('sphinx',function($container){
        $pdo = new PDO('mysql:host=127.0.0.1;port=9306');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    });
}
?>