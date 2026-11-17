<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$utilisateur = verifierToken();
$pdo = connexion();
$methode = $_SERVER['REQUEST_METHOD'];

if ($methode === 'GET') {

    $requete = $pdo->prepare('SELECT id, texte FROM notes WHERE id_utilisateur = ? ORDER BY id');
    $requete->execute([$utilisateur->sub]);
    echo json_encode($requete->fetchAll(PDO::FETCH_ASSOC));

} elseif ($methode === 'POST') {

    $body = json_decode(file_get_contents('php://input'), true);

    if (empty($body['texte'])) {
        http_response_code(400);
        echo json_encode(['erreur' => 'Le champ texte est obligatoire']);
        exit;
    }

    $requete = $pdo->prepare('INSERT INTO notes (texte, id_utilisateur) VALUES (?, ?)');
    $requete->execute([$body['texte'], $utilisateur->sub]);

    http_response_code(201);
    echo json_encode(['id' => (int) $pdo->lastInsertId(), 'texte' => $body['texte']]);

} else {
    http_response_code(405);
    echo json_encode(['erreur' => 'Méthode non autorisée']);
}