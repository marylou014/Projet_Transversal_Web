<?php


require __DIR__ . '/../../back/bootstrap.php';
$moi = page_admin_requise();

$comptes = db()->query(
    'SELECT idUser, nomUser, emailUser, Admin, actif FROM Utilisateur ORDER BY nomUser'
)->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="/asset/css/style.css">
<link rel="stylesheet" href="/asset/css/header.css">
<link rel="stylesheet" href="/asset/css/membre.css">
<link rel="stylesheet" href="/asset/css/admin.css">
<link rel="stylesheet" href="/asset/css/footer.css">
<script src="/asset/js/admin.js" defer></script>

<main class="page-membre">

    <p><a href="/admin/homeAdmin.php">← Espace administrateur</a></p>
    <h1>Les membres</h1>

    <p><a class="bouton-lien" href="/admin/membreForm.php">➕ Créer un compte</a></p>

    <p class="message" id="message-membres"></p>

    <table class="tableau">
        <thead>
            <tr>
                <th>Nom</th>
                <th>E-mail</th>
                <th>Rôle</th>
                <th>État</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($comptes as $compte): ?>
            <!-- Les data-... servent à admin.js : il y lit les infos du compte -->
            <tr data-id="<?= $compte['idUser'] ?>"
                data-nom="<?= e($compte['nomUser']) ?>"
                data-email="<?= e($compte['emailUser']) ?>"
                data-admin="<?= $compte['Admin'] ?>"
                data-actif="<?= $compte['actif'] ?>">

                <td><?= e($compte['nomUser']) ?></td>
                <td><?= e($compte['emailUser']) ?></td>
                <td><?= $compte['Admin'] == 1 ? 'Admin' : 'Membre' ?></td>
                <td><?= $compte['actif'] == 1 ? '✅ Actif' : '⛔ Désactivé' ?></td>
                <td>
                    <a href="/admin/membreForm.php?id=<?= $compte['idUser'] ?>">Modifier</a>

                    <?php if ($compte['idUser'] != $moi['id']): ?>
                        <!-- Pas de bouton sur son propre compte : un admin ne se bloque pas lui-même -->
                        <button class="petit-bouton" data-action="activer">
                            <?= $compte['actif'] == 1 ? 'Désactiver' : 'Réactiver' ?>
                        </button>
                        <button class="petit-bouton danger" data-action="supprimer">Supprimer</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</main>

<footer>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</footer>
