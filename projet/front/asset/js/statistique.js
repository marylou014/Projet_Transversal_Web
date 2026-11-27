// ===========================================================================
// statistique.js – page "Mes statistiques" (Alexis)
// ---------------------------------------------------------------------------
// 1. On demande les chiffres à l'API : GET /api/statistiques
// 2. On les écrit dans les cases de la page (grâce à leur id)
// 3. On dessine une barre par genre : plus le genre est vu, plus la barre est longue
// ===========================================================================


// Transforme des minutes en texte lisible : 280 → "4 h 40"
function enHeures(minutes) {
    const heures = Math.floor(minutes / 60);         // floor : arrondi vers le bas
    const reste = minutes % 60;                       // % : le reste de la division
    return heures + ' h ' + String(reste).padStart(2, '0');   // padStart : 5 → "05"
}

// Écrit un texte dans l'élément qui a cet id
function ecrire(id, texte) {
    document.getElementById(id).textContent = texte;
}

async function chargerStats() {
    const reponse = await fetch('/api/statistiques');
    if (!reponse.ok) {
        throw new Error('réponse ' + reponse.status);
    }
    const stats = await reponse.json();

    // ----- Les chiffres -----
    ecrire('nbVus', stats.nbVus);
    ecrire('nbAVoir', stats.nbAVoir);
    ecrire('nbAvis', stats.nbAvis);
    ecrire('nbCoupsCoeur', stats.nbCoupsCoeur);
    ecrire('tempsTotal', enHeures(stats.tempsTotal));
    // Pas encore de note → un tiret plutôt que 0 (0/5 voudrait dire "nul")
    ecrire('noteMoyenne', stats.noteMoyenne === null ? '–' : stats.noteMoyenne);

    // ----- Les genres -----
    const zone = document.getElementById('genres');

    if (stats.genres.length === 0) {
        zone.textContent = 'Ajoute des films vus pour découvrir tes genres préférés.';
        return;
    }

    // Le genre le plus vu sert de référence : sa barre fait 100 %
    const maximum = stats.genres[0].total;   // l'API les renvoie déjà du plus vu au moins vu

    stats.genres.forEach((ligne) => {
        // On construit :  <div class="genre"> <span>Thriller (2)</span> <div class="barre" style="width: 50%"></div> </div>
        const bloc = document.createElement('div');
        bloc.className = 'genre';

        const nom = document.createElement('span');
        nom.textContent = ligne.genre + ' (' + ligne.total + ')';

        const barre = document.createElement('div');
        barre.className = 'barre';
        barre.style.width = (ligne.total / maximum * 100) + '%';

        bloc.append(nom, barre);   // append : met les deux à l'intérieur du bloc
        zone.append(bloc);
    });
}

chargerStats().catch(() => {
    const message = document.getElementById('message-stats');
    message.textContent = 'Impossible de charger tes statistiques. Réessaie plus tard.';
    message.className = 'message erreur';
});
