<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\ProveedorController;

$app->group('/api/proveedores', function (RouteCollectorProxy $group) {
    $group->post('', ProveedorController::class . ':crearProveedor');
    $group->get('', ProveedorController::class . ':listarProveedores');
    $group->get('/{id}', ProveedorController::class . ':obtenerProveedor');
    $group->put('/{id}', ProveedorController::class . ':actualizarProveedor');
    $group->delete('/{id}', ProveedorController::class . ':eliminarProveedor');
});