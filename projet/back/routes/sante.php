<?php
// GET /api/sante : le backend et la BDD répondent-ils ?
try {
    db()->query('SELECT 1');
    $bdd = 'ok';
} catch (Throwable $e) {
    $bdd = $e->getMessage();
}
json(['backend' => 'ok', 'php' => PHP_VERSION, 'bdd' => $bdd]);
