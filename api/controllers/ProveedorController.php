<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;
use PDO;

class ProveedorController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function crearProveedor(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (!v::key('cuit', v::digit()->length(11, 11))->validate($data) ||
            !v::key('nombre', v::stringType()->length(1, 100))->validate($data)) {
            $response->getBody()->write(json_encode(['error' => 'Datos inválidos']));
            return $response->withStatus(400);
        }

        $cuit = htmlspecialchars($data['cuit'], ENT_QUOTES, 'UTF-8');
        $nombre = htmlspecialchars($data['nombre'], ENT_QUOTES, 'UTF-8');
        $direccion = isset($data['direccion']) ? htmlspecialchars($data['direccion'], ENT_QUOTES, 'UTF-8') : null;
        $condicion_fiscal = isset($data['condicion_fiscal']) ? htmlspecialchars($data['condicion_fiscal'], ENT_QUOTES, 'UTF-8') : null;

        $stmt = $this->db->prepare("INSERT INTO proveedores (cuit, nombre, direccion, condicion_fiscal) VALUES (:cuit, :nombre, :direccion, :condicion_fiscal)");
        $stmt->execute([
            'cuit' => $cuit,
            'nombre' => $nombre,
            'direccion' => $direccion,
            'condicion_fiscal' => $condicion_fiscal,
        ]);

        $response->getBody()->write(json_encode(['message' => 'Proveedor creado']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function listarProveedores(Request $request, Response $response)
    {
        $stmt = $this->db->query("SELECT * FROM proveedores");
        $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($proveedores));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function obtenerProveedor(Request $request, Response $response, $args)
    {
        $id = filter_var($args['id'], FILTER_VALIDATE_INT);

        if (!$id) {
            $response->getBody()->write(json_encode(['error' => 'ID inválido']));
            return $response->withStatus(400);
        }

        $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$proveedor) {
            $response->getBody()->write(json_encode(['error' => 'Proveedor no encontrado']));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($proveedor));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function actualizarProveedor(Request $request, Response $response, $args)
    {
        $id = filter_var($args['id'], FILTER_VALIDATE_INT);
        $data = $request->getParsedBody();

        if (!$id || !v::key('nombre', v::stringType()->length(1, 100))->validate($data)) {
            $response->getBody()->write(json_encode(['error' => 'Datos inválidos']));
            return $response->withStatus(400);
        }

        $nombre = htmlspecialchars($data['nombre'], ENT_QUOTES, 'UTF-8');
        $direccion = isset($data['direccion']) ? htmlspecialchars($data['direccion'], ENT_QUOTES, 'UTF-8') : null;
        $condicion_fiscal = isset($data['condicion_fiscal']) ? htmlspecialchars($data['condicion_fiscal'], ENT_QUOTES, 'UTF-8') : null;

        $stmt = $this->db->prepare("UPDATE proveedores SET nombre = :nombre, direccion = :direccion, condicion_fiscal = :condicion_fiscal WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'nombre' => $nombre,
            'direccion' => $direccion,
            'condicion_fiscal' => $condicion_fiscal,
        ]);

        $response->getBody()->write(json_encode(['message' => 'Proveedor actualizado']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function eliminarProveedor(Request $request, Response $response, $args)
    {
        $id = filter_var($args['id'], FILTER_VALIDATE_INT);

        if (!$id) {
            $response->getBody()->write(json_encode(['error' => 'ID inválido']));
            return $response->withStatus(400);
        }

        $stmt = $this->db->prepare("DELETE FROM proveedores WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $response->getBody()->write(json_encode(['message' => 'Proveedor eliminado']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}