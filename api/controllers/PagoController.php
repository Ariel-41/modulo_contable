<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use MercadoPago\SDK;

class PagoController
{
    public function crearPago(Request $request, Response $response)
    {
        SDK::setAccessToken('TU_ACCESS_TOKEN');

        $data = $request->getParsedBody();
        $pago = new \MercadoPago\Payment();
        $pago->transaction_amount = $data['monto'];
        $pago->token = $data['token'];
        $pago->description = $data['descripcion'];
        $pago->installments = $data['cuotas'];
        $pago->payment_method_id = $data['metodo_pago'];
        $pago->payer = [
            'email' => $data['email']
        ];

        $pago->save();

        $response->getBody()->write(json_encode($pago));
        return $response->withHeader('Content-Type', 'application/json');
    }
}