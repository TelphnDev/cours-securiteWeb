<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use Firebase\JWT\JWT;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['erreur' => 'Méthode non autorisée']); exit; }

$body = json_decode(file_get_contents('php://input'), true);

if (empty($body['email']) || empty($body['password'])) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Email et mot de passe obligatoires']);
    exit;
}

$fichier = __DIR__ . '/../data/users.json';
$users   = json_decode(file_get_contents($fichier), true) ?? [];

$userTrouve = null;
foreach ($users as $u) {
    if ($u['email'] === $body['email']) { $userTrouve = $u; break; }
}

if ($userTrouve === null || !password_verify($body['password'], $userTrouve['password_hash'])) {
    http_response_code(401);
    echo json_encode(['erreur' => 'Identifiants incorrects']);
    exit;
}

$now     = time();
$payload = [
    'sub'   => $userTrouve['id'],
    'email' => $userTrouve['email'],
    'iat'   => $now,
    'exp'   => $now + JWT_EXPIRATION,
];

$token = JWT::encode($payload, JWT_SECRET, 'HS256');

echo json_encode(['token' => $token]);