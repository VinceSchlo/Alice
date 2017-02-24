<?php
session_start(); // Utilisation des variables $_SESSION

require_once('../class/ferie.php');
require_once('../class/vacances.php');
require_once('../include/alice_fonctions.php');
?>

<?php include("../include/doctype.php"); ?>
<div class="col-lg-6">
    <!-- Affichage du titre de la page -->
    <h2>Modification des dates des vacances scolaires et des jours fériés</h2>
</div>
<?php include("../include/header_admin.php"); ?>

<?php
// Création des objets jours fériés et Vacances
$ferie = new Ferie();
$vacances = new Vacances();
// Création des tableaux issus des select en BDD pour l'affichage
$tabFerie = $ferie->selectAllFerie();
$tabVacances = $vacances->selectAllVacances();
// var_dump($tabFerie);
// var_dump($tabVacances);
// exit;

if (isset($_POST['updateVac'])) { // Cas du bouton orange "enregistrer"
    // var_dump($_POST);
    // exit;
    for ($i = 0; $i < count($tabVacances); $i++) {
        $vacances->setIdVac($_POST['idVacForm' . $i]);
        $vacances->setDateDebVac(convertDateFrUs($_POST['dateDebForm' . $i]));
        $vacances->setDateFinVac(convertDateFrUs($_POST['dateFinForm' . $i]));
        // On met à jour la BDD vacances
        $vacances->updateVacances();
        // On rafraîchit le select pour afficher les mdifs faites en BDD
        $tabVacances = $vacances->selectAllVacances();
    }
}
if (isset($_POST['updateFerie'])) { // Cas du bouton orange "enregistrer"
    // var_dump($_POST);
    // exit;
    for ($i = count($tabVacances); $i < (count($tabVacances) + count($tabFerie)); $i++) {
        $ferie->setIdFerie($_POST['idFerieForm' . $i]);
        $ferie->setDateDebFerie(convertDateFrUs($_POST['dateDebForm' . $i]));
        $ferie->setDateFinFerie(convertDateFrUs($_POST['dateFinForm' . $i]));
        // On met à jour la BDD ferie
        $ferie->updateFerie();
        // On rafraîchit le select pour afficher les mdifs faites en BDD
        $tabFerie = $ferie->selectAllFerie();
    }
}
if (isset($_POST['annuler'])) {// Cas du bouton vert "annuler"
    // Retour à la page d'accueil administrateur sans modification
    // die('<META HTTP-equiv="refresh" content=0;URL=admin_modif_plan.php>');
}
?>
<body>
<!-- Affichage des vacances -->
<div class="container-fluid col-lg-offset-2">
    <div class="col-lg-7">
        <table class="table table-bordered">
            <tr class="color-grey">
                <th class="thCentre">Vacances</th>
                <th class="thCentre">Date de début</th>
                <th class="thCentre">Date de fin</th>
            </tr>
            <br/>

            <?php
            for ($i = 0;
            $i < count($tabVacances);
            $i++) {
            ?>
            <form class="form-horizontal" method="POST" action="mod_VacancesJF.php">
                <tr>
                    <input type="hidden" name="idVacForm<?php echo $i; ?>"
                           value="<?php echo $tabVacances[$i]['idVac']; ?>">
                    <td>
                        <input size="10" class="form-control" disabled type="text" name="nomForm<?php echo $i; ?>"
                               value="<?php echo $tabVacances[$i]['nomVac']; ?>">
                    </td>
                    <td>
                        <input size="10" class="form-control" type="text" name="dateDebForm<?php echo $i; ?>"
                               value="<?php echo convertDateUsFr($tabVacances[$i]['dateDebVac']); ?>">
                    </td>
                    <td>
                        <input size="10" class="form-control" type="text" name="dateFinForm<?php echo $i; ?>"
                               value="<?php echo convertDateUsFr($tabVacances[$i]['dateFinVac']); ?>">
                    </td>
                </tr>
                <?php } ?>
                <!-- Affichage de 2 boutons -->
                <button type="submit" name="annuler" class="btn btn-success"
                        class="glyphicon glyphicon-ban-circle"><span
                        class="glyphicon glyphicon-ban-circle"></span> Annuler
                </button>
                <button type="submit" name="updateVac" class="btn btn-warning"><span
                        class="glyphicon glyphicon-floppy-open"></span> Enregistrer
                </button>
            </form>
        </table>
    </div>
</div>

<!-- Affichage des jours fériés -->
<div class="container-fluid col-lg-offset-2">
    <div class="col-lg-7">
        <table class="table table-bordered">
            <tr class="color-grey">
                <th class="thCentre">Jours fériés</th>
                <th class="thCentre">Date de début</th>
                <th class="thCentre">Date de fin</th>
            </tr>
            <br/>

            <?php
            $j = 0;
            for ($i = count($tabVacances);
            $i < (count($tabVacances) + count($tabFerie));
            $i++) {
            ?>
            <form class="form-horizontal" method="POST" action="mod_VacancesJF.php">
                <tr>
                    <input type="hidden" name="idFerieForm<?php echo $i; ?>"
                           value="<?php echo $tabFerie[$j]['idFerie']; ?>">
                    <td>
                        <input size="10" class="form-control" disabled type="text" name="nomForm<?php echo $i; ?>"
                               value="<?php echo $tabFerie[$j]['nomFerie']; ?>">
                    </td>
                    <td>
                        <input size="10" class="form-control" type="text" name="dateDebForm<?php echo $i; ?>"
                               value="<?php echo convertDateUsFr($tabFerie[$j]['dateDebFerie']); ?>">
                    </td>
                    <td>
                        <input size="10" class="form-control" type="text" name="dateFinForm<?php echo $i; ?>"
                               value="<?php
                               if (empty($tabFerie[$j]['dateFinFerie'])) {
                                   echo convertDateUsFr($tabFerie[$j]['dateDebFerie']);
                               } else {
                                   echo convertDateUsFr($tabFerie[$j]['dateFinFerie']);
                               }
                               ?>">

                    </td>
                </tr>
                <?php
                $j++;
                }
                ?>
                <!-- Affichage de 2 boutons -->
                <button type="submit" name="annuler" class="btn btn-success"><span
                        class="glyphicon glyphicon-ban-circle"></span> Annuler
                </button>
                <button type="submit" name="updateFerie" class="btn btn-warning"><span
                        class="glyphicon glyphicon-floppy-open"></span> Enregistrer
                </button>
            </form>
        </table>
    </div>
</div>
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