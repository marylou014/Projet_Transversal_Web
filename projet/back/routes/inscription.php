<?php


if ($methode !== 'POST') {
    json(['erreur' => 'Toujours pas comme ca'], 405);
}

// TODO rajouter ?? '' pour eviter les espaces
$donnees = corps();
$nom   = trim($donnees['nom'] ?? '');
$email = trim($donnees['email'] ?? '');
$mdp   = $donnees['mdp'] ?? '';

//On check si tout va bueno
if ($nom === '' || strlen($nom) > 50) {
    json(['erreur' => 'Le nom d\'utilisateur doit faire entre 1 et 50 caractères.'], 400);
}
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {   // vérifie le format d'un e-mail
    json(['erreur' => 'Cette adresse e-mail n\'est pas valide.'], 400);
}
if (strlen($mdp) < 8) {
    json(['erreur' => 'Pas assez au moins 8 chef'], 400);
}


$requete = db()->prepare('SELECT COUNT(*) FROM Utilisateur WHERE nomUser = ? OR emailUser = ?');
$requete->execute([$nom, $email]);
if ($requete->fetchColumn() > 0) {
    json(['erreur' => 'Identifiant deja pris frerot'], 409);
}

// 0 c'est toujours un membre pour pas se faire niquer
$requete = db()->prepare(
    'INSERT INTO Utilisateur (nomUser, emailUser, mdpUser, Admin) VALUES (?, ?, ?, 0)'
);
$requete->execute([$nom, $email, password_hash($mdp, PASSWORD_DEFAULT)]);

json(['ok' => true], 201); 
