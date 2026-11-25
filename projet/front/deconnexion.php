<?php
// ===========================================================================
// Déconnexion (Alexis)
// ---------------------------------------------------------------------------
// Il suffit d'un lien vers cette page pour se déconnecter :
//   <a href="/deconnexion.php">Se déconnecter</a>
// ===========================================================================
require __DIR__ . '/../back/bootstrap.php';

demarrer_session();
$_SESSION = [];                                      // on vide la mémoire de la session
setcookie(session_name(), '', time() - 3600, '/');   // on supprime le cookie (date dans le passé)
session_destroy();                                   // on détruit la session sur le serveur

header('Location: /');   // retour à la page d'arrivée
exit;
