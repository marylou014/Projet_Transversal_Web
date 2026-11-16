<?php
// ===========================================================================
// /api/auth : connexion (Alexis)
// ---------------------------------------------------------------------------
//   POST /api/auth  → se connecter, appelée par le formulaire "Se connecter"
//                     JSON envoyé : {"identifiant": "nom ou e-mail", "mdp": "..."}
//   GET  /api/auth  → savoir qui est connecté (pratique pour le JavaScript)
//
// La déconnexion se fait avec la page /deconnexion.php (un simple lien).
// ===========================================================================

// ---------- GET : qui est connecté ? ----------
if ($methode === 'GET') {
    json(['utilisateur' => utilisateur()]);   // null si personne
}

// ---------- POST : se connecter ----------
if ($methode === 'POST') {
    $donnees     = corps();
    $identifiant = trim($donnees['identifiant'] ?? '');
    $mdp         = $donnees['mdp'] ?? '';

    if ($identifiant === '' || $mdp === '') {
        json(['erreur' => 'Indique ton identifiant et ton mot de passe.'], 400);
    }

    // On cherche le compte par son nom OU son e-mail
    $requete = db()->prepare(
        'SELECT idUser, nomUser, mdpUser, Admin FROM Utilisateur WHERE nomUser = ? OR emailUser = ?'
    );
    $requete->execute([$identifiant, $identifiant]);
    $compte = $requete->fetch();   // false si aucun compte trouvé

    // password_verify compare le mot de passe tapé avec le hash enregistré.
    // Même message dans les deux cas (compte inconnu OU mauvais mot de passe) :
    // on ne dit pas à un pirate quels comptes existent.
    if ($compte === false || !password_verify($mdp, $compte['mdpUser'])) {
        json(['erreur' => 'Identifiant ou mot de passe incorrect.'], 401);
    }

    // C'est bon : on mémorise la personne dans la session.
    demarrer_session();
    session_regenerate_id(true);   // nouveau numéro de session à chaque connexion (sécurité)
    $_SESSION['utilisateur'] = [
        'id'    => (int) $compte['idUser'],
        'nom'   => $compte['nomUser'],
        'admin' => $compte['Admin'] == 1,
    ];   // on ne met JAMAIS le mot de passe dans la session

    json(['utilisateur' => $_SESSION['utilisateur']]);
}

// ---------- Toute autre méthode ----------
json(['erreur' => 'méthode non autorisée'], 405);
