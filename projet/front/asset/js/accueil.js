// ===========================================================================
// accueil.js – page d'arrivée (Alexis)
// ---------------------------------------------------------------------------
// Les fonctionnalités, chacune dans son bloc :
//   1. Le rideau d'ouverture (retiré de la page une fois ouvert)
//   2. Les cartes "atouts" qui apparaissent quand on les voit à l'écran
//   3. Les onglets "Créer un compte" / "Se connecter"
//   4. La vérification et l'envoi des formulaires à l'API
//
// Le script est chargé avec "defer" : quand il s'exécute, le HTML est déjà
// entièrement lu, on peut donc chercher les éléments directement.
// ===========================================================================


// ---------------------------------------------------------------------------
// 1. Le rideau d'ouverture
// ---------------------------------------------------------------------------
// L'animation est faite en CSS. Ici, on retire le rideau de la page une fois
// ouvert (1,6 s) : par sécurité, si l'animation ne s'est pas jouée (onglet en
// arrière-plan, vieux navigateur...), le site n'est pas caché derrière.
const rideau = document.querySelector('.rideau');
setTimeout(() => rideau?.remove(), 1600);   // ?. : ne fait rien si le rideau n'existe pas


// ---------------------------------------------------------------------------
// 2. Apparition des cartes "atouts" (toutes en même temps)
// ---------------------------------------------------------------------------
// IntersectionObserver prévient quand un élément entre dans l'écran.
// On surveille la LISTE entière (.atouts) : dès qu'elle est visible,
// on affiche les 4 cartes d'un coup.
const listeAtouts = document.querySelector('.atouts');
const atouts = document.querySelectorAll('.atout');

const observateur = new IntersectionObserver((entrees) => {
    if (entrees[0].isIntersecting) {
        // On attend 1,2 s (le temps que le rideau s'ouvre), puis on affiche tout
        setTimeout(() => {
            atouts.forEach((carte) => carte.classList.add('visible'));
        }, 1200);
        observateur.disconnect();   // c'est fait une fois pour toutes : on arrête de surveiller
    }
}, { threshold: 0.2 });   // déclenché quand 20 % de la liste est visible

observateur.observe(listeAtouts);


// ---------------------------------------------------------------------------
// 3. Les onglets
// ---------------------------------------------------------------------------
const onglets = document.querySelectorAll('.onglet');

function afficherOnglet(ongletChoisi) {
    onglets.forEach((onglet) => {
        const actif = onglet === ongletChoisi;
        const formulaire = document.getElementById(onglet.getAttribute('aria-controls'));

        onglet.setAttribute('aria-selected', String(actif));   // change la couleur (voir CSS)
        formulaire.hidden = !actif;                            // montre l'un, cache l'autre

        if (actif) {
            formulaire.querySelector('input').focus();         // curseur directement dans le 1er champ
        }
    });
}

onglets.forEach((onglet) => {
    onglet.addEventListener('click', () => afficherOnglet(onglet));
});


// ---------------------------------------------------------------------------
// 4. Les formulaires
// ---------------------------------------------------------------------------

// Écrit un message sous le formulaire. type = 'erreur' ou 'succes' (change la couleur).
// textContent (et pas innerHTML) : le texte n'est jamais interprété comme du HTML.
function afficherMessage(formulaire, texte, type) {
    const zone = formulaire.querySelector('.message');
    zone.textContent = texte;
    zone.className = 'message ' + type;
}

// Marque un champ en rouge (CSS : [aria-invalid="true"]) et met le curseur dedans
function marquerChamp(champ) {
    champ.setAttribute('aria-invalid', 'true');
    champ.focus();
}

// Retire toutes les marques d'erreur d'un formulaire
function nettoyer(formulaire) {
    formulaire.querySelectorAll('input').forEach((champ) => champ.removeAttribute('aria-invalid'));
    afficherMessage(formulaire, '', '');
}

// Envoie des données en JSON à une route de l'API et renvoie { ok, donnees }.
// Gère aussi le cas où la route n'existe pas encore ou ne répond pas du JSON.
async function envoyer(url, donnees) {
    const reponse = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(donnees),
    });
    // Si la réponse n'est pas du JSON (route pas encore codée, page d'erreur...),
    // on considère que c'est un échec, même si le serveur a répondu "200 OK".
    try {
        const corps = await reponse.json();
        return { ok: reponse.ok, donnees: corps };
    } catch {
        return { ok: false, donnees: { erreur: 'Ce service n\'est pas encore disponible.' } };
    }
}

// Bloque le bouton pendant l'envoi (évite les doubles clics), puis le débloque
async function pendantEnvoi(formulaire, action) {
    const bouton = formulaire.querySelector('button[type="submit"]');
    bouton.disabled = true;
    try {
        await action();
    } catch {
        afficherMessage(formulaire, 'Le serveur ne répond pas. Réessaie plus tard.', 'erreur');
    } finally {
        bouton.disabled = false;
    }
}

// ----- Inscription -----
const formInscription = document.getElementById('form-inscription');

formInscription.addEventListener('submit', (evenement) => {
    evenement.preventDefault();      // empêche le rechargement de la page
    nettoyer(formInscription);

    const champs = formInscription.elements;   // accès aux champs par leur "name"
    const nom   = champs.nom.value.trim();     // trim : enlève les espaces au début et à la fin
    const email = champs.email.value.trim();
    const mdp   = champs.mdp.value;
    const mdp2  = champs.mdp2.value;

    // Vérifications côté navigateur : rapides et pratiques pour l'utilisateur.
    // ⚠️ Elles ne protègent RIEN : n'importe qui peut les contourner.
    //    Le back devra refaire les mêmes vérifications.
    if (nom === '') {
        marquerChamp(champs.nom);
        return afficherMessage(formInscription, 'Choisis un nom d\'utilisateur.', 'erreur');
    }
    if (!champs.email.checkValidity() || email === '') {   // checkValidity : le navigateur vérifie le format e-mail
        marquerChamp(champs.email);
        return afficherMessage(formInscription, 'Cette adresse e-mail n\'est pas valide.', 'erreur');
    }
    if (mdp.length < 8) {
        marquerChamp(champs.mdp);
        return afficherMessage(formInscription, 'Le mot de passe doit faire au moins 8 caractères.', 'erreur');
    }
    if (mdp !== mdp2) {
        marquerChamp(champs.mdp2);
        return afficherMessage(formInscription, 'Les deux mots de passe ne sont pas identiques.', 'erreur');
    }

    pendantEnvoi(formInscription, async () => {
        const { ok, donnees } = await envoyer('/api/inscription', { nom, email, mdp });
        if (ok) {
            afficherMessage(formInscription, 'Compte créé ! Tu peux te connecter.', 'succes');
            formInscription.reset();
            // On passe sur l'onglet connexion avec le nom déjà rempli
            afficherOnglet(document.getElementById('onglet-connexion'));
            document.getElementById('cnx-identifiant').value = nom;
            document.getElementById('cnx-mdp').focus();
        } else {
            afficherMessage(formInscription, donnees.erreur ?? 'Inscription impossible.', 'erreur');
        }
    });
});

// ----- Connexion -----
const formConnexion = document.getElementById('form-connexion');

formConnexion.addEventListener('submit', (evenement) => {
    evenement.preventDefault();
    nettoyer(formConnexion);

    const champs = formConnexion.elements;
    const identifiant = champs.identifiant.value.trim();
    const mdp = champs.mdp.value;

    if (identifiant === '') {
        marquerChamp(champs.identifiant);
        return afficherMessage(formConnexion, 'Indique ton nom d\'utilisateur ou ton e-mail.', 'erreur');
    }
    if (mdp === '') {
        marquerChamp(champs.mdp);
        return afficherMessage(formConnexion, 'Indique ton mot de passe.', 'erreur');
    }

    pendantEnvoi(formConnexion, async () => {
        const { ok, donnees } = await envoyer('/api/auth', { identifiant, mdp });
        if (ok) {
            window.location.href = '/';   // connecté : la page d'accueil redirigera au bon endroit
        } else {
            afficherMessage(formConnexion, donnees.erreur ?? 'Connexion impossible.', 'erreur');
        }
    });
});
