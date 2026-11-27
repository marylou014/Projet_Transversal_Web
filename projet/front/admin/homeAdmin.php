<?php

require __DIR__ . '/../../back/bootstrap.php';
$moi = page_admin_requise();


// Les chiffres
$comptes = db()->query(
    'SELECT COUNT(*) AS total, SUM(actif = 0) AS desactives FROM Utilisateur'
)->fetch();
$nbFilms = db()->query('SELECT COUNT(*) FROM Film')->fetchColumn();
$nbAvis  = db()->query('SELECT COUNT(*) FROM Avis')->fetchColumn();


include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="/asset/css/style.css">
<link rel="stylesheet" href="/asset/css/header.css">
<link rel="stylesheet" href="/asset/css/membre.css">
<link rel="stylesheet" href="/asset/css/footer.css">

<main class="page-membre">

    <h1>Espace administrateur 🛠️</h1>
    <p class="sous-titre">Connecté en tant que <?= e($moi['nom']) ?>.</p>

    <div class="chiffres">
        <div class="chiffre">
            <span class="nombre"><?= $comptes['total'] ?></span>
            compte(s) dont <?= (int) $comptes['desactives'] ?> désactivé(s)
        </div>
        <div class="chiffre">
            <span class="nombre"><?= $nbFilms ?></span>
            film(s) enregistré(s)
        </div>
        <div class="chiffre">
            <span class="nombre"><?= $nbAvis ?></span>
            avis écrit(s)
        </div>
    </div>

    <h2>Que veux-tu faire ?</h2>
    <div class="raccourcis">
        <a href="/admin/membres.php">👥 Gérer les membres</a>
        <a href="/admin/membreForm.php">➕ Créer un compte</a>
        <a href="/membre/homeMem.php">🍿 Aller à mon espace membre</a>
    </div>

    <p class="deconnexion">
        <a href="/deconnexion.php">Se déconnecter</a>
    </p>

</main>

<footer>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</footer>
