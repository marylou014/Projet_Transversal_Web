<?php
// ===========================================================================
// POST /api/inscription : créer un compte (Alexis)
// ---------------------------------------------------------------------------
// Appelée par le formulaire "Créer un compte" de la page d'arrivée (accueil.js).
// Le JS envoie du JSON : {"nom": "...", "email": "...", "mdp": "..."}
// Réponses :
//   201 + {"ok": true}             → compte créé
//   400 + {"erreur": "..."}        → un champ n'est pas bon
//   409 + {"erreur": "..."}        → nom ou e-mail déjà utilisé
// ===========================================================================

// Cette route n'accepte que POST (envoi de données)
if ($methode !== 'POST') {
    json(['erreur' => 'méthode non autorisée'], 405);
}

// 1. On récupère les données envoyées par le formulaire
$donnees = corps();
$nom   = trim($donnees['nom'] ?? '');     // trim : enlève les espaces autour
$email = trim($donnees['email'] ?? '');
$mdp   = $donnees['mdp'] ?? '';

// 2. On REFAIT les vérifications du JavaScript.
//    Le JS peut être contourné (n'importe qui peut envoyer une requête à la main),
//    donc c'est ici, côté serveur, que la vérification compte vraiment.
if ($nom === '' || strlen($nom) > 50) {
    json(['erreur' => 'Le nom d\'utilisateur doit faire entre 1 et 50 caractères.'], 400);
}
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {   // vérifie le format d'un e-mail
    json(['erreur' => 'Cette adresse e-mail n\'est pas valide.'], 400);
}
if (strlen($mdp) < 8) {
    json(['erreur' => 'Le mot de passe doit faire au moins 8 caractères.'], 400);
}

// 3. Le nom ou l'e-mail sont-ils déjà pris ?
//    Requête PRÉPARÉE : les "?" sont remplis par PDO de façon sûre (pas d'injection SQL).
$requete = db()->prepare('SELECT COUNT(*) FROM Utilisateur WHERE nomUser = ? OR emailUser = ?');
$requete->execute([$nom, $email]);
if ($requete->fetchColumn() > 0) {
    json(['erreur' => 'Ce nom d\'utilisateur ou cet e-mail est déjà utilisé.'], 409);
}

// 4. On enregistre le compte.
//    password_hash transforme le mot de passe en "hash" : une empreinte illisible.
//    On ne stocke JAMAIS le mot de passe en clair dans la base.
//    Admin = 0 : un compte créé par le formulaire est toujours un simple membre.
$requete = db()->prepare(
    'INSERT INTO Utilisateur (nomUser, emailUser, mdpUser, Admin) VALUES (?, ?, ?, 0)'
);
$requete->execute([$nom, $email, password_hash($mdp, PASSWORD_DEFAULT)]);

json(['ok' => true], 201);   // 201 = "créé"
