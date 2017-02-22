/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function activeDesactiveTypeVehicule() {
    var agent = document.getElementById("$_POST['deleteAgent']").value;

    if (monIdLFF == "KM") {
        document.getElementById("divPuissanceFiscale").style.display = "block";
    } else {
        document.getElementById("divPuissanceFiscale").style.display = "none";
    }

    //alert(monIdLFF);
}
