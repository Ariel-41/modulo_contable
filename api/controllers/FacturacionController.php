<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;
use PDO;
use Afip;

class FacturacionController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function emitirFactura(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (!v::key('monto', v::floatVal()->positive())->validate($data) ||
            !v::key('tipo', v::in(['A', 'B', 'C']))->validate($data) ||
            !v::key('cliente_id', v::intVal()->positive())->validate($data)) {
            $response->getBody()->write(json_encode(['error' => 'Datos inválidos']));
            return $response->withStatus(400);
        }

        $monto = filter_var($data['monto'], FILTER_VALIDATE_FLOAT);
        $tipo = htmlspecialchars($data['tipo'], ENT_QUOTES, 'UTF-8');
        $cliente_id = filter_var($data['cliente_id'], FILTER_VALIDATE_INT);

        $afip = new Afip([
            'CUIT' => getenv('AFIP_CUIT'),
            'cert' => getenv('AFIP_CERT'),
            'key' => getenv('AFIP_KEY'),
        ]);

        $factura = $afip->ElectronicBilling->createVoucher([
            'CantReg' => 1,
            'PtoVta' => 1,
            'CbteTipo' => $tipo === 'A' ? 1 : ($tipo === 'B' ? 6 : 11),
            'ImpTotal' => $monto,
        ]);

        $stmt = $this->db->prepare("INSERT INTO comprobantes (cliente_id, tipo, monto, cae) VALUES (:cliente_id, :tipo, :monto, :cae)");
        $stmt->execute([
            'cliente_id' => $cliente_id,
            'tipo' => $tipo,
            'monto' => $monto,
            'cae' => $factura['CAE'],
        ]);

        $response->getBody()->write(json_encode($factura));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function consultarFactura(Request $request, Response $response, $args)
    {
        $id = filter_var($args['id'], FILTER_VALIDATE_INT);

        if (!$id) {
            $response->getBody()->write(json_encode(['error' => 'ID inválido']));
            return $response->withStatus(400);
        }

        $stmt = $this->db->prepare("SELECT * FROM comprobantes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$factura) {
            $response->getBody()->write(json_encode(['error' => 'Factura no encontrada']));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($factura));
        return $response->withHeader('Content-Type', 'application/json');
    }
}