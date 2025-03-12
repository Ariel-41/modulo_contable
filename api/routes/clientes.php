<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\ClienteController;

$app->group('/api/clientes', function (RouteCollectorProxy $group) {
    $group->post('', ClienteController::class . ':crearCliente');
    $group->get('', ClienteController::class . ':listarClientes');
    $group->get('/{id}', ClienteController::class . ':obtenerCliente');
    $group->put('/{id}', ClienteController::class . ':actualizarCliente');
    $group->delete('/{id}', ClienteController::class . ':eliminarCliente');
});