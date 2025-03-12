<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\PadronController;

$app->group('/api/padrones', function (RouteCollectorProxy $group) {
    $group->get('/consultar/{cuit}', PadronController::class . ':consultarPadron');
});