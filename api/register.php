<?php
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

foreach ($users as $u) {
    if ($u['email'] === $body['email']) {
        http_response_code(409);
        echo json_encode(['erreur' => 'Cet email est déjà utilisé']);
        exit;
    }
}

$nouveauUser = [
    'id'            => count($users) + 1,
    'email'         => $body['email'],
    'password_hash' => password_hash($body['password'], PASSWORD_BCRYPT)
];
$users[] = $nouveauUser;
file_put_contents($fichier, json_encode($users, JSON_PRETTY_PRINT));

http_response_code(201);
echo json_encode(['message' => 'Compte créé', 'email' => $nouveauUser['email']]);