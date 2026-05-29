/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : details.js
    Rôle    : Code JavaScript commun à toutes les pages details*.html
              Chaque page HTML définit une variable CONFIG avant d'inclure ce fichier.
              CONFIG contient : storageKey, medecinNom, photo, horairesDefaut
*/


/* =========================================
   1. CONSTRUCTEUR : Objet Horaire
   Définit le type d'objet utilisé pour représenter
   un horaire de consultation d'un médecin.
   Propriétés :
     - jour       : le jour de la semaine (ex: "Lundi")
     - matin      : horaire du matin (ex: "8h - 12h")
     - apresMidi  : horaire de l'après-midi (ex: "14h - 18h" ou "Fermé")
     - medecinNom : nom complet du médecin
     - photo      : chemin vers la photo du médecin
   ========================================= */

function Horaire(jour, matin, apresMidi, medecinNom, photo) {
    this.jour       = jour;
    this.matin      = matin;
    this.apresMidi  = apresMidi;
    this.medecinNom = medecinNom;
    this.photo      = photo;
}


/* =========================================
   2. VARIABLES GLOBALES
   - horaires : tableau d'objets Horaire chargés depuis localStorage
   - isAdmin  : booléen indiquant si l'admin est connecté
   ========================================= */

var horaires = [];
var isAdmin  = localStorage.getItem("admin") === "true";


/* =========================================
   3. ADMIN : Connexion / Déconnexion
   loginAdmin()  : demande le mot de passe et active le mode admin
   logoutAdmin() : supprime la session admin et recharge la page
   Mot de passe admin : "admin123"
   ========================================= */

// Connexion admin avec mot de passe
function loginAdmin() {
    var mdp = prompt("Mot de passe admin :");
    if (mdp === "admin123") {
        localStorage.setItem("admin", "true");  // sauvegarder la session
        alert("Mode admin activé !");
        location.reload();                       // recharger pour afficher le formulaire
    } else {
        alert("Mot de passe incorrect.");
    }
}

// Déconnexion admin
function logoutAdmin() {
    localStorage.removeItem("admin");  // supprimer la session
    alert("Déconnecté.");
    location.reload();
}


/* =========================================
   4. CHARGER les données depuis localStorage
   - Si des données existent dans localStorage (clé = CONFIG.storageKey),
     on les charge et on les convertit en objets Horaire.
   - Sinon, on utilise les horaires par défaut définis dans CONFIG.horairesDefaut
     (tableaux d'objets simples convertis en objets Horaire).
   ========================================= */

function charger() {
    var data = localStorage.getItem(CONFIG.storageKey);

    if (data) {
        // Données existantes → convertir JSON en objets Horaire
        var parsed = JSON.parse(data);
        horaires = parsed.map(function(h) {
            return new Horaire(h.jour, h.matin, h.apresMidi, h.medecinNom, h.photo);
        });
    } else {
        // Pas de données → utiliser les horaires par défaut de CONFIG
        horaires = CONFIG.horairesDefaut.map(function(h) {
            return new Horaire(h.jour, h.matin, h.apresMidi, h.medecinNom, h.photo);
        });
    }
}


/* =========================================
   5. SAUVEGARDER dans localStorage
   Convertit le tableau d'objets Horaire en JSON
   et le sauvegarde avec la clé CONFIG.storageKey.
   ========================================= */

function sauvegarder() {
    localStorage.setItem(CONFIG.storageKey, JSON.stringify(horaires));
}


/* =========================================
   6. FONCTION 1 : ajouterLigneTableau(horaire)
   Prend un objet Horaire et insère une nouvelle ligne <tr>
   dans le tableau HTML "table-horaires".
   Chaque cellule correspond à une propriété de l'objet.
   ========================================= */

function ajouterLigneTableau(horaire) {
    var table = document.getElementById("table-horaires");
    var row   = table.insertRow();              // créer une nouvelle ligne <tr>
    row.insertCell(0).innerText = horaire.jour;       // colonne Jour
    row.insertCell(1).innerText = horaire.matin;      // colonne Matin
    row.insertCell(2).innerText = horaire.apresMidi;  // colonne Après-midi
}


/* =========================================
   7. FONCTION 2 : afficherTableau()
   Réinitialise le tableau HTML avec les en-têtes,
   puis appelle ajouterLigneTableau() pour chaque
   objet du tableau horaires (itération avec forEach).
   ========================================= */

function afficherTableau() {
    var table = document.getElementById("table-horaires");

    // Réinitialiser le tableau avec les en-têtes
    table.innerHTML = "<tr><th rowspan='2'>Jour</th><th colspan='2'>Horaires</th></tr><tr><th>Matin</th><th>Après-midi</th></tr>";

    // Parcourir le tableau et afficher chaque horaire
    horaires.forEach(function(h) {
        ajouterLigneTableau(h);
    });
}


/* =========================================
   8. AJOUTER / MODIFIER un horaire (formulaire admin)
   - Vérifie que l'admin est connecté
   - Récupère les valeurs saisies dans le formulaire
   - Si le jour existe déjà → modification (mise à jour)
   - Sinon → ajout d'un nouvel objet Horaire
   - Sauvegarde et rafraîchit le tableau
   ========================================= */

function ajouterLigne() {

    // Vérification : seul l'admin peut modifier
    if (!isAdmin) {
        alert("Accès refusé. Connectez-vous en mode admin.");
        return;
    }

    // Récupérer les valeurs saisies dans le formulaire
    var jour      = document.getElementById("jour").value.trim();
    var matin     = document.getElementById("matin").value.trim();
    var apresMidi = document.getElementById("apresMidi").value.trim();

    // Vérifier que tous les champs sont remplis
    if (!jour || !matin || !apresMidi) {
        alert("Veuillez remplir tous les champs.");
        return;
    }

    // Chercher si ce jour existe déjà dans le tableau → modification
    var trouve = false;
    horaires.forEach(function(h) {
        if (h.jour.toLowerCase() === jour.toLowerCase()) {
            h.matin     = matin;      // mettre à jour le matin
            h.apresMidi = apresMidi;  // mettre à jour l'après-midi
            trouve      = true;
        }
    });

    // Si le jour n'existe pas → ajouter un nouvel objet Horaire
    if (!trouve) {
        horaires.push(new Horaire(jour, matin, apresMidi, CONFIG.medecinNom, CONFIG.photo));
    }

    // Sauvegarder dans localStorage et rafraîchir le tableau HTML
    sauvegarder();
    afficherTableau();

    // Vider les champs du formulaire
    document.getElementById("jour").value      = "";
    document.getElementById("matin").value     = "";
    document.getElementById("apresMidi").value = "";

    // Afficher un message de confirmation pendant 3 secondes
    var msg = document.getElementById("msg-ajout");
    msg.innerText = trouve ? "✔ Horaire modifié avec succès." : "✔ Nouvel horaire ajouté.";
    setTimeout(function() { msg.innerText = ""; }, 3000);
}


/* =========================================
   9. RECHERCHER un horaire par jour
   - Récupère la valeur saisie dans le champ de recherche
   - Filtre les objets Horaire dont le jour contient la valeur
   - Réinitialise le tableau et affiche uniquement les résultats
   - Si aucun résultat → affiche un message "Aucun résultat trouvé"
   ========================================= */

function rechercherHoraire() {
    var val = document.getElementById("search").value.toLowerCase().trim();

    // Filtrer les horaires dont le jour contient la valeur cherchée
    var resultats = horaires.filter(function(h) {
        return h.jour.toLowerCase().includes(val);
    });

    // Réinitialiser le tableau avec les en-têtes
    var table = document.getElementById("table-horaires");
    table.innerHTML = "<tr><th rowspan='2'>Jour</th><th colspan='2'>Horaires</th></tr><tr><th>Matin</th><th>Après-midi</th></tr>";

    if (resultats.length === 0) {
        // Aucun résultat → afficher un message dans une cellule fusionnée
        var row  = table.insertRow();
        var cell = row.insertCell(0);
        cell.colSpan         = 3;
        cell.innerText       = "Aucun résultat trouvé.";
        cell.style.fontStyle = "italic";
        cell.style.color     = "gray";
    } else {
        // Afficher les résultats filtrés
        resultats.forEach(function(h) {
            ajouterLigneTableau(h);
        });
    }
}


/* =========================================
   10. INITIALISATION au chargement de la page
   - Charger les données depuis localStorage (ou par défaut)
   - Afficher le tableau des horaires
   - Cacher le formulaire admin si l'utilisateur n'est pas admin
   ========================================= */

charger();        // charger les données
afficherTableau(); // afficher le tableau

// Cacher le formulaire admin si pas connecté
if (!isAdmin) {
    document.getElementById("adminForm").style.display = "none";
}
