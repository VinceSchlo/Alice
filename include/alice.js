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

function connexion() {
    var statut = document.getElementById("connexion").style.display;
    if (statut === "none") {
        document.getElementById("connexion").style.display = "block";
    } else {
        document.getElementById("connexion").style.display = "none";
    }
}
// Affiche un petit message à la manière de Toast d'Android
function toast(texte, couleurFond) {
    var options = {
        style: {
            main: {
                background: couleurFond,
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
// Masque de saisie pour les dates qui ajoute automatiquement des "/"
function masqueSaisieDate(obj) {
    var ch;
    var ch_gauche, ch_droite;
    ch = obj.value;
    obj.value = ch.slice(0, 10);
    ch.toString();
    if (((ch.slice(2, 3)) !== ("/")) && (ch.length >= 3)) {
        if (ch.slice(0, 2) > 31) {
            ch_gauche = '31';
        } else {
            ch_gauche = ch.slice(0, 2);
        }
        ch_droite = ch.slice(2);
        obj.value = ch_gauche + "/" + ch_droite;
    }
    if (((ch.slice(5, 6)) !== ("/")) && (ch.length >= 6)) {
        if (ch.slice(3, 5) > 12) {
            ch_gauche = ch.slice(0, 3) + '12';
        } else {
            ch_gauche = ch.slice(0, 5);
        }
        ch_droite = ch.slice(5);
        obj.value = ch_gauche + "/" + ch_droite;
    }
    return;
}

function verifFormDate() {
    var dateDebVac = stringToDate(document.getElementById("dateDebVac<?php echo $i; ?>").value, "dd/MM/yyyy", "/");
    var dateFinVac = stringToDate(document.getElementById("dateFinVac<?php echo $i; ?>").value, "dd/MM/yyyy", "/");
    var dateDebFerie = stringToDate(document.getElementById("dateDebFerie<?php echo $i; ?>").value, "dd/MM/yyyy", "/");
    var dateFinFerie = stringToDate(document.getElementById("dateFinFerie<?php echo $i; ?>").value, "dd/MM/yyyy", "/");
    if (dateDebVac > dateFinVac || dateDebFerie > dateFinFerie) {
        return false;
    } else {
        return true;
    }
}
// Fonction pour convertir le string du formulaire date en une date au format jj/mm/aaaa
function stringToDate(_date, _format, _delimiter)
{
    var formatLowerCase = _format.toLowerCase();
    var formatItems = formatLowerCase.split(_delimiter);
    var dateItems = _date.split(_delimiter);
    var monthIndex = formatItems.indexOf("mm");
    var dayIndex = formatItems.indexOf("dd");
    var yearIndex = formatItems.indexOf("yyyy");
    var month = parseInt(dateItems[monthIndex]);
    month -= 1;
    var formatedDate = new Date(dateItems[yearIndex], month, dateItems[dayIndex]);
    return formatedDate;
}
