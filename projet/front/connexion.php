<?php
// VOUUUUS NE PASSERAIS PAAAAASSSS
if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    header('Location: /');
    exit;
}
?>


<section class="carte" aria-label="Connexion et inscription">

    <div class="onglets" role="tablist">
        <button class="onglet" role="tab" id="onglet-inscription"
                aria-controls="form-inscription" aria-selected="true">Créer un compte</button>
        <button class="onglet" role="tab" id="onglet-connexion"
                aria-controls="form-connexion" aria-selected="false">Se connecter</button>
    </div>

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
        <p class="message" aria-live="polite"></p>
    </form>

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
