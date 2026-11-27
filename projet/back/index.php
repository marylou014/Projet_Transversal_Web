<?php

declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$chemin  = trim(substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', strlen('/api')), '/');
$parties = $chemin === '' ? [] : explode('/', $chemin);
$route   = $parties[0] ?? '';
$params  = array_slice($parties, 1);
$methode = $_SERVER['REQUEST_METHOD'];

if ($route === '') {
    json(['routes' => array_map(fn($f) => '/api/' . basename($f, '.php'), glob(__DIR__ . '/routes/*.php'))]);
}
if (!preg_match('/^[A-Za-z0-9_-]+$/', $route) || !is_file($fichier = __DIR__ . "/routes/$route.php")) {
    json(['erreur' => "route inconnue : /api/$route"], 404);
}
try {
    require $fichier;
} catch (Throwable $e) {
    error_log((string) $e);
    json(['erreur' => $e->getMessage()], 500);
}
