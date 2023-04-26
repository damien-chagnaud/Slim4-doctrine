<?php

declare(strict_types=1);

#Users:
use App\Application\Actions\User\GenerateTokenAction;

#Tampons:
use App\Application\Actions\Tampon\ListTamponAction;
use App\Application\Actions\Tampon\ViewTamponAction;
use App\Application\Actions\Tampon\NewTamponAction;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Tuupola\Middleware\HttpBasicAuthentication\PdoAuthenticator;
use App\Application\Middleware\JwtAuthentication\PdoGetUserToken;
use App\Application\Middleware\JwtAuthentication;


//Tuupola\Middleware\JwtAuthentication

return function (App $app) {
    $pdo = new PDO($_ENV['PDO_URL'], $_ENV['PDO_USER'], $_ENV['PDO_PASS']);

    //LOGGER
    $logger = new Logger('slim-app');
    $processor = new UidProcessor();
    $logger->pushProcessor($processor);
    $handler = new StreamHandler(isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log', Logger::DEBUG);
    $logger->pushHandler($handler);


    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        //$response->getBody()->write('</br>session:'.print_r($this, true));
        return $response;
    });

    $app->get('/token', GenerateTokenAction::class)->add(new Tuupola\Middleware\HttpBasicAuthentication([
        "realm" => "Protected",
        "before" => function ($request, $arguments) {
            return $request->withAttribute("user", $arguments["user"]);
        },
        "authenticator" => new PdoAuthenticator([
            "pdo" => $pdo,
            "table" => "users",
            "user" => "username",
            "hash" => "password"
        ]),
    ]));

    $app->group('/tampons', function (Group $group) {
        $group->get('', ListTamponAction::class);
    });

    $app->group('/tampons/admin', function (Group $group) {
        $group->get('', function (Request $request, Response $response) {$response->getBody()->write('Tampons admin pages:'. print_r($request->getAttribute('token'),true));return $response;});
        $group->post('/new', NewTamponAction::class);
        $group->post('/delete', ListTamponAction::class);
        $group->post('/stock', ListTamponAction::class);
    })->add(new  JwtAuthentication([
        "logger" => $logger,
        "attribute" => "token",
        "authenticator" => new PdoGetUserToken([
            "pdo" => $pdo,
            "table" => "users",
            "user" => "username",
            "hash" => "password"
        ]),
        "error" =>  function ($response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            $response->getBody()->write(
                json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            );
            return $response->withHeader("Content-Type", "application/json");
        }
    ]));

    

};
