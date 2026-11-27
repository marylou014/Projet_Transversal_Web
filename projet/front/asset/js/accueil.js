// ===========================================================================
// accueil.js – page d'arrivée (Alexis)
// ---------------------------------------------------------------------------
// Ce fichier s'occupe de la présentation :
//   1. Le rideau d'ouverture (retiré de la page une fois ouvert)
//   2. Les cartes "atouts" qui apparaissent quand on les voit à l'écran
// Les formulaires (onglets, inscription, connexion) sont dans connexion.js.
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
        // S'il y a le rideau, on attend 1,2 s (le temps qu'il s'ouvre), sinon on affiche tout de suite
        const attente = rideau ? 1200 : 0;
        setTimeout(() => {
            atouts.forEach((carte) => carte.classList.add('visible'));
        }, attente);
        observateur.disconnect();   // c'est fait une fois pour toutes : on arrête de surveiller
    }
}, { threshold: 0.2 });   // déclenché quand 20 % de la liste est visible

observateur.observe(listeAtouts);
