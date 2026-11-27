<?php
// ===========================================================================
// Accueil membre (Alexis)
// ---------------------------------------------------------------------------
// La première page qu'on voit une fois connecté :
//   - un message de bienvenue avec le nom
//   - 3 chiffres : films vus, films à voir, avis écrits
//   - des raccourcis vers les autres pages
// ===========================================================================

// On charge les fonctions communes (base de données, session...)
require __DIR__ . '/../../back/bootstrap.php';

// Pas connecté → retour à la page d'arrivée.
// Connecté → $moi contient : $moi['id'], $moi['nom'], $moi['admin']
// Ça doit être fait AVANT d'afficher quoi que ce soit (sinon la redirection ne marche pas).
$moi = page_connexion_requise();


// ---------- On compte les films et les avis de CE membre ----------

// Nombre de films "vu" et "a_voir" dans sa bibliothèque.
// GROUP BY Statut : MariaDB fait un total pour chaque statut.
$requete = db()->prepare('SELECT Statut, COUNT(*) AS total FROM Vue WHERE idUser = ? GROUP BY Statut');
$requete->execute([$moi['id']]);

$nbVus = 0;
$nbAVoir = 0;
foreach ($requete->fetchAll() as $ligne) {
    if ($ligne['Statut'] === 'vu') {
        $nbVus = $ligne['total'];
    } else {
        $nbAVoir = $ligne['total'];
    }
}

// Nombre d'avis écrits par ce membre
$requete = db()->prepare('SELECT COUNT(*) FROM Avis WHERE idUser = ?');
$requete->execute([$moi['id']]);
$nbAvis = $requete->fetchColumn();   // fetchColumn : récupère directement le nombre


// En-tête commun (Marylou)
include __DIR__ . '/../includes/header.php';
?>

<!-- En attendant que le header utilise des chemins qui commencent par "/",
     on recharge ici les styles communs, sinon ils ne marchent pas dans le dossier membre/.
     À retirer quand le header sera corrigé. -->
<link rel="stylesheet" href="/asset/css/style.css">
<link rel="stylesheet" href="/asset/css/header.css">
<!-- Le style des pages membre -->
<link rel="stylesheet" href="/asset/css/membre.css">
<link rel="stylesheet" href="/asset/css/footer.css">

<main class="page-membre">

    <!-- e() protège le nom avant de l'afficher (voir bootstrap.php) -->
    <h1>Bonjour <?= e($moi['nom']) ?> 👋</h1>
    <p class="sous-titre">Content de te revoir sur PopcornClub.</p>

    <!-- Les 3 chiffres -->
    <div class="chiffres">
        <div class="chiffre">
            <span class="nombre"><?= $nbVus ?></span>
            film(s) vu(s)
        </div>
        <div class="chiffre">
            <span class="nombre"><?= $nbAVoir ?></span>
            film(s) à voir
        </div>
        <div class="chiffre">
            <span class="nombre"><?= $nbAvis ?></span>
            avis écrit(s)
        </div>
    </div>

    <!-- Les raccourcis vers les autres pages -->
    <h2>Que veux-tu faire ?</h2>
    <div class="raccourcis">
        <a href="/membre/recherche.php">🔍 Ajouter un film</a>
        <a href="/membre/bibliotheque.php">📚 Ma bibliothèque</a>
        <a href="/membre/aVoir.php">🎬 Mes films à voir</a>
        <a href="/membre/statistiques.php">📊 Mes statistiques</a>
        <?php if ($moi['admin']): ?>
            <!-- Seulement pour les administrateurs -->
            <a href="/admin/homeAdmin.php">🛠️ Espace administrateur</a>
        <?php endif; ?>
    </div>

    <p class="deconnexion">
        <a href="/deconnexion.php">Se déconnecter</a>
    </p>

</main>
<footer>
    <?php
    include __DIR__ . '/../includes/footer.php';
    ?>
</footer>

