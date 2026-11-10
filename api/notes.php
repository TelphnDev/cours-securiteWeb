<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Le middleware : sans token valide, le script s'arrête ici avec une 401
require_once __DIR__ . '/auth.php';
$utilisateur = verifierToken();

$fichier = __DIR__ . '/../data/notes.json';
$methode = $_SERVER['REQUEST_METHOD'];

if ($methode === 'GET') {

    $notes = json_decode(file_get_contents($fichier), true) ?? [];

    // On ne renvoie que les notes de l'utilisateur connecté
    $mesNotes = [];
    foreach ($notes as $note) {
        if (($note['id_utilisateur'] ?? null) === $utilisateur->sub) {
            $mesNotes[] = $note;
        }
    }
    echo json_encode($mesNotes);

} elseif ($methode === 'POST') {

    $body = json_decode(file_get_contents('php://input'), true);

    if (empty($body['texte'])) {
        http_response_code(400);
        echo json_encode(['erreur' => 'Le champ texte est obligatoire']);
        exit;
    }

    $notes = json_decode(file_get_contents($fichier), true) ?? [];
    $nouvelle = [
        'id'             => count($notes) + 1,
        'texte'          => $body['texte'],
        'id_utilisateur' => $utilisateur->sub
    ];
    $notes[] = $nouvelle;
    file_put_contents($fichier, json_encode($notes, JSON_PRETTY_PRINT));

    http_response_code(201);
    echo json_encode($nouvelle);

} else {
    http_response_code(405);
    echo json_encode(['erreur' => 'Méthode non autorisée']);
}