<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['erreur' => 'Méthode non autorisée']); exit; }

$body = json_decode(file_get_contents('php://input'), true);

if (empty($body['email']) || empty($body['password'])) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Email et mot de passe obligatoires']);
    exit;
}

$pdo = connexion();

$requete = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$requete->execute([$body['email']]);
if ($requete->fetch()) {
    http_response_code(409);
    echo json_encode(['erreur' => 'Cet email est déjà utilisé']);
    exit;
}

$requete = $pdo->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
$requete->execute([$body['email'], password_hash($body['password'], PASSWORD_BCRYPT)]);

http_response_code(201);
echo json_encode(['message' => 'Compte créé', 'email' => $body['email']]);