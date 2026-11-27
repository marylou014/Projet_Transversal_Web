<?php
// Page d'arrivée

require __DIR__ . '/../back/bootstrap.php';

// Déjà connecté
if (utilisateur() !== null) {
    header('Location: /membre/homeMem.php');
    exit;
}

// Rideau : seulement à la première arrivée sur le site.
// On pose un cookie "rideau_vu" sans date de fin : il disparaît quand on ferme le navigateur.
// Si le cookie existe déjà (erreur de connexion, retour sur la page...), pas de rideau.
$afficherRideau = !isset($_COOKIE['rideau_vu']);
if ($afficherRideau) {
    setcookie('rideau_vu', '1', ['path' => '/', 'samesite' => 'Lax']);
}

?>


<link rel="stylesheet" href="/asset/css/accueil.css">
<link rel="stylesheet" href="/asset/css/style.css">


<script src="/asset/js/accueil.js" defer></script>
<script src="/asset/js/connexion.js" defer></script>

<!-- Rideau (seulement la première fois, voir en haut du fichier) -->
<?php if ($afficherRideau): ?>
<div class="rideau" aria-hidden="true">
    <div class="rideau-gauche"></div>
    <div class="rideau-droite"></div>
</div>
<?php endif; ?>

<main class="scene">

    <section class="presentation" aria-labelledby="titre-principal">

        <div class="bloc-logo">
            <img src="/asset/img/logo-popcornclub.webp" alt="Logo PopcornClub" width="480" height="263">
        </div>

        <h1 id="titre-principal">
            Ajoute tes films,<br>entre cinéphiles.
        </h1>

        <p>
            PopcornClub, c'est ta bibliothèque de films personnelle&nbsp;: ce que tu as vu,
            ce que tu veux voir, tes notes et tes avis, au même endroit.
        </p>

        <!-- Les objectifs  -->
        <ul class="atouts">
            <li class="atout">
                <span aria-hidden="true">🎬</span>
                <strong>Ajouter</strong>
                <span>Les infos des films arrivent toutes seules depuis TMDB.</span>
            </li>
            <li class="atout">
                <span aria-hidden="true">🏷️</span>
                <strong>Classer</strong>
                <span>Vus, à voir, notes, avis et tags rien qu'à toi.</span>
            </li>
            <li class="atout">
                <span aria-hidden="true">💬</span>
                <strong>Partager</strong>
                <span>Lis les avis des autres membres et la note moyenne.</span>
            </li>
            <li class="atout">
                <span aria-hidden="true">📊</span>
                <strong>Progresser</strong>
                <span>Tes statistiques et des suggestions faites pour toi.</span>
            </li>
        </ul>
    </section>

    <!-- Les formulaires d'inscription et de connexion sont dans connexion.php -->
    <?php include __DIR__ . '/connexion.php'; ?>

</main>

<?php
include __DIR__ . '/includes/footer.php';
?>
