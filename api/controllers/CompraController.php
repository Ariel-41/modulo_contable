<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;
use PDO;

class CompraController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function registrarCompra(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (!v::key('proveedor_id', v::intVal()->positive())->validate($data) ||
            !v::key('monto', v::floatVal()->positive())->validate($data) ||
            !v::key('fecha_compra', v::date('Y-m-d'))->validate($data)) {
            $response->getBody()->write(json_encode(['error' => 'Datos inválidos']));
            return $response->withStatus(400);
        }

        $proveedor_id = filter_var($data['proveedor_id'], FILTER_VALIDATE_INT);
        $monto = filter_var($data['monto'], FILTER_VALIDATE_FLOAT);
        $fecha_