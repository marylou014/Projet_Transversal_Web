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


// ===========================================================================
// CONNEXION DES UTILISATEURS (Alexis)
// ---------------------------------------------------------------------------
// La SESSION, c'est la mémoire du serveur d'une page à l'autre.
// Quand quelqu'un se connecte, on range ses infos dans $_SESSION['utilisateur'].
// Le navigateur garde un cookie qui permet au serveur de le reconnaître.
// ===========================================================================

// Démarre la session (si elle n'est pas déjà démarrée)
function demarrer_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,   // le JavaScript ne peut pas lire le cookie (protection contre le vol)
            'cookie_samesite' => 'Lax',  // le cookie n'est pas envoyé depuis un autre site
        ]);
    }
}

// Renvoie l'utilisateur connecté, ou null si personne n'est connecté.
// Exemple de résultat : ['id' => 2, 'nom' => 'alexis', 'admin' => false]
function utilisateur(): ?array {
    demarrer_session();
    return $_SESSION['utilisateur'] ?? null;
}

// À mettre TOUT EN HAUT d'une page réservée aux membres connectés.
// Si personne n'est connecté, on renvoie vers la page d'arrivée (qui a le formulaire).
function page_connexion_requise(): array {
    $moi = utilisateur();
    if ($moi === null) {
        header('Location: /');   // "Location" = va sur cette page
        exit;                    // arrête le script : le reste de la page n'est pas envoyé
    }
    return $moi;
}

// Même chose pour une route de l'API (répond en JSON au lieu de rediriger)
function api_connexion_requise(): array {
    $moi = utilisateur();
    if ($moi === null) {
        json(['erreur' => 'tu dois être connecté'], 401);   // 401 = non connecté
    }
    return $moi;
}

// Protège un texte avant de l'afficher dans du HTML.
// Sans ça, un nom comme "<script>..." serait exécuté par le navigateur.
// Utilisation dans une page :  echo e($moi['nom']);
function e(?string $texte): string {
    return htmlspecialchars($texte ?? '', ENT_QUOTES, 'UTF-8');
}
