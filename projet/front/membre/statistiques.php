<?php
// ===========================================================================
// Mes statistiques (Alexis)
// ---------------------------------------------------------------------------
// La page est vide au départ : statistique.js demande les chiffres à
// /api/statistiques puis les écrit dans les cases ci-dessous.
// ===========================================================================

require __DIR__ . '/../../back/bootstrap.php';
$moi = page_connexion_requise();

include __DIR__ . '/../includes/header.php';
?>

<!-- Chemins en "/" en attendant que le header soit corrigé (voir homeMem.php) -->
<link rel="stylesheet" href="/asset/css/style.css">
<link rel="stylesheet" href="/asset/css/header.css">
<link rel="stylesheet" href="/asset/css/membre.css">
<link rel="stylesheet" href="/asset/css/footer.css">
<script src="/asset/js/statistique.js" defer></script>

<main class="page-membre">

    <h1>Mes statistiques 📊</h1>
    <p class="sous-titre">Calculées à partir de ta bibliothèque.</p>

    <!-- Les chiffres : chaque <span class="nombre"> a un id, le JS le remplit -->
    <div class="chiffres">
        <div class="chiffre"><span class="nombre" id="nbVus">…</span> film(s) vu(s)</div>
        <div class="chiffre"><span class="nombre" id="nbAVoir">…</span> film(s) à voir</div>
        <div class="chiffre"><span class="nombre" id="noteMoyenne">…</span> note moyenne / 5</div>
        <div class="chiffre"><span class="nombre" id="nbAvis">…</span> avis écrit(s)</div>
        <div class="chiffre"><span class="nombre" id="nbCoupsCoeur">…</span> coup(s) de cœur</div>
        <div class="chiffre"><span class="nombre" id="tempsTotal">…</span> devant l'écran</div>
    </div>

    <h2>Mes genres préférés</h2>
    <!-- Le JS ajoute ici une barre par genre -->
    <div id="genres"></div>

    <p class="message" id="message-stats"></p>

</main>

<footer>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</footer>
