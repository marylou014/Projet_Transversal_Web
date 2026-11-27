// ===========================================================================
// admin.js – pages de l'espace administrateur (Alexis)
// ---------------------------------------------------------------------------
// Utilisé par 2 pages :
//   1. membres.php    : boutons "Désactiver / Réactiver" et "Supprimer"
//   2. membreForm.php : envoi du formulaire (créer ou modifier un compte)
// Tout passe par l'API /api/gesMembres, sans recharger la page en cas d'erreur.
// ===========================================================================


// Envoie une requête à /api/gesMembres et renvoie { ok, donnees }
// methode : 'POST', 'PUT' ou 'DELETE'  —  id : le compte (0 pour en créer un)
async function appelerApi(methode, id, donnees) {
    const adresse = id ? '/api/gesMembres/' + id : '/api/gesMembres';
    const reponse = await fetch(adresse, {
        method: methode,
        headers: { 'Content-Type': 'application/json' },
        body: donnees ? JSON.stringify(donnees) : undefined,   // DELETE : pas de données
    });
    try {
        return { ok: reponse.ok, donnees: await reponse.json() };
    } catch {
        return { ok: false, donnees: { erreur: 'Le serveur a répondu quelque chose d\'inattendu.' } };
    }
}

// Écrit un message (type = 'erreur' ou 'succes')
function afficherMessage(zone, texte, type) {
    zone.textContent = texte;
    zone.className = 'message ' + type;
}


// ---------------------------------------------------------------------------
// 1. La liste des membres
// ---------------------------------------------------------------------------
const tableau = document.querySelector('.tableau');

if (tableau) {   // on est bien sur membres.php
    const message = document.getElementById('message-membres');

    // Un seul écouteur sur tout le tableau : on regarde quel bouton a été cliqué.
    // (plus simple que d'en mettre un sur chaque bouton)
    tableau.addEventListener('click', async (evenement) => {
        const bouton = evenement.target.closest('button');   // closest : le bouton cliqué (ou null)
        if (!bouton) {
            return;
        }
        const ligne = bouton.closest('tr');
        const compte = ligne.dataset;   // dataset : les attributs data-... de la ligne

        let resultat;

        if (bouton.dataset.action === 'supprimer') {
            // confirm : fenêtre "OK / Annuler" du navigateur
            if (!confirm('Supprimer le compte "' + compte.nom + '" ? Sa bibliothèque et ses avis seront perdus.')) {
                return;
            }
            resultat = await appelerApi('DELETE', compte.id);
        }

        if (bouton.dataset.action === 'activer') {
            // L'API attend le compte complet : on renvoie les mêmes infos, seul "actif" change
            resultat = await appelerApi('PUT', compte.id, {
                nom: compte.nom,
                email: compte.email,
                admin: compte.admin === '1',
                actif: compte.actif !== '1',   // actif → désactivé, désactivé → actif
                mdp: '',
            });
        }

        if (resultat.ok) {
            window.location.reload();   // on recharge pour voir le tableau à jour
        } else {
            afficherMessage(message, resultat.donnees.erreur ?? 'Action impossible.', 'erreur');
        }
    });
}


// ---------------------------------------------------------------------------
// 2. Le formulaire créer / modifier
// ---------------------------------------------------------------------------
const formMembre = document.getElementById('form-membre');

if (formMembre) {   // on est bien sur membreForm.php
    const message = formMembre.querySelector('.message');
    const id = Number(formMembre.dataset.id);   // 0 = création

    formMembre.addEventListener('submit', async (evenement) => {
        evenement.preventDefault();   // pas de rechargement de la page
        const champs = formMembre.elements;

        // Une case à cocher a .checked ; un champ caché (son propre compte) a .value = "1"
        const coche = (champ) => champ.type === 'checkbox' ? champ.checked : champ.value === '1';

        const donnees = {
            nom: champs.nom.value.trim(),
            email: champs.email.value.trim(),
            mdp: champs.mdp.value,
            admin: coche(champs.admin),
            actif: coche(champs.actif),
        };

        // Petite vérification avant d'envoyer (l'API refait tout de toute façon)
        if (donnees.nom === '' || donnees.email === '') {
            return afficherMessage(message, 'Le nom et l\'e-mail sont obligatoires.', 'erreur');
        }
        if (id === 0 && donnees.mdp.length < 8) {
            return afficherMessage(message, 'Le mot de passe doit faire au moins 8 caractères.', 'erreur');
        }

        try {
            const resultat = await appelerApi(id === 0 ? 'POST' : 'PUT', id, donnees);
            if (resultat.ok) {
                window.location.href = '/admin/membres.php';   // c'est bon : retour à la liste
            } else {
                afficherMessage(message, resultat.donnees.erreur ?? 'Enregistrement impossible.', 'erreur');
            }
        } catch {
            afficherMessage(message, 'Le serveur ne répond pas. Réessaie plus tard.', 'erreur');
        }
    });
}
