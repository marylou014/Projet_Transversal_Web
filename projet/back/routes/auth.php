<?php

// T ki ?
if ($methode === 'GET') {
    json(['utilisateur' => utilisateur()]);
}

// va fonce et co toi
if ($methode === 'POST') {
    $donnees     = corps();
    $identifiant = trim($donnees['identifiant'] ?? '');
    $mdp         = $donnees['mdp'] ?? '';

    if ($identifiant === '' || $mdp === '') {
        json(['erreur' => 'Et ton nom ?'], 400);
    }

    $requete = db()->prepare(
        'SELECT idUser, nomUser, mdpUser, Admin, actif FROM Utilisateur WHERE nomUser = ? OR emailUser = ?'
    );
    $requete->execute([$identifiant, $identifiant]);
    $compte = $requete->fetch();   // false si aucun compte trouvé

    if ($compte === false || !password_verify($mdp, $compte['mdpUser'])) {
        json(['erreur' => 'Tu sais pas ecrire mdr'], 401);
    }

    if ($compte['actif'] == 0) {
        json(['erreur' => 'Bah alors plus de compte ?'], 403);
    }
    
    demarrer_session();
    session_regenerate_id(true);   // nouveau numéro de session à chaque connexion (sécurité)
    $_SESSION['utilisateur'] = [
        'id'    => (int) $compte['idUser'],
        'nom'   => $compte['nomUser'],
        'admin' => $compte['Admin'] == 1,
    ];

    json(['utilisateur' => $_SESSION['utilisateur']]);
}

json(['erreur' => 'Pas par la chef'], 405);
