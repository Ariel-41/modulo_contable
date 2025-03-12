<?php
use PHPUnit\Framework\TestCase;
use App\Controllers\ClienteController;
use PDO;
use PDOStatement;

class ClienteControllerTest extends TestCase
{
    private $db;
    private $controller;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
        $this->controller = new ClienteController($this->db);
    }

    public function testCrearCliente()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);

        $this->db->method('prepare')->willReturn($stmt);

        $request = $this->createMock(Request::class);
        $request->method('getParsedBody')->willReturn([
            'cuit' => '12345678901',
            'nombre' => 'Cliente de Prueba',
            'direccion' => 'Calle Falsa 123',
            'condicion_fiscal' => 'Responsable Inscripto'
        ]);

        $response = $this->createMock(Response::class);
        $response->method('getBody')->willReturnCallback(function ($data) {
            $this->assertJsonStringEqualsJsonString(json_encode(['message' => 'Cliente creado']), $data);
        });

        $this->controller->crearCliente($request, $response);
    }
}