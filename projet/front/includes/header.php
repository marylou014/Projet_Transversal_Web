<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>PopcornClub</title>
    <link rel="stylesheet" href="asset/css/style.css">
    <link rel="stylesheet" href="asset/css/header.css">
</head>
<body>

<!-- HEADER / NAVBAR -->
<header>
    <div>
        <div class="header">

            <!-- LOGO & NOM -->
            <div class="zone-logo">
                <a href="index.php">
                    <!-- logo -->
                    <div class="logo-header">
                        <img src="../asset/img/logo_simple_noBG.png" alt="logo">
                    </div>
                </a>
            </div>

            <!-- NAVIGATION PRINCIPALE -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="/membre/homeMem.php">Accueil</a>
                <a href="/membre/bibliotheque.php">Bibliothèque</a>
                <a href="/membre/aVoir.php">À voir </a>
                <a href="/membre/statistiques.php">Statistiques</a>
            </nav>

            <!--BOUTON AJOUTER + profil -->
            <div class="action">
                <!-- "Bouton" pour ajouter un film renvoie à la page  -->
                <div class="ajout">
                    <a href="/membre/recherche.php">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajouter un film
                    </a>
                </div>

                <!-- Profil Membre -->
                <div id="icone-profil">
                    <div>
                        👤
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>