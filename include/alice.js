//fonction pour activer une fenétre de suppression
function confirmer() {
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

function verifFormDate(f) {
    var dateOK = verifDate(f.dateFin);
    if (dateOK)
        return true;
    else
    {
        alert("Les dates saisies ne sont pas correctes");
        return false;
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

function changeColor(){
    var selectPlan = document.getElementById("selectPlan").style.backgroundColor;
}