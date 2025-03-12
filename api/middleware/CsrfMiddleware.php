<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class CsrfMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        session_start();
        $data = $request->getParsedBody();

        if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Token CSRF inválido']));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}