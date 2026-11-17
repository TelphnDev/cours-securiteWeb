<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['erreur' => 'Méthode non autorisée']); exit; }

$body = json_decode(file_get_contents('php://input'), true);

if (empty($body['email']) || empty($body['password'])) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Email et mot de passe obligatoires']);
    exit;
}

$pdo = connexion();

$sql = "SELECT * FROM users WHERE email = '" . $body['email'] . "'";
$userTrouve = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

if (!$userTrouve) {
    http_response_code(401);
    echo json_encode(['erreur' => 'Identifiants incorrects']);
    exit;
}

$now = time();
$payload = ['sub' => (int) $userTrouve['id'], 'email' => $userTrouve['email'], 'iat' => $now, 'exp' => $now + JWT_EXPIRATION];
echo json_encode(['token' => JWT::encode($payload, JWT_SECRET, 'HS256')]);