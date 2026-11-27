-- ===========================================================================
-- PopcornClub : création des tables + jeu de test
-- ---------------------------------------------------------------------------
-- Base : MariaDB 10.11 (VM du projet), base "projet_transversal_web".
-- Les tables suivent le MCD de la page Notion (Marylou).
-- En plus du MCD : des contraintes qui font respecter les règles de gestion
-- directement par la base (repérées par "RÈGLE n°" dans les commentaires).
--
-- ⚠️ ATTENTION : ce fichier SUPPRIME puis RECRÉE toutes les tables.
--    Le relancer efface TOUTES les données (comptes, films, avis...).
--    À utiliser pour installer ou remettre la base à zéro, pas pour ajouter des données.
--
-- Les noms de tables sont sensibles à la casse sur Linux :
-- écrire "Utilisateur" et pas "utilisateur" dans les requêtes PHP.
-- ===========================================================================

SET NAMES utf8mb4;

-- On supprime dans l'ordre inverse des dépendances (les tables "enfants"
-- d'abord), sinon les clés étrangères bloquent la suppression.
DROP TABLE IF EXISTS Film_tag;
DROP TABLE IF EXISTS Avis;
DROP TABLE IF EXISTS Tags;
DROP TABLE IF EXISTS Vue;
DROP TABLE IF EXISTS Film;
DROP TABLE IF EXISTS Utilisateur;


-- ---------------------------------------------------------------------------
-- Utilisateur : les comptes (membres et administrateurs)
-- ---------------------------------------------------------------------------
CREATE TABLE Utilisateur (
    idUser    INT          NOT NULL AUTO_INCREMENT,  -- numéro donné automatiquement (1, 2, 3...)
    nomUser   VARCHAR(50)  NOT NULL,
    emailUser VARCHAR(255) NOT NULL,
    mdpUser   VARCHAR(255) NOT NULL,                 -- HASH du mot de passe (password_hash), jamais en clair
    Admin     TINYINT(1)   NOT NULL DEFAULT 0,       -- 1 = administrateur, 0 = membre (RÈGLE 1 : un seul rôle)
    actif     TINYINT(1)   NOT NULL DEFAULT 1,       -- 0 = compte désactivé par un admin (ne peut plus se connecter)
    PRIMARY KEY (idUser),
    UNIQUE KEY uq_nomUser   (nomUser),               -- on se connecte avec le nom ou l'email :
    UNIQUE KEY uq_emailUser (emailUser)              -- ils doivent donc être uniques
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------------
-- Film : les infos d'un film, recopiées depuis l'API TMDB (RÈGLE 5 : non modifiables)
-- Un film n'est enregistré qu'une fois, même si 10 membres l'ont vu.
-- ---------------------------------------------------------------------------
CREATE TABLE Film (
    idFilm     INT          NOT NULL AUTO_INCREMENT,
    titreFilm  VARCHAR(255) NOT NULL,   -- TMDB : title
    realFilm   VARCHAR(150) NOT NULL,   -- TMDB : credits.crew (job = "Director")
    acteurs    VARCHAR(255) NULL,       -- TMDB : credits.cast
    genre      VARCHAR(45)  NULL,       -- TMDB : genres[].name
    temps      INT          NULL,       -- TMDB : runtime (en minutes)
    resume     LONGTEXT     NULL,       -- TMDB : overview
    afficheURL VARCHAR(255) NOT NULL,   -- TMDB : poster_path (+ URL de base des images)
    anneeFilm  DATE         NULL,       -- TMDB : release_date
    PRIMARY KEY (idFilm)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------------
-- Vue : le lien "tel membre a tel film dans sa bibliothèque"
-- Statut = 'vu' ou 'a_voir'. C'est le cœur de l'appli : avis et tags s'y rattachent.
-- ---------------------------------------------------------------------------
CREATE TABLE Vue (
    idVue  INT         NOT NULL AUTO_INCREMENT,
    idUser INT         NOT NULL,
    idFilm INT         NOT NULL,
    Statut VARCHAR(45) NOT NULL,
    `date` DATE        NOT NULL,            -- date d'ajout / de visionnage (entre ` ` car "date" est un mot SQL)
    PRIMARY KEY (idVue),
    -- RÈGLES 6 et 7 : un membre n'a qu'UNE ligne par film,
    -- donc un film est soit "vu" soit "à voir", jamais les deux, et jamais en double.
    UNIQUE KEY uq_vue_user_film (idUser, idFilm),
    CONSTRAINT ck_vue_statut CHECK (Statut IN ('vu', 'a_voir')),
    -- Clés étrangères : idUser doit exister dans Utilisateur, idFilm dans Film.
    -- ON DELETE CASCADE : si on supprime le compte, ses lignes Vue partent avec.
    CONSTRAINT fk_vue_user FOREIGN KEY (idUser) REFERENCES Utilisateur (idUser) ON DELETE CASCADE,
    -- RESTRICT : impossible de supprimer un film encore présent dans une bibliothèque.
    CONSTRAINT fk_vue_film FOREIGN KEY (idFilm) REFERENCES Film (idFilm) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------------
-- Avis : note + commentaire d'un membre sur un film qu'il a dans sa bibliothèque
-- ---------------------------------------------------------------------------
CREATE TABLE Avis (
    idAvis      INT        NOT NULL AUTO_INCREMENT,
    idVue       INT        NOT NULL,
    note        INT        NOT NULL,
    coupCoeur   TINYINT(1) NOT NULL DEFAULT 0,   -- 1 = coup de cœur
    Commentaire LONGTEXT   NULL,
    idUser      INT        NULL,                 -- présent dans le MCD (déjà connu via la Vue)
    PRIMARY KEY (idAvis),
    UNIQUE KEY uq_avis_vue (idVue),              -- RÈGLE 8 : un seul avis par film et par membre
    CONSTRAINT ck_avis_note CHECK (note BETWEEN 0 AND 5),   -- RÈGLE 9 : échelle 0 à 5 (à confirmer)
    -- Si le film est retiré de la bibliothèque (Vue supprimée), l'avis part avec.
    CONSTRAINT fk_avis_vue  FOREIGN KEY (idVue)  REFERENCES Vue (idVue)            ON DELETE CASCADE,
    CONSTRAINT fk_avis_user FOREIGN KEY (idUser) REFERENCES Utilisateur (idUser)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- NB : "on ne note qu'un film VU" (RÈGLE 8) ne peut pas être vérifié simplement
-- par la base : c'est la route avis.php qui le contrôle avant d'insérer.


-- ---------------------------------------------------------------------------
-- Tags : les étiquettes créées par chaque membre ("à revoir", "Noël"...)
-- ---------------------------------------------------------------------------
CREATE TABLE Tags (
    idTags  INT          NOT NULL AUTO_INCREMENT,
    idUser  INT          NOT NULL,                -- RÈGLE 4 : chaque membre a ses propres tags
    nomTags VARCHAR(255) NOT NULL,
    PRIMARY KEY (idTags),
    CONSTRAINT fk_tags_user FOREIGN KEY (idUser) REFERENCES Utilisateur (idUser) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------------
-- Film_tag : table de liaison "tel tag est posé sur tel film de la bibliothèque"
-- (un film peut avoir plusieurs tags, un tag peut être sur plusieurs films)
-- ---------------------------------------------------------------------------
CREATE TABLE Film_tag (
    idVue  INT NOT NULL,
    idTags INT NOT NULL,
    PRIMARY KEY (idVue, idTags),   -- clé composée : le même tag ne peut pas être posé 2 fois sur le même film
    CONSTRAINT fk_ft_vue  FOREIGN KEY (idVue)  REFERENCES Vue (idVue)   ON DELETE CASCADE,
    CONSTRAINT fk_ft_tags FOREIGN KEY (idTags) REFERENCES Tags (idTags) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ===========================================================================
-- JEU DE TEST
-- ---------------------------------------------------------------------------
-- Comptes de test (mots de passe de TEST uniquement, à changer en production) :
--   admin   / admin@popcornclub.test   mot de passe : Admin123!   (administrateur)
--   alexis  / alexis@popcornclub.test  mot de passe : Membre123!  (membre)
--   marylou / marylou@popcornclub.test mot de passe : Membre123!  (membre)
-- Les mots de passe sont stockés sous forme de hash, générés avec :
--   php -r 'echo password_hash("Admin123!", PASSWORD_DEFAULT), "\n";'
--
-- On donne les id à la main (1, 2, 3...) pour pouvoir les réutiliser
-- dans les INSERT suivants.
-- ===========================================================================

INSERT INTO Utilisateur (idUser, nomUser, emailUser, mdpUser, Admin) VALUES
(1, 'admin',   'admin@popcornclub.test',   '$2y$12$9DonRMC.3Rt0yIN5okP9X.hbgD2ZWXGjQ6cYSKnJKZoCE.tB/nd.6', 1),
(2, 'alexis',  'alexis@popcornclub.test',  '$2y$12$SAxpSTf.u2W9W6xNPEfAzu7N6vFBB5RlJPFKVxcKV98eHVAIBgtiy', 0),
(3, 'marylou', 'marylou@popcornclub.test', '$2y$12$SAxpSTf.u2W9W6xNPEfAzu7N6vFBB5RlJPFKVxcKV98eHVAIBgtiy', 0);

-- Films de test. Dans l'appli, ces lignes seront créées automatiquement
-- depuis l'API TMDB ; ici l'affiche et le résumé sont laissés vides.
INSERT INTO Film (idFilm, titreFilm, realFilm, genre, temps, afficheURL, anneeFilm) VALUES
(1, 'Inception',                                 'Christopher Nolan',  'Science-Fiction', 148, '', '2010-07-16'),
(2, 'Le Fabuleux Destin d''Amélie Poulain',      'Jean-Pierre Jeunet', 'Comédie',         122, '', '2001-04-25'),
(3, 'Parasite',                                  'Bong Joon-ho',       'Thriller',        132, '', '2019-05-30');
-- ('' : deux apostrophes = une apostrophe dans le texte, sinon SQL croit que la chaîne s'arrête)

-- Bibliothèques : alexis a vu Inception et Parasite, veut voir Amélie ;
-- marylou a vu Inception.
INSERT INTO Vue (idVue, idUser, idFilm, Statut, `date`) VALUES
(1, 2, 1, 'vu',     '2026-11-01'),
(2, 2, 3, 'vu',     '2026-11-08'),
(3, 2, 2, 'a_voir', '2026-11-10'),
(4, 3, 1, 'vu',     '2026-11-05');

-- Avis (uniquement sur des films "vu") : 2 avis sur Inception → moyenne = 4,5
INSERT INTO Avis (idAvis, idVue, note, coupCoeur, Commentaire, idUser) VALUES
(1, 1, 5, 1, 'Test : avis d''alexis sur Inception.',  2),
(2, 2, 4, 0, 'Test : avis d''alexis sur Parasite.',   2),
(3, 4, 4, 0, 'Test : avis de marylou sur Inception.', 3);

-- Tags d'alexis, posés sur ses films
INSERT INTO Tags (idTags, idUser, nomTags) VALUES
(1, 2, 'À revoir'),
(2, 2, 'Entre amis');

INSERT INTO Film_tag (idVue, idTags) VALUES
(1, 1),   -- Inception   → "À revoir"
(1, 2),   -- Inception   → "Entre amis"
(2, 2);   -- Parasite    → "Entre amis"
