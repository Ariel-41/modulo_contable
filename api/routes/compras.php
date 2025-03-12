<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\CompraController;

$app->group('/api/compras', function (RouteCollectorProxy $group) {
    $group->post('', CompraController::class . ':registrarCompra');
    $group->get('', CompraController::class . ':listarCompras');
});