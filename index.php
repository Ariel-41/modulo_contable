<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\TokenBucket;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Crear la aplicación Slim
$app = AppFactory::create();

// Middleware para parsear JSON
$app->addBodyParsingMiddleware();

// Middleware de autenticación JWT
$app->add(new AuthMiddleware());

// Middleware de manejo de errores
$errorMiddleware = $app->addErrorMiddleware(
    getenv('APP_ENV') === 'development', // Mostrar detalles de errores en desarrollo
    true, // Registrar errores
    true  // Mostrar errores al cliente
);

// Middleware de rate limiting
$app->group('/api', function (RouteCollectorProxy $group) {
    $group->get('/clientes', ClienteController::class . ':listarClientes');
})->add(new TokenBucket([
    'bucket' => [
        'capacity' => 100, // Número máximo de solicitudes
        'leak' => 1,       // Solicitudes por segundo
    ],
]));

// Ruta de inicio
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Módulo Contable para PYMES");
    return $response;
});

// Incluir rutas de la API
require __DIR__ . '/../api/routes/auth.php';
require __DIR__ . '/../api/routes/facturacion.php';
require __DIR__ . '/../api/routes/clientes.php';
require __DIR__ . '/../api/routes/proveedores.php';
require __DIR__ . '/../api/routes/compras.php';
require __DIR__ . '/../api/routes/reportes.php';
require __DIR__ . '/../api/routes/pagos.php';

// Middleware de CSRF
$app->post('/api/clientes', ClienteController::class . ':crearCliente')
    ->add(new CsrfMiddleware());

// Ejecutar la aplicación
$app->run();