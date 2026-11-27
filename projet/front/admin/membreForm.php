<?php


require __DIR__ . '/../../back/bootstrap.php';
$moi = page_admin_requise();

$id = (int) ($_GET['id'] ?? 0);

$compte = ['nomUser' => '', 'emailUser' => '', 'Admin' => 0, 'actif' => 1];

if ($id !== 0) {
    $requete = db()->prepare('SELECT nomUser, emailUser, Admin, actif FROM Utilisateur WHERE idUser = ?');
    $requete->execute([$id]);
    $compte = $requete->fetch();

    if ($compte === false) {
        header('Location: /admin/membres.php');
        exit;
    }
}

$modification = $id !== 0;
$moiMeme = $id === $moi['id'];

include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="/asset/css/style.css">
<link rel="stylesheet" href="/asset/css/header.css">
<link rel="stylesheet" href="/asset/css/membre.css">
<link rel="stylesheet" href="/asset/css/admin.css">
<link rel="stylesheet" href="/asset/css/footer.css">
<script src="/asset/js/admin.js" defer></script>

<main class="page-membre">

    <p><a href="/admin/membres.php">← Les membres</a></p>
    <h1><?= $modification ? 'Modifier ' . e($compte['nomUser']) : 'Créer un compte' ?></h1>

    <form class="formulaire-admin" id="form-membre" data-id="<?= $id ?>" novalidate>

        <label for="nom">Nom d'utilisateur</label>
        <input id="nom" name="nom" maxlength="50" value="<?= e($compte['nomUser']) ?>" required>

        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" maxlength="255" value="<?= e($compte['emailUser']) ?>" required>

        <label for="mdp">
            Mot de passe
            <small><?= $modification ? '(laisser vide pour ne pas le changer)' : '(8 caractères minimum)' ?></small>
        </label>
        <input id="mdp" name="mdp" type="password" autocomplete="new-password">

        <?php if (!$moiMeme): ?>
            <label class="case">
                <input type="checkbox" name="admin" <?= $compte['Admin'] == 1 ? 'checked' : '' ?>>
                Administrateur
            </label>

            <label class="case">
                <input type="checkbox" name="actif" <?= $compte['actif'] == 1 ? 'checked' : '' ?>>
                Compte actif (décoché = ne peut plus se connecter)
            </label>
        <?php else: ?>
            <input type="hidden" name="admin" value="1">
            <input type="hidden" name="actif" value="1">
        <?php endif; ?>

        <button type="submit" class="bouton-lien"><?= $modification ? 'Enregistrer' : 'Créer le compte' ?></button>
        <p class="message" aria-live="polite"></p>
    </form>

</main>

<footer>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</footer>
