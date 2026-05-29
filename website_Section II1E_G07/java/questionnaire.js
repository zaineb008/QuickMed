/*
    Membres: Zeineb Mekki / Israa Trabelsi 
*/

/* =========================================
   1. TABLEAU DES AVIS (localStorage)
   ========================================= */

// Charger les avis déjà enregistrés, ou démarrer avec un tableau vide
let avisList = JSON.parse(localStorage.getItem("avis")) || [];


/* =========================================
   2. FONCTION : Afficher / Cacher une erreur
   ========================================= */

// Affiche un message d'erreur sous un champ
function afficherErreur(id) {
    document.getElementById(id).style.display = "block";
}

// Cache un message d'erreur
function cacherErreur(id) {
    document.getElementById(id).style.display = "none";
}


/* =========================================
   3. FONCTION : Valider le formulaire
      Vérifie les 5 champs obligatoires
   ========================================= */

function validerFormulaire(nom, email, satisfait, services, note, commentaire) {

    let valide = true; // on suppose que tout est correct

    // Validation 1 : Nom (non vide, min 3 caractères, lettres uniquement)
    if (nom.trim().length < 3 || !/^[a-zA-ZÀ-ÿ\s]+$/.test(nom.trim())) {
        afficherErreur("err-nom");
        valide = false;
    } else {
        cacherErreur("err-nom");
    }

    // Validation 2 : Email (format valide avec regex)
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(email.trim())) {
        afficherErreur("err-email");
        valide = false;
    } else {
        cacherErreur("err-email");
    }

    // Validation 3 : Satisfaction (radio obligatoire)
    if (!satisfait) {
        afficherErreur("err-satisfait");
        valide = false;
    } else {
        cacherErreur("err-satisfait");
    }

    // Validation 4 : Au moins un service coché (checkbox)
    if (services.length === 0) {
        afficherErreur("err-services");
        valide = false;
    } else {
        cacherErreur("err-services");
    }

    // Validation 5 : Note étoile sélectionnée
    if (!note) {
        afficherErreur("err-note");
        valide = false;
    } else {
        cacherErreur("err-note");
    }

    // Validation 6 : Commentaire (minimum 10 caractères)
    if (commentaire.trim().length < 10) {
        afficherErreur("err-commentaire");
        valide = false;
    } else {
        cacherErreur("err-commentaire");
    }

    return valide; // true = tout est bon, false = erreurs trouvées
}


/* =========================================
   4. FONCTION : Calculer et afficher la moyenne
   ========================================= */

function afficherMoyenne() {

    // Si aucun avis → ne rien afficher
    if (avisList.length === 0) {
        document.getElementById("resultat").innerHTML = "";
        return;
    }

    let total = 0;

    // Additionner toutes les notes
    avisList.forEach(a => total += a.note);

    // Calculer la moyenne
    let moyenne = (total / avisList.length).toFixed(1);

    // Afficher la moyenne
    document.getElementById("resultat").innerHTML =
        "⭐ Moyenne des avis : " + moyenne + " / 5 (" + avisList.length + " avis)";
}


/* =========================================
   5. EVENEMENT : Soumission du formulaire
   ========================================= */

document.getElementById("formAvis").addEventListener("submit", function(e) {

    // --- Récupérer les valeurs ---

    let nom         = document.getElementById("nom").value;
    let email       = document.getElementById("email").value;
    let commentaire = document.getElementById("commentaire").value;

    // Radio : satisfaction
    let satisfaitEl = document.querySelector('input[name="satisfait"]:checked');
    let satisfait   = satisfaitEl ? satisfaitEl.value : null;

    // Radio : note étoile
    let noteEl = document.querySelector('input[name="note"]:checked');
    let note   = noteEl ? noteEl.value : null;

    // Checkboxes : services cochés
    let services = [];
    document.querySelectorAll(".cb-service:checked").forEach(cb => {
        services.push(cb.value);
    });

    // --- Valider ---
    let ok = validerFormulaire(nom, email, satisfait, services, note, commentaire);

    // Toujours bloquer la soumission native
    e.preventDefault();

    if (!ok) return;

    // Envoyer les données via fetch vers PHP
    let formData = new FormData(document.getElementById("formAvis"));

    fetch("../php/traitement_avis.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Afficher le message de succès
        document.getElementById("msg-succes").style.display = "block";
        document.getElementById("formAvis").reset();

        // Mettre à jour la moyenne locale
        let noteVal = parseInt(note);
        avisList.push({ note: noteVal });
        localStorage.setItem("avis", JSON.stringify(avisList));
        afficherMoyenne();
    })
    .catch(err => {
        alert("Erreur lors de l'envoi : " + err);
    });
});


/* =========================================
   6. INITIALISATION : afficher la moyenne au chargement
   ========================================= */

afficherMoyenne();
