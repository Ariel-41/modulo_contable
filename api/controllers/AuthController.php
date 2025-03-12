<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;

class AuthController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Autentica al usuario y genera un token JWT.
     */
    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        // Validar datos de entrada
        if (!isset($data['username']) || !isset($data['password'])) {
            $response->getBody()->write(json_encode(['error' => 'Usuario y contraseña son obligatorios']));
            return $response->withStatus(400);
        }

        $username = htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8');

        // Buscar el usuario en la base de datos
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar credenciales
        if ($usuario && password_verify($password, $usuario['password'])) {
            // Generar token JWT
            $payload = [
                'user_id' => $usuario['id'],
                'username' => $usuario['username'],
                'exp' => time() + 3600 // Expira en 1 hora
            ];
            $jwt = JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');

            // Devolver el token
            $response->getBody()->write(json_encode(['token' => $jwt]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        // Credenciales inválidas
        $response->getBody()->write(json_encode(['error' => 'Credenciales inválidas']));
        return $response->withStatus(401);
    }

    /**
     * Genera un token CSRF para proteger contra ataques CSRF.
     */
    public function generarTokenCsrf(Request $request, Response $response)
    {
        session_start();
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;

        $response->getBody()->write(json_encode(['csrf_token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}