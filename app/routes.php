<?php

declare(strict_types=1);

#Users:
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
#Tampons:
use App\Application\Actions\Tampon\ListTamponAction;
use App\Application\Actions\Tampon\ViewTamponAction;
use App\Application\Actions\Tampon\NewTamponAction;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        $response->getBody()->write('</br>session:'. $request->getAttribute('session'));
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->get('/tampons', ListTamponAction::class);
    
    $app->group('/tampons/admin', function (Group $group) {
        $group->get('', function (Request $request, Response $response) {$response->getBody()->write('Tampons admin pages');return $response;});
        $group->post('/new', NewTamponAction::class);
        $group->get('/delete', ListTamponAction::class);
        $group->get('/stock', ListTamponAction::class);
    })->add(new Tuupola\Middleware\HttpBasicAuthentication([
        "secure" => true,
        "relaxed" => ["localhost"],
        "users" => [
            "root" => "t00r",
        ]
    ]));;

    

};
