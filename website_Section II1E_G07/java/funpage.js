/*
    Membres: Zeineb Mekki / Israa Trabelsi 
*/

/* =====================================================
   1. CONSTRUCTEUR : Objet Question
===================================================== */

// Définir un type d'objet Question avec 4 propriétés
function Question(definition, reponse, difficulte, photo) {
    this.definition = definition; // texte affiché au joueur
    this.reponse    = reponse;    // réponse correcte en minuscule
    this.difficulte = difficulte; // niveau : "facile", "moyen", "difficile"
    this.photo      = photo;      // chemin vers l'image illustrant la spécialité
}


/* =====================================================
   2. TABLEAU DE QUESTIONS (objets Question)
===================================================== */

let questions = [
    new Question("Médecin du cœur",         "cardiologue",   "moyen",     "../images/doctor1.jpg"),
    new Question("Spécialiste des enfants",  "pediatre",      "facile",    "../images/doctor3.jpg"),
    new Question("Médecin des yeux",         "ophtalmologue", "moyen",     "../images/doctor4.jpg"),
    new Question("Médecin de la peau",       "dermatologue",  "moyen",     "../images/doctor2.jpg"),
    new Question("Médecin du cerveau",       "neurologue",    "difficile", "../images/doctor7.jpg"),
    new Question("Médecin des os",           "orthopediste",  "difficile", "../images/doctor5.jpg"),
    new Question("Spécialiste des dents",    "dentiste",      "facile",    "../images/logo.jpg")
];

// Variables globales du jeu
let index = 0;  // question actuelle
let score = 0;  // score du joueur
let temps = 10; // secondes restantes
let timer;      // référence au setInterval


/* =====================================================
   3. AFFICHER UNE QUESTION (manipulation du DOM)
===================================================== */

function afficherQuestion() {

    let questionEl = document.getElementById("question");

    // Appliquer l'animation fade (classe CSS)
    questionEl.classList.add("fade");

    // Après 200ms : changer le texte et retirer l'animation
    setTimeout(() => {
        questionEl.textContent = questions[index].definition;
        questionEl.classList.remove("fade");
    }, 200);

    // Effacer le message précédent
    document.getElementById("message").textContent = "";

    // Relancer le timer
    resetTimer();
}


/* =====================================================
   4. TIMER : compte à rebours
===================================================== */

function resetTimer() {

    // Stopper l'ancien timer avant d'en créer un nouveau
    clearInterval(timer);

    // Remettre le temps à 10 secondes
    temps = 10;
    document.getElementById("temps").textContent = temps;

    // Démarrer un nouveau compte à rebours (toutes les 1 seconde)
    timer = setInterval(() => {

        temps--;
        document.getElementById("temps").textContent = temps;

        // Temps écoulé → passer à la question suivante
        if (temps === 0) {
            clearInterval(timer);
            document.getElementById("message").textContent = "⏱️ Temps écoulé !";
            prochaineQuestion();
        }

    }, 1000);
}


/* =====================================================
   5. QUESTION SUIVANTE
===================================================== */

function prochaineQuestion() {

    index++;

    // Si toutes les questions sont terminées
    if (index >= questions.length) {
        finJeu();
    } else {
        afficherQuestion();
    }
}


/* =====================================================
   6. FIN DU JEU
===================================================== */

function finJeu() {

    // Arrêter le timer
    clearInterval(timer);

    // Afficher le score final dans la zone question
    document.getElementById("question").textContent =
        "🎉 Jeu terminé ! Score final : " + score + " / " + questions.length;

    // Cacher les éléments de jeu
    document.getElementById("reponse").style.display   = "none";
    document.getElementById("btnValider").style.display = "none";
    document.getElementById("temps").textContent        = "-";

    // Afficher le bouton rejouer
    document.getElementById("btnRejouer").style.display = "inline-block";
}


/* =====================================================
   7. REJOUER
===================================================== */

function rejouer() {

    // Réinitialiser toutes les variables
    index = 0;
    score = 0;

    // Remettre l'affichage
    document.getElementById("score").textContent       = 0;
    document.getElementById("reponse").style.display   = "block";
    document.getElementById("btnValider").style.display = "inline-block";
    document.getElementById("btnRejouer").style.display = "none";
    document.getElementById("message").textContent     = "";

    // Relancer le jeu
    afficherQuestion();
}


/* =====================================================
   8. ÉVÉNEMENT : Bouton Valider (addEventListener)
      + stopPropagation pour éviter le bubbling
===================================================== */

document.getElementById("btnValider").addEventListener("click", function(e) {

    // ⛔ Empêcher la propagation vers childBox et parentBox
    // Sans ça, cliquer sur Valider déclencherait aussi les événements parents
    e.stopPropagation();

    // Récupérer la réponse saisie (convertie en minuscule)
    let input   = document.getElementById("reponse").value.trim().toLowerCase();
    let bonneRep = questions[index].reponse;

    // Vérifier si la réponse est correcte
    if (input === bonneRep) {
        score++;
        document.getElementById("score").textContent = score;
        document.getElementById("message").textContent = "✅ Bonne réponse !";
    } else {
        document.getElementById("message").textContent =
            "❌ Mauvaise réponse. C'était : " + bonneRep;
    }

    // Vider le champ de saisie
    document.getElementById("reponse").value = "";

    // Passer à la question suivante
    prochaineQuestion();
});

// Permettre de valider avec la touche Entrée (addEventListener clavier)
document.getElementById("reponse").addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
        document.getElementById("btnValider").click();
    }
});


/* =====================================================
   9. PROPAGATION DES ÉVÉNEMENTS (Event Bubbling)
      3 niveaux imbriqués : niveau3 → niveau2 → niveau1
===================================================== */

// Fonction qui AJOUTE une ligne dans le log (au lieu de remplacer)
function logBubbling(msg) {
    let log = document.getElementById("bubbling-log");

    // Créer une nouvelle ligne
    let ligne = document.createElement("div");
    ligne.innerHTML = msg;
    log.appendChild(ligne);
}

// Fonction qui vide le log avant chaque nouveau clic
function viderLog() {
    document.getElementById("bubbling-log").innerHTML = "";
}

// --- NIVEAU 3 : interactiveEl (l'élément le plus profond) ---
// Le clic part d'ici EN PREMIER et remonte vers les parents
document.getElementById("interactiveEl").addEventListener("click", function(e) {
    viderLog(); // vider le log au début de chaque clic
    logBubbling("🟢 Niveau 3 → <strong>interactive-zone</strong> a reçu le clic");
});

// --- NIVEAU 2 : childBox ---
// Reçoit le clic EN DEUXIÈME (bubbling depuis niveau 3)
document.getElementById("childBox").addEventListener("click", function() {
    logBubbling("🔵 Niveau 2 → <strong>child-zone</strong> a reçu le clic");
});

// --- NIVEAU 1 : parentBox (le plus haut dans la hiérarchie) ---
// Reçoit le clic EN DERNIER (bubbling depuis niveau 2)
document.getElementById("parentBox").addEventListener("click", function() {
    logBubbling("🔴 Niveau 1 → <strong>game-box (parent)</strong> a reçu le clic");
});

/*
    RÉSUMÉ DU BUBBLING :
    - Clic sur interactiveEl → affiche les 3 messages (niveau 3 → 2 → 1)
    - Clic sur childBox      → affiche 2 messages (niveau 2 → 1)
    - Clic sur parentBox     → affiche 1 message  (niveau 1 seulement)
    - Clic sur btnValider    → stopPropagation() → aucun message
*/


/* =====================================================
   10. INITIALISATION : lancer le jeu
===================================================== */

afficherQuestion();
