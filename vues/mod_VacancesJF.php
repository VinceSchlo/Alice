<?php
session_start(); // Utilisation des variables $_SESSION

require_once('../class/ferie.php');
require_once('../class/vacances.php');
require_once('../include/alice_fonctions.php');
require_once('../include/alice_dao.inc.php');
?>

<?php include("../include/doctype.php"); ?>
<div class="col-xs-8">
    <br />
    <div class="row">
        <!-- Affichage du titre de la page -->
        <div class="col-md-12 border-table">
            <h2>Modification des dates des vacances scolaires et des jours fériés</h2>
        </div>
    </div>
    <div class="row">
    </div>
</div>
<?php
include("../include/header_admin.php");

// Création des objets jours fériés et Vacances
$ferie = new Ferie();
$vacances = new Vacances();
// Création des tableaux issus des select en BDD pour l'affichage
$tabFerie = $ferie->selectAllFerie();
$tabVacances = $vacances->selectAllVacances();
// Initialisation de variables pour les for
 $lengthTabVacances = count($tabVacances);
 $lengthTabFerie = count($tabFerie);

if (isset($_POST['updateVac'])) { // Cas du bouton orange "enregistrer"
   
    for ($i = 0; $i < $lengthTabVacances; $i++) {
        $vacances->setIdVac($_POST['idVacForm' . $i]);
        $vacances->setDateDebVac(convertDateFrUs($_POST['dateDebForm' . $i]));
        $vacances->setDateFinVac(convertDateFrUs($_POST['dateFinForm' . $i]));
        // On met à jour la BDD vacances
        $vacances->updateVacances();
    }
}
if (isset($_POST['updateFerie'])) { // Cas du bouton orange "enregistrer"
    
    for ($i = count($tabVacances); $i < ($lengthTabVacances + $lengthTabFerie); $i++) {
        $ferie->setIdFerie($_POST['idFerieForm' . $i]);
        $ferie->setDateDebFerie(convertDateFrUs($_POST['dateDebForm' . $i]));
        $ferie->setDateFinFerie(convertDateFrUs($_POST['dateFinForm' . $i]));
        // On met à jour la BDD ferie
        $ferie->updateFerie();
    }
}
if (isset($_POST['annuler'])) {// Cas du bouton vert "annuler"
    // Retour à la page d'accueil administrateur sans modification
    // die('<META HTTP-equiv="refresh" content=0;URL=mod_VacancesJF.php>');
}
// On rafraîchit le select pour afficher les modifs faites en BDD
$tabVacances = $vacances->selectAllVacances();
// On rafraîchit le select pour afficher les modifs faites en BDD
$tabFerie = $ferie->selectAllFerie();
//
?>
<body class="background-color-admin">
    <!-- Affichage des vacances -->
    <div class="container-fluid col-md-offset-1 col-md-10 col-lg-offset-3 col-lg-10">
        <div class="col-lg-7">
            <table class="table table-bordered">
                <tr class="color-grey">
                    <th class="thCentre">Vacances</th>
                    <th class="thCentre">Date de début</th>
                    <th class="thCentre">Date de fin</th>
                </tr>
                <br/>
                <!-- Début du formulaire des vacances -->
                <form class="form-horizontal" id="formVac" name="formVac" method="POST" action="mod_VacancesJF.php" onsubmit="return verifFormDate()">
                    <?php
                    for ($i = 0; $i < $lengthTabVacances; $i++) {
                        ?>
                        <tr>
                        <input type="hidden" name="idVacForm<?php echo $i; ?>"
                               value="<?php echo $tabVacances[$i]['idVac']; ?>">
                        <td class="name-size-admin">
                            <input size="10" class="form-control" disabled name="nomForm<?php echo $i; ?>"
                                   value="<?php echo $tabVacances[$i]['nomVac']; ?>">
                        </td>

                        <!--                            Date de début des vacances -->
                        <td class="name-size-admin">
                            <input size="10" class="form-control" id="dateDebVac<?php echo $i; ?>" type="text"
                                   name="dateDebForm<?php echo $i; ?>"
                                   value="<?php echo convertDateUsFr($tabVacances[$i]['dateDebVac']); ?>"
                                   onKeyUp="masqueSaisieDate(this.form.dateDebForm<?php echo $i; ?>)"
                                   onchange="verifDateFinVac<?php echo $i; ?>()">
                        </td>

                        <!--                            Date de fin des vacances -->
                        <td class="name-size-admin">
                            <div class="form-group has-feedback" id="divDateVac<?php echo $i; ?>">
                                <input size="10" class="form-control" id="dateFinVac<?php echo $i; ?>" type="text"
                                       name="dateFinForm<?php echo $i; ?>"
                                       value="<?php echo convertDateUsFr($tabVacances[$i]['dateFinVac']); ?>"
                                       onKeyUp="masqueSaisieDate(this.form.dateFinForm<?php echo $i; ?>)"
                                       onchange="verifDateFinVac<?php echo $i; ?>()">
                                <span class="help-block" style="display: none" id="spanVac<?php echo $i; ?>">La date de fin doit être<br/>supérieure à la date de début</span>
                        </td>
                        <script type="text/javascript">
                            function verifDateFinVac<?php echo $i; ?>() {

                                var dateDebVac = stringToDate(document.getElementById("dateDebVac<?php echo $i; ?>").value, "dd/MM/yyyy", "/");
                                var dateFinVac = stringToDate(document.getElementById("dateFinVac<?php echo $i; ?>").value, "dd/MM/yyyy", "/");
                                var differenceDate = dateFinVac - dateDebVac;
                                // alert(differenceDate);
                                var divVac = document.getElementById("divDateVac<?php echo $i; ?>");
                                var spanVac = document.getElementById("spanVac<?php echo $i; ?>");

                                if (differenceDate < 0) {
                                    divVac.classList.add("has-error");
                                    spanVac.style.display = "block";
                                    erreurDate = true; // variable globale initialisée dans alice.js
                                } else {
                                    divVac.classList.remove("has-error");
                                    spanVac.style.display = "none";
                                    erreurDate = false; // variable globale initialisée dans alice.js
                                }
                            }
                        </script>
                        </tr>
                    <?php } ?>
                    <div class="pull-right text-right">
                        <!-- Affichage de 2 boutons -->
                        <button name="annuler" class="btn btn-success"
                                class="glyphicon glyphicon-ban-circle"><span
                                class="glyphicon glyphicon-ban-circle"></span> Annuler
                        </button>
                        <button type="submit" name="updateVac" class="btn btn-warning"><span
                                class="glyphicon glyphicon-floppy-open"></span> Enregistrer
                        </button>
                    </div>
                </form>
            </table>
        </div>
    </div>

    <!-- Affichage des jours fériés -->
    <div class="container-fluid col-md-offset-1 col-md-10 col-lg-offset-3 col-lg-10">
        <div class="col-lg-7">
            <table class="table table-bordered" id="tableJF">
                <thead>
                    <tr class="color-grey">
                        <th class="thCentre">Jours fériés</th>
                        <th class="thCentre">Date de début</th>
                        <th class="thCentre">Date de fin</th>
                    </tr>
                </thead>
                <br/>
                <tbody>
                    <!-- Début du formulaire des jours fériés -->
                <form class="form-horizontal" id="formJF" name="formJF" method="POST" action="mod_VacancesJF.php" onsubmit="return verifFormDate()">
                    <?php
                    $j = 0;
                    for ($i = $lengthTabVacances; $i < ($lengthTabVacances + $lengthTabFerie); $i++) {
                        ?>
                        <tr>
                        <input type="hidden" name="idFerieForm<?php echo $i; ?>"
                               value="<?php echo $tabFerie[$j]['idFerie']; ?>">
                        <td class="name-size-admin">
                            <input size="10" class="form-control" disabled type="text" name="nomForm<?php echo $i; ?>"
                                   value="<?php echo $tabFerie[$j]['nomFerie']; ?>">
                        </td>

                        <!--                            Date de début des jours fériés -->
                        <td class="name-size-admin">
                            <input size="10" class="form-control" type="text" name="dateDebForm<?php echo $i; ?>"
                                   value="<?php echo convertDateUsFr($tabFerie[$j]['dateDebFerie']); ?>"
                                   id="dateDebFerie<?php echo $i; ?>"
                                   onKeyUp="masqueSaisieDate(this.form.dateDebForm<?php echo $i; ?>)"
                                   onchange="verifDateFinFerie<?php echo $i; ?>()">
                        </td>

                        <!--                            Date de fin des jours fériés -->
                        <td class="name-size-admin">
                            <div class="form-group has-feedback" id="divDateFerie<?php echo $i; ?>">
                                <input size="6" class="form-control" type="text" name="dateFinForm<?php echo $i; ?>"
                                       value="<?php
                                       if (empty($tabFerie[$j]['dateFinFerie'])) {
                                           echo convertDateUsFr($tabFerie[$j]['dateDebFerie']);
                                       } else {
                                           echo convertDateUsFr($tabFerie[$j]['dateFinFerie']);
                                       }
                                       ?>"
                                       id="dateFinFerie<?php echo $i; ?>"
                                       onKeyUp="masqueSaisieDate(this.form.dateFinForm<?php echo $i; ?>)"
                                       onchange="verifDateFinFerie<?php echo $i; ?>()">
                                <span class="help-block" style="display: none" id="spanFerie<?php echo $i; ?>">La date de fin doit être<br/>supérieure à la date de début</span>
                            </div>
                        </td>
                        <script type="text/javascript">
                            function verifDateFinFerie<?php echo $i; ?>() {

                                var dateDebFerie = stringToDate(document.getElementById("dateDebFerie<?php echo $i; ?>").value, "dd/MM/yyyy", "/");
                                var dateFinFerie = stringToDate(document.getElementById("dateFinFerie<?php echo $i; ?>").value, "dd/MM/yyyy", "/");
                                var differenceDate = dateFinFerie - dateDebFerie;
                                var divFerie = document.getElementById("divDateFerie<?php echo $i; ?>");
                                var spanFerie = document.getElementById("spanFerie<?php echo $i; ?>");

                                if (differenceDate < 0) {
                                    divFerie.classList.add("has-error");
                                    spanFerie.style.display = "block";
                                    erreurDate = true; // variable globale initialisée dans alice.js
                                } else {
                                    divFerie.classList.remove("has-error");
                                    spanFerie.style.display = "none";
                                    erreurDate = false; // variable globale initialisée dans alice.js
                                }
                            }
                        </script>
                        </tr>
                        <?php
                        $j++;
                    }
                    ?>
                    <!-- Affichage des 2 boutons -->
                    <div class="pull-right text-right">
                        <button type="submit" name="annuler" class="btn btn-success"><span
                                class="glyphicon glyphicon-ban-circle"></span> Annuler
                        </button>
                        <button type="submit" name="updateFerie" class="btn btn-warning"><span
                                class="glyphicon glyphicon-floppy-open"></span> Enregistrer
                        </button>
                    </div>
                </form>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Header flottant -->
    <script type="text/javascript">
        var table = document.getElementById('tableJF');
        lrStickyHeader(table);
    </script>

    <!-- jQuery -->
    <script src="../bootstrap/js/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bootstrap/js/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../bootstrap/js/sb-admin-2.js"></script>
</body>
</html>