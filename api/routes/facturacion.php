<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\FacturacionController;

$app->group('/api/facturacion', function (RouteCollectorProxy $group) {
    $group->post('/emitir', FacturacionController::class . ':emitirFactura');
    $group->get('/consultar/{id}', FacturacionController::class . ':consultarFactura');
});