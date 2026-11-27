<?php

$moi = api_admin_requise();
$id  = (int) ($params[0] ?? 0);   // verif les id


//Véreif user
function lire_compte(array $donnees, int $idCompte): array {
    $nom   = trim($donnees['nom'] ?? '');
    $email = trim($donnees['email'] ?? '');

    if ($nom === '' || strlen($nom) > 50) {
        json(['erreur' => 'Le nom d\'utilisateur doit faire entre 1 et 50 caractères.'], 400);
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        json(['erreur' => 'Cette adresse e-mail n\'est pas valide.'], 400);
    }

    $requete = db()->prepare(
        'SELECT COUNT(*) FROM Utilisateur WHERE (nomUser = ? OR emailUser = ?) AND idUser <> ?'
    );
    $requete->execute([$nom, $email, $idCompte]);
    if ($requete->fetchColumn() > 0) {
        json(['erreur' => 'Ce nom d\'utilisateur ou cet e-mail est déjà utilisé.'], 409);
    }

    return [
        'nom'   => $nom,
        'email' => $email,
        'admin' => !empty($donnees['admin']) ? 1 : 0,
        'mdp'   => $donnees['mdp'] ?? '',
    ];
}

// Stop
function compte_existe(int $idCompte): void {
    $requete = db()->prepare('SELECT COUNT(*) FROM Utilisateur WHERE idUser = ?');
    $requete->execute([$idCompte]);
    if ($requete->fetchColumn() == 0) {
        json(['erreur' => 'Ce compte n\'existe pas.'], 404);
    }
}


if ($methode === 'GET') {
    // On ne renvoie rien
    $colonnes = 'idUser, nomUser, emailUser, Admin, actif';

    if ($id !== 0) {
        compte_existe($id);
        $requete = db()->prepare("SELECT $colonnes FROM Utilisateur WHERE idUser = ?");
        $requete->execute([$id]);
        json($requete->fetch());
    }

    json(db()->query("SELECT $colonnes FROM Utilisateur ORDER BY nomUser")->fetchAll());
}


// créer compte
if ($methode === 'POST') {
    $compte = lire_compte(corps(), 0);

    if (strlen($compte['mdp']) < 8) {
        json(['erreur' => 'Un peu plus long stp au moins 8.'], 400);
    }

    $requete = db()->prepare(
        'INSERT INTO Utilisateur (nomUser, emailUser, mdpUser, Admin, actif) VALUES (?, ?, ?, ?, 1)'
    );
    $requete->execute([
        $compte['nom'], $compte['email'], password_hash($compte['mdp'], PASSWORD_DEFAULT), $compte['admin'],
    ]);

    json(['idUser' => (int) db()->lastInsertId()], 201);
}


// Modif compte
if ($methode === 'PUT') {
    compte_existe($id);
    $donnees = corps();
    $compte  = lire_compte($donnees, $id);
    $actif   = ($donnees['actif'] ?? true) ? 1 : 0;

    // Mdp
    if ($compte['mdp'] !== '' && strlen($compte['mdp']) < 8) {
        json(['erreur' => 'Chef il faut 8 caractères'], 400);
    }

    if ($id === $moi['id'] && ($compte['admin'] === 0 || $actif === 0)) {
        json(['erreur' => 'Tu ne peux pas idiot'], 400);
    }

    $requete = db()->prepare(
        'UPDATE Utilisateur SET nomUser = ?, emailUser = ?, Admin = ?, actif = ? WHERE idUser = ?'
    );
    $requete->execute([$compte['nom'], $compte['email'], $compte['admin'], $actif, $id]);

    // Nv mdp
    if ($compte['mdp'] !== '') {
        $requete = db()->prepare('UPDATE Utilisateur SET mdpUser = ? WHERE idUser = ?');
        $requete->execute([password_hash($compte['mdp'], PASSWORD_DEFAULT), $id]);
    }

    json(['ok' => true]);
}


// Supp compte
if ($methode === 'DELETE') {
    compte_existe($id);

    if ($id === $moi['id']) {
        json(['erreur' => 'Tu ne peux pas supprimer ton propre compte.'], 400);
    }

    $requete = db()->prepare('DELETE FROM Utilisateur WHERE idUser = ?');
    $requete->execute([$id]);

    json(['ok' => true]);
}

json(['erreur' => 'méthode non autorisée'], 405);
