<?php
// Middleware d'authentification : vérifie le token JWT avant d'accéder à une route protégée
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

// Arrête la requête avec une erreur 401 Unauthorized
function refuser(string $message): void
{
    http_response_code(401);
    echo json_encode(['erreur' => $message]);
    exit;
}

// Lit l'en-tête Authorization envoyé par le client
function lireEnTeteAuthorization(): string
{
    foreach (getallheaders() as $nom => $valeur) {
        if (strtolower($nom) === 'authorization') {
            return $valeur;
        }
    }
    return $_SERVER['HTTP_AUTHORIZATION'] ?? '';
}

// Renvoie le payload du token s'il est valide, sinon répond 401 et arrête tout
function verifierToken(): object
{
    $entete = lireEnTeteAuthorization();

    if (!str_starts_with($entete, 'Bearer ')) {
        refuser('Token manquant');
    }

    $token = substr($entete, 7); // ce qui suit « Bearer »

    try {
        return JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
    } catch (ExpiredException $e) {
        refuser('Token expiré');
    } catch (SignatureInvalidException $e) {
        refuser('Signature invalide');
    } catch (Exception $e) {
        refuser('Token invalide');
    }
}