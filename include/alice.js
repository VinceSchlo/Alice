//fonction pour activer une fenétre de suppression
function confirmDeleteAgent() {
    return confirm("Etes-vous sûr de vouloir supprimer ?");
}

function surligne(champ, erreur) {
    if (erreur)
        champ.style.backgroundColor = "#fba";
    else
        champ.style.backgroundColor = "";
}

function activeLoginMdpForm() {
    var statut = document.getElementById("divLogin").style.display;

    if (statut === "none") {
        document.getElementById("divLogin").style.display = "block";
        document.getElementById("divMdp1").style.display = "block";
        document.getElementById("divMdp2").style.display = "block";
    } else {
        document.getElementById("divLogin").style.display = "none";
        document.getElementById("divMdp1").style.display = "none";
        document.getElementById("divMdp2").style.display = "none";
    }
}

function verifNom(champ) {
    if (champ.value.length < 3 || champ.value.length > 20) {
        surligne(champ, true);
        return false;
    } else {
        surligne(champ, false);
        return true;
    }
}

function verifAddAgentForm(f) {
    var nomOK = verifNom(f.nomForm);
    var prenomOK = verifNom(f.prenomForm);
    var statut = document.getElementById("divLogin").style.display;

    if (!nomOK || !prenomOK) {
        alert("Veuillez saisir un nom et un prénom\nde longueur supérieure à 2 caractères\net inférieure à 20 caractères");
        return false;
    } else {
        if (statut === "block") {
            /* var login = document.getElementById("login").value; */
            var loginOK = verifNom(f.loginForm);

            if (!loginOK) {
                alert("Veuillez saisir un identifiant de connexion\nde longueur supérieure à 2 caractères\net inférieure à 20 caractères");
                return false;
            } else {
                var mdp1 = document.getElementById("mdp1").value;
                var mdp2 = document.getElementById("mdp2").value;

                if (!mdp1 || !mdp2) {
                    alert("Veuillez saisir un mot de passe\nde longueur supérieure à 2 caractères\net inférieure à 20 caractères");
                    return false;
                }

                if (mdp1 !== mdp2) {
                    alert("Les 2 mots de passe sont différents");
                    return false;
                }

                if (loginOK && mdp1 === mdp2) {
                    return true;
                }

            }
        }
    }
}

function verifDate(champ) {

    var dateDeb = document.getElementById("dateDeb").value;
    var dateFin = document.getElementById("dateFin").value;

    if (dateDeb > dateFin) {
        champ.style.backgroundColor = "#fba";
        alert("La date de fin saisie est inférieure à la date de début");
        return false;
    } else {
        champ.style.backgroundColor = "";
        return true;
    }
}

function connexion() {
    var statut = document.getElementById("connexion").style.display;

    if (statut === "none") {
        document.getElementById("connexion").style.display = "block";
    } else {
        document.getElementById("connexion").style.display = "none";
    }
}

function toast(texte) {
    var options = {
        style: {
            main: {
                background: "#ec971f",
                color: "white",
                width: '15%'
            }
        },
        settings: {
            duration: 10000
        }
    };
    iqwerty.toast.Toast(texte, options);
}

function saisieDate() {
var Masker = require('maskerjs');
var dateMask = new Masker(
    ['__/__/____'],
    /^[0-9]$/ // allowed chars
);

var dateDebInput = document.getElementById('dateDeb');
var dateFinInput = document.getElementById('dateFin');

dateMask.bind(dateDebInput);
dateMask.unbind(dateDebInput);
dateMask.bind(dateFinInput);
// telMask.unbind(telInput);

var val1 = dateMask.unmask(dateDebInput.value).text;
var val2 = dateMask.unmask(dateFinInput.value).text;
}