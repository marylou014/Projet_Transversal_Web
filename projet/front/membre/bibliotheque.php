<?php
// ===========================================================================
// Ma bibliothèque (Marylou)
// ---------------------------------------------------------------------------
// Page réservée aux membres connectés.
// ===========================================================================

// On charge les fonctions communes (base de données, session...)
require __DIR__ . '/../../back/bootstrap.php';

// Pas connecté → retour à la page d'arrivée. Connecté → $moi contient
// l'utilisateur : $moi['id'], $moi['nom'], $moi['admin'].
// Ça doit être fait AVANT d'afficher quoi que ce soit (sinon la redirection ne marche pas).
$moi = page_connexion_requise();

// En-tête commun (Marylou)
include __DIR__ . '/../includes/header.php';
?>

<main>
    <h1>Ma bibliothèque</h1>
    <!-- Contenu de la page à faire -->
</main>

<?php
// Pied de page commun (Marylou)
include __DIR__ . '/../includes/footer.php';
