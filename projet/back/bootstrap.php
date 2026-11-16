<?php
// Partagé par le front et l'API : connexion à la BDD et réponses JSON.
// Dans une page du front :  require __DIR__ . '/../back/bootstrap.php';   puis   db()->query(...)
declare(strict_types=1);

function env_projet(): array {
    static $e = null;
    return $e ??= (@parse_ini_file('/opt/projet/.env', false, INI_SCANNER_RAW) ?: []);
}

function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $e = env_projet();
    $dsn = match ($e['DB_TYPE'] ?? '') {
        'mariadb', 'mysql' => "mysql:host={$e['DB_HOST']};port={$e['DB_PORT']};dbname={$e['DB_NAME']};charset=utf8mb4",
        'postgresql'       => "pgsql:host={$e['DB_HOST']};port={$e['DB_PORT']};dbname={$e['DB_NAME']}",
        default            => throw new RuntimeException('Ce projet n\'a pas de BDD SQL'),
    };
    return $pdo = new PDO($dsn, $e['DB_USER'], $e['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

// Envoie une réponse JSON et s'arrête :  json(['ok' => true]);   json(['erreur' => '...'], 400);
function json(mixed $donnees, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($donnees, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Corps JSON envoyé par le front (fetch avec body: JSON.stringify(...))
function corps(): array {
    return json_decode(file_get_contents('php://input') ?: '[]', true) ?? [];
}
