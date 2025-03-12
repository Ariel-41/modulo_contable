<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AuthController;

$app->group('/api/auth', function (RouteCollectorProxy $group) {
    $group->post('/login', AuthController::class . ':login');
    $group->get('/csrf', AuthController::class . ':generarTokenCsrf');
});