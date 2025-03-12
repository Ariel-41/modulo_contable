<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\ReporteController;

$app->group('/api/reportes', function (RouteCollectorProxy $group) {
    $group->get('/ventas', ReporteController::class . ':reporteVentas');
    $group->get('/compras', ReporteController::class . ':reporteCompras');
});