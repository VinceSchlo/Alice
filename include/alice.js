//fonction pour activer une fenétre de suppression
function confirmer(){
    return confirm("Etes-vous sûr de vouloir supprimer ?");
}

function activeLoginMdpForm() {
    var statut = document.getElementById("divLogin").style.display;

    if (statut == "none") {
        document.getElementById("divLogin").style.display = "block";
        document.getElementById("divMdp1").style.display = "block";
        document.getElementById("divMdp2").style.display = "block";
    } else {
        document.getElementById("divLogin").style.display = "none";
        document.getElementById("divMdp1").style.display = "none";
        document.getElementById("divMdp2").style.display = "none";
    }
}

function verif2MdpForm() {
    var mdp1 = document.getElementById("mdp1").value;
    var mdp2 = document.getElementById("mdp2").value;

    if (mdp1 != mdp2) {
        var msg = confirm("Les 2 mots de passe sont différents. Si vous voulez qu'ils soient identiques, appuyez sur OK");
        return msg;
        if (msg) {
            document.getElementById("mdp2").value=document.getElementById("mdp1").value;
        }
    }
}

function connexion(){
    var statut = document.getElementById("connexion").style.display;

    if(statut== "none"){
        document.getElementById("connexion").style.display="block";
    } else {
        document.getElementById("connexion").style.display="none";
    }
}