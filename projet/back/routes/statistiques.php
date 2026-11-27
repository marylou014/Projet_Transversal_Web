<?php

$moi = api_connexion_requise();

if ($methode !== 'GET') {
    json(['erreur' => 'Toujours pas ici'], 405);
}


// Films vus et à voir
$requete = db()->prepare(
    "SELECT SUM(Statut = 'vu') AS nbVus, SUM(Statut = 'a_voir') AS nbAVoir FROM Vue WHERE idUser = ?"
);
$requete->execute([$moi['id']]);
$films = $requete->fetch();


// Avis / coups de cœur / moyenne
$requete = db()->prepare(
    'SELECT COUNT(*) AS nbAvis, SUM(coupCoeur) AS nbCoupsCoeur, ROUND(AVG(note), 1) AS noteMoyenne
     FROM Avis WHERE idUser = ?'
);
$requete->execute([$moi['id']]);
$avis = $requete->fetch();


// Temps passé
$requete = db()->prepare(
    "SELECT SUM(Film.temps) FROM Vue JOIN Film ON Film.idFilm = Vue.idFilm
     WHERE Vue.idUser = ? AND Vue.Statut = 'vu'"
);
$requete->execute([$moi['id']]);
$tempsTotal = $requete->fetchColumn();


//  Genres préf
$requete = db()->prepare(
    "SELECT Film.genre AS genre, COUNT(*) AS total
     FROM Vue JOIN Film ON Film.idFilm = Vue.idFilm
     WHERE Vue.idUser = ? AND Vue.Statut = 'vu' AND Film.genre IS NOT NULL
     GROUP BY Film.genre
     ORDER BY total DESC"
);
$requete->execute([$moi['id']]);
$genres = $requete->fetchAll();

// Si vide alors 0
json([
    'nbVus'        => (int) $films['nbVus'],
    'nbAVoir'      => (int) $films['nbAVoir'],
    'nbAvis'       => (int) $avis['nbAvis'],
    'nbCoupsCoeur' => (int) $avis['nbCoupsCoeur'],
    'noteMoyenne'  => $avis['noteMoyenne'] === null ? null : (float) $avis['noteMoyenne'],
    'tempsTotal'   => (int) $tempsTotal,
    'genres'       => $genres,
]);
