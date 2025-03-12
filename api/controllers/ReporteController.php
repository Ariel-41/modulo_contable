<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class ReporteController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function generarLibroIva(Request $request, Response $response)
    {
        $stmt = $this->db->query("SELECT * FROM comprobantes WHERE tipo IN ('A', 'B')");
        $comprobantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($comprobantes));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function generarLibroVentas(Request $request, Response $response)
    {
        $stmt = $this->db->query("SELECT * FROM comprobantes WHERE tipo = 'A'");
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($ventas));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function generarLibroCompras(Request $request, Response $response)
    {
        $stmt = $this->db->query("SELECT * FROM comprobantes WHERE tipo = 'B'");
        $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($compras));
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function generarReportePdf(Request $request, Response $response)
    {
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Reporte de Ventas', 0, 1, 'C');
        $pdf->Output('reporte.pdf', 'I');

        return $response->withHeader('Content-Type', 'application/pdf');
    }
}