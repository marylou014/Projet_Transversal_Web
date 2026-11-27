<?php


// T'es co ?
$moi = api_connexion_requise();

$id = (int) ($params[0] ?? 0);

function lire_avis(array $donnees): array {
    $note = $donnees['note'] ?? null;
    if (!is_int($note) || $note < 0 || $note > 5) {
        json(['erreur' => 'On a dis 1 a 5 frerot'], 400);
    }
    $commentaire = trim($donnees['commentaire'] ?? '');
    return [
        'note'        => $note,
        'coupCoeur'   => !empty($donnees['coupCoeur']) ? 1 : 0,   // true → 1, false ou absent → 0
        'commentaire' => $commentaire === '' ? null : $commentaire,   // vide → NULL dans la base
    ];
}

// Cherche un avis et check
function mon_avis(int $idAvis, int $idUser): void {
    $requete = db()->prepare('SELECT idUser FROM Avis WHERE idAvis = ?');
    $requete->execute([$idAvis]);
    $auteur = $requete->fetchColumn();

    if ($auteur === false) {
        json(['erreur' => 'Cet avis n\'existe pas.'], 404);
    }
    if ((int) $auteur !== $idUser) {
        json(['erreur' => 'Trooooop taaaard'], 403);
    }
}


// Avis film
if ($methode === 'GET') {
    if ($id === 0) {
        json(['erreur' => 'Indique le film : /api/avis/<idFilm>'], 400);
    }

    $requete = db()->prepare(
        'SELECT Avis.idAvis, Avis.note, Avis.coupCoeur, Avis.Commentaire, Utilisateur.nomUser,
                Avis.idUser = ? AS estMoi
         FROM Avis
         JOIN Vue ON Vue.idVue = Avis.idVue
         JOIN Utilisateur ON Utilisateur.idUser = Avis.idUser
         WHERE Vue.idFilm = ?
         ORDER BY Avis.idAvis DESC'
    );
    $requete->execute([$moi['id'], $id]);
    $avis = $requete->fetchAll();

    // Moyenne
    $requete = db()->prepare(
        'SELECT ROUND(AVG(Avis.note), 1) FROM Avis JOIN Vue ON Vue.idVue = Avis.idVue WHERE Vue.idFilm = ?'
    );
    $requete->execute([$id]);
    $moyenne = $requete->fetchColumn();   // NULL si personne n'a noté le film

    json([
        'moyenne' => $moyenne === null ? null : (float) $moyenne,
        'avis'    => $avis,
    ]);
}


// Ecrire avis
if ($methode === 'POST') {
    $donnees = corps();
    $idVue = (int) ($donnees['idVue'] ?? 0);
    $avis = lire_avis($donnees);

    // Doit etre dans la biblio
    $requete = db()->prepare('SELECT Statut FROM Vue WHERE idVue = ? AND idUser = ?');
    $requete->execute([$idVue, $moi['id']]);
    $statut = $requete->fetchColumn();

    if ($statut === false) {
        json(['erreur' => 'Ce film n\'est pas dans ta bibliothèque.'], 404);
    }
    if ($statut !== 'vu') {
        json(['erreur' => 'Tu ne peux noter qu\'un film que tu as vu.'], 400);
    }

    // Un seul avis
    $requete = db()->prepare('SELECT COUNT(*) FROM Avis WHERE idVue = ?');
    $requete->execute([$idVue]);
    if ($requete->fetchColumn() > 0) {
        json(['erreur' => 'Tu as deja raconter ta vie dessus'], 409);
    }

    $requete = db()->prepare(
        'INSERT INTO Avis (idVue, note, coupCoeur, Commentaire, idUser) VALUES (?, ?, ?, ?, ?)'
    );
    $requete->execute([$idVue, $avis['note'], $avis['coupCoeur'], $avis['commentaire'], $moi['id']]);
    
    json(['idAvis' => (int) db()->lastInsertId()], 201);
}


// Modif avis
if ($methode === 'PUT') {
    mon_avis($id, $moi['id']);
    $avis = lire_avis(corps());

    $requete = db()->prepare('UPDATE Avis SET note = ?, coupCoeur = ?, Commentaire = ? WHERE idAvis = ?');
    $requete->execute([$avis['note'], $avis['coupCoeur'], $avis['commentaire'], $id]);

    json(['ok' => true]);
}


// Supp avis
if ($methode === 'DELETE') {
    mon_avis($id, $moi['id']);

    $requete = db()->prepare('DELETE FROM Avis WHERE idAvis = ?');
    $requete->execute([$id]);

    json(['ok' => true]);
}

// Curieu
json(['erreur' => 'Toujours pas par la'], 405);
