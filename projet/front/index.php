<?php
// Page d'arrivée

require __DIR__ . '/../back/bootstrap.php';

// Déjà connecté
if (utilisateur() !== null) {
    header('Location: /membre/homeMem.php');
    exit;
}

?>


<link rel="stylesheet" href="/asset/css/accueil.css">
<link rel="stylesheet" href="/asset/css/style.css">


<script src="/asset/js/accueil.js" defer></script>

<!-- Rideau  -->
<div class="rideau" aria-hidden="true">
    <div class="rideau-gauche"></div>
    <div class="rideau-droite"></div>
</div>

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

    <!-- FORMULAIRES -->
    <section class="carte" aria-label="Connexion et inscription">

        <!-- Les onglets -->
        <div class="onglets" role="tablist">
            <button class="onglet" role="tab" id="onglet-inscription"
                    aria-controls="form-inscription" aria-selected="true">Créer un compte</button>
            <button class="onglet" role="tab" id="onglet-connexion"
                    aria-controls="form-connexion" aria-selected="false">Se connecter</button>
        </div>

        <!-- Inscription -->
        <!-- vérification -->
        <form class="formulaire" id="form-inscription" role="tabpanel"
              aria-labelledby="onglet-inscription" novalidate>
            <h2>Rejoins le club 🍿</h2>

            <label for="ins-nom">Nom d'utilisateur</label>
            <input id="ins-nom" name="nom" autocomplete="username" maxlength="50" required>

            <label for="ins-email">E-mail</label>
            <input id="ins-email" name="email" type="email" autocomplete="email" maxlength="255" required>

            <label for="ins-mdp">Mot de passe <small>(8 caractères minimum)</small></label>
            <input id="ins-mdp" name="mdp" type="password" autocomplete="new-password" minlength="8" required>

            <label for="ins-mdp2">Confirme le mot de passe</label>
            <input id="ins-mdp2" name="mdp2" type="password" autocomplete="new-password" required>

            <button type="submit" class="bouton">Créer mon compte</button>
            <!-- tpm pour js askip -->
            <p class="message" aria-live="polite"></p>
        </form>

        <!-- Connexion -->
        <form class="formulaire" id="form-connexion" role="tabpanel"
              aria-labelledby="onglet-connexion" novalidate hidden>
            <h2>Bon retour parmi nous 🎞️</h2>

            <label for="cnx-identifiant">Nom d'utilisateur ou e-mail</label>
            <input id="cnx-identifiant" name="identifiant" autocomplete="username" required>

            <label for="cnx-mdp">Mot de passe</label>
            <input id="cnx-mdp" name="mdp" type="password" autocomplete="current-password" required>

            <button type="submit" class="bouton">Se connecter</button>
            <p class="message" aria-live="polite"></p>
        </form>
    </section>

</main>

<?php
include __DIR__ . '/includes/footer.php';
?>
