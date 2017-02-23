//fonction pour activer une fenétre de suppression
function confirmer(){
    return confirm("Etes-vous sur de vouloir supprimer ?");
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

function connexion(){
    var statut = document.getElementById("connexion").style.display;

    if(statut== "none"){
        document.getElementById("connexion").style.display="block";
    } else {
        document.getElementById("connexion").style.display="none";
    }
}