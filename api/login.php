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

// L'email est envoyé comme donnée, jamais comme code
$requete = $pdo->prepare('SELECT id, email, password_hash FROM users WHERE email = ?');
$requete->execute([$body['email']]);
$userTrouve = $requete->fetch(PDO::FETCH_ASSOC);

// On revérifie toujours le mot de passe
if (!$userTrouve || !password_verify($body['password'], $userTrouve['password_hash'])) {
    http_response_code(401);
    echo json_encode(['erreur' => 'Identifiants incorrects']);
    exit;
}

$now = time();
$payload = ['sub' => (int) $userTrouve['id'], 'email' => $userTrouve['email'], 'iat' => $now, 'exp' => $now + JWT_EXPIRATION];
echo json_encode(['token' => JWT::encode($payload, JWT_SECRET, 'HS256')]);