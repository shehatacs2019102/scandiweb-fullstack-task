<?php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../DBCreate.php';
require_once '../DataInsertion.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->post('/graphql', [App\Controller\GraphQL::class, 'handle']);
});
$routeInfo = $dispatcher->dispatch
(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
   
switch ($routeInfo[0]) 
{
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $handler($vars);
        break;
}