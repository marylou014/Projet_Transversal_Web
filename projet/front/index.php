<?php
// Page d'accueil du site : remplace-la. Le code du backend est accessible par ce require :
require __DIR__ . '/../back/bootstrap.php';
try { db()->query('SELECT 1'); $bdd = 'connectée'; } catch (Throwable $e) { $bdd = 'erreur : ' . $e->getMessage(); }
?>
<!DOCTYPE html>
<html lang="fr"><head><meta charset="utf-8"><title>projet-transversal-web</title></head>
<body style="font-family:sans-serif;max-width:640px;margin:4rem auto">
<h1>Projet projet-transversal-web</h1>
<p>PHP <?= PHP_VERSION ?>, BDD <?= htmlspecialchars($bdd) ?>.</p>
<p>Backend : <span id="api">…</span></p>
<script>
fetch("/api/sante").then(r => r.json()).then(d => document.getElementById("api").textContent = JSON.stringify(d))
  .catch(e => document.getElementById("api").textContent = "pas de réponse (" + e + ")");
</script>
</body></html>
