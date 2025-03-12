<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Afip;

class PadronController
{
    public function consultarPadron(Request $request, Response $response, $args)
    {
        $cuit = $args['cuit'];

        $afip = new Afip([
            'CUIT' => getenv('AFIP_CUIT'),
            'cert' => getenv('AFIP_CERT'),
            'key' => getenv('AFIP_KEY'),
        ]);

        try {
            $contribuyente = $afip->RegisterScopeFive->GetContributorData($cuit);
            $response->getBody()->write(json_encode($contribuyente));
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}