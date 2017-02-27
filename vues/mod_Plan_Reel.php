<?php
session_start(); // Utilisation des variables $_SESSION

require_once('../class/Agent.php');
require_once('../class/Poste.php');
require_once('../class/PlanStd.php');
require_once('../class/Horaire.php');
require_once('../class/PlanReel.php');
require_once('../class/Ferie.php');
require_once('../include/alice_fonctions.php');
require_once('../include/alice_dao.inc.php');
?>

<?php

include("../include/doctype.php");

$oPlanStd = new PlanStd();
$tabPlanStd = $oPlanStd->selectPlanStd();

$oPlanReel = new PlanReel();

if (isset($_POST['enregistrer'])) { // Cas du bouton orange "enregistrer"
//                 var_dump($_POST);
//                 die();
    $i = 0;
    while ($i < count($tabPlanStd)) {
        $j = 0;
        while ($j < count($tabPlanStd)) {
            if ($_POST['idAgentForm' . $i] == $tabPlanStd[$j]['idAgent'] &&
                $_POST['idJourForm' . $i] == $tabPlanStd[$j]['idJour'] &&
                $_POST['horaireDebForm' . $i] == $tabPlanStd[$j]['horaireDeb'] &&
                $_POST['horaireFinForm' . $i] == $tabPlanStd[$j]['horaireFin'] &&
                $_POST['idPosteForm' . $i] != $tabPlanStd[$j]['idPoste']
            ) {
                $oPlanReel->setIdAgent($_POST['idAgentForm' . $i]);
                $oPlanReel->setDateReel($_POST['dateReelForm' . $i]);
                $oPlanReel->setHoraireDeb($_POST['horaireDebForm' . $i]);
                $oPlanReel->setHoraireFin($_POST['horaireFinForm' . $i]);
                $oPlanReel->setIdPoste($_POST['idPosteForm' . $i]);

                $selectPlanReel = $oPlanReel->preUpdatePlanReel();

                if ($selectPlanReel != null) {
                    $oPlanReel->updatePlanReel();
                    $selectPlanReel = null;
                } else {
                    // On met à jour la BDD planReel
                    $oPlanReel->insertPlanReel();
                }
                $j = count($tabPlanStd);
            }
            $j++;
        }
        $i++;
    }
}

if (isset($_POST['annuler'])) {// Cas du bouton vert "annuler"
    // Retour à la page d'accueil administrateur sans modification
    // die('<META HTTP-equiv="refresh" content=0;URL=admin_modif_plan.php>');
}

$oAgent = new Agent();
$user = $oAgent->selectUser();

$plan = new PlanStd();
$tabPlanStd = $plan->selectPlanStd();
$compte = 0;
$t = 10;

if (!isset($_POST['precedente']) && !isset($_POST['suivante']) && !isset($_POST['enregistrer']) && !isset($_POST['annuler'])) {
    $_SESSION['weekNumber'] = date("W");
    $_SESSION['year'] = date("Y");
}


if (isset($_POST['precedente'])) {
    $_SESSION['weekNumber']--;
    if ($_SESSION['weekNumber'] < 1) {
        $_SESSION['weekNumber'] = 52;
        $_SESSION['year']--;
    }
}
if (isset($_POST['home'])) {
    $_SESSION['weekNumber'] = date("W");
}
if (isset($_POST['suivante'])) {
    $_SESSION['weekNumber']++;
    if ($_SESSION['weekNumber'] > 52) {
        $_SESSION['weekNumber'] = 1;
        $_SESSION['year']++;
    }
}


// Tableau des dates réelles du dimanche au samedi au format américain
$tabDatesJoursSemaines = datesJourSemaine($_SESSION['weekNumber'], $_SESSION['year']);

// Selection des plannings réél de la semaine
$planReel = $oPlanReel->selectReel($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);
$oFerie = new Ferie();
$jourFerie = $oFerie->selectFerie($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);

// Si $planReel contient un résultat, je remplace la date pas le numéro du jour de la semaine
if (isset($planReel) || isset($jourFerie)) {
    for ($i = 0; $i < count($planReel); $i++) {
        $planReel[$i]['dateReel'] = array_search($planReel[$i]['dateReel'], $tabDatesJoursSemaines);
    }
    for ($i = 0; $i < count($jourFerie); $i++) {
        $jourFerie[$i]['dateDebFerie'] = array_search($jourFerie[$i]['dateDebFerie'], $tabDatesJoursSemaines);
    }

//    Je remplace les données du planing standard par le planing réél (coulGroupe, idPoste, libPoste)
    for ($j = 0; $j < count($tabPlanStd); $j++) {
        for ($k = 0; $k < count($planReel); $k++) {
            if ($tabPlanStd[$j]['idAgent'] == $planReel[$k]['idAgent'] && $tabPlanStd[$j]['idJour'] == $planReel[$k]['dateReel'] && $tabPlanStd[$j]['horaireDeb'] == $planReel[$k]['horaireDeb'] && $tabPlanStd[$j]['horaireFin'] == $planReel[$k]['horaireFin']) {

                $tabPlanStd[$j]['libPoste'] = $planReel[$k]['libPoste'];
                $tabPlanStd[$j]['idPoste'] = $planReel[$k]['idPoste'];
                $tabPlanStd[$j]['coulGroupe'] = $planReel[$k]['coulGroupe'];

                $k = count($planReel);
            }
        }
    }
    for ($j = 0; $j < count($tabPlanStd); $j++) {
        for ($k = 0; $k < count($jourFerie); $k++) {
            if ($tabPlanStd[$j]['idJour'] == $jourFerie[$k]['dateDebFerie']) {

                $tabPlanStd[$j]['libPoste'] = "Ferie";
                $tabPlanStd[$j]['coulGroupe'] = null;
                $tabPlanStd[$j]['idPoste'] = null;

                $k = count($planReel);
            }
        }
    }
}

$oHoraire = new Horaire();
$time = $oHoraire->selectHoraire();

?>

<div class="col-lg-6">
    <div class="row">
        <div class="col-lg-offset-4 col-lg-4">
            <table class="table top-marge">
                <tr>
                    <td>
                        <form action="" method="post">
                            <input type="submit" value=" " name="precedente" class="leftArrow">
                        </form>
                    </td>
                    <td>
                        <form action="" method="post">
                            <input type="submit" value=" " name="home" class="house">
                        </form>
                    </td>
                    <td>
                        <form action="" method="post">
                            <input type="submit" value=" " name="suivante" class="rightArrow">
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <h2 class=" col-lg-offset-1 col-lg-3">
            <?php
            echo "<br />";
            echo "Semaine n°" . $_SESSION['weekNumber'];
            ?>
        </h2>
    </div>
    <div class="row">
        <h2 class="col-lg-offset-4 col-md-5">
            <?php
            echo "<br />";
            echo "<br />";
            echo "Semaine du " . convertDateUsFr($tabDatesJoursSemaines[1]) . " au " . convertDateUsFr($tabDatesJoursSemaines[6]);
            echo "<br />";
            ?>
        </h2>
    </div>
</div>

<?php include("../include/header_admin.php"); ?>

<body>
<div class="container-fluid">
    <table class="table table-bordered">
        <!--            Affichage des jours-->
        <tr class="color-grey text-size">
            <th></th>
            <th class="text-center" colspan="2">Lundi</th>
            <th class="text-center" colspan="2">Mardi</th>
            <th class="text-center" colspan="3">Mercredi</th>
            <th class="text-center" colspan="2">Jeudi</th>
            <th class="text-center" colspan="2">Vendredi</th>
            <th class="text-center" colspan="2">Samedi</th>
        </tr>
        <!--            Affichage des horraires -->
        <tr class="color-grey name-size">
            <td>Personnel</td>
            <?php

            $oHoraire = new Horaire();
            $time = $oHoraire->selectHoraire();

            for ($i = 0; $i < 4; $i++) {
                if ($i % 2 == 0) {
                    echo "<td class=\"text-center\">";
                    echo substr($time[1]['libHoraire'], 0, 5), " - ";
                    echo substr($time[3]['libHoraire'], 0, 5);
                    echo "</td>";
                }
                if ($i % 2 != 0) {
                    echo "<td class=\"text-center\">";
                    echo substr($time[3]['libHoraire'], 0, 5), " - ";
                    echo substr($time[6]['libHoraire'], 0, 5);
                    echo "</td>";
                }
            }
            echo "<td class=\"text-center\">";
            echo substr($time[0]['libHoraire'], 0, 5), " - ";
            echo substr($time[2]['libHoraire'], 0, 5);
            echo "</td>";
            echo "<td class=\"text-center\">";
            echo substr($time[2]['libHoraire'], 0, 5), " - ";
            echo substr($time[3]['libHoraire'], 0, 5);
            echo "</td>";
            echo "<td class=\"text-center\">";
            echo substr($time[3]['libHoraire'], 0, 5), " - ";
            echo substr($time[6]['libHoraire'], 0, 5);
            echo "</td>";
            for ($i = 0; $i < 4; $i++) {
                if ($i % 2 == 0) {
                    echo "<td class=\"text-center\">";
                    echo substr($time[1]['libHoraire'], 0, 5), " - ";
                    echo substr($time[3]['libHoraire'], 0, 5);
                    echo "</td>";
                }
                if ($i % 2 != 0) {
                    echo "<td class=\"text-center\">";
                    echo substr($time[3]['libHoraire'], 0, 5), " - ";
                    echo substr($time[6]['libHoraire'], 0, 5);
                    echo "</td>";
                }
            }
            echo "<td class=\"text-center\">";
            echo substr($time[0]['libHoraire'], 0, 5), " - ";
            echo substr($time[2]['libHoraire'], 0, 5);
            echo "</td>";
            echo "<td class=\"text-center\">";
            echo substr($time[2]['libHoraire'], 0, 5), " - ";
            echo substr($time[4]['libHoraire'], 0, 5);
            echo "</td>";
            ?>
        </tr>
        <form class="form-horizontal" action="mod_Plan_Reel.php" method="post">
            <?php

            $oPoste = new Poste();
            $poste = $oPoste->selectAllPoste();

            $i = 0;
            $l = 0;

            while ($i < count($tabPlanStd)) { ?>
                <tr>
                    <td>
                        <?php echo $tabPlanStd[$i]['prenom']; ?>
                    </td>
                    <?php for ($j = 0;
                               $j < 13;
                               $j++) { ?>
                        <td>
                            <?php

                            switch ($j) {

                                case 0:
                                case 1: ?>
                                    <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                           value="<?php echo $tabDatesJoursSemaines[1]; ?>">
                                    <?php break;
                                case 2:
                                case 3: ?>
                                    <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                           value="<?php echo $tabDatesJoursSemaines[2]; ?>">
                                    <?php break;
                                case 4:
                                case 5:
                                case 6: ?>
                                    <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                           value="<?php echo $tabDatesJoursSemaines[3]; ?>">
                                    <?php break;
                                case 7:
                                case 8:
                                    ?>
                                    <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                           value="<?php echo $tabDatesJoursSemaines[4]; ?>">
                                    <?php break;
                                case 9:
                                case 10:
                                    ?>
                                    <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                           value="<?php echo $tabDatesJoursSemaines[5]; ?>">
                                    <?php break;
                                case 11:
                                case 12: ?>
                                    <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                           value="<?php echo $tabDatesJoursSemaines[6]; ?>">
                                    <?php break;
                            } ?>

                            <input type="hidden" name="idAgentForm<?php echo $l; ?>"
                                   value="<?php echo $tabPlanStd[$i]['idAgent']; ?>">
                            <input type="hidden" name="idJourForm<?php echo $l; ?>"
                                   value="<?php echo $tabPlanStd[$i]['idJour']; ?>">
                            <input type="hidden" name="horaireDebForm<?php echo $l; ?>"
                                   value="<?php echo $tabPlanStd[$i]['horaireDeb']; ?>">
                            <input type="hidden" name="horaireFinForm<?php echo $l; ?>"
                                   value="<?php echo $tabPlanStd[$i]['horaireFin']; ?>">

                            <select id="selectPlan" name="idPosteForm<?php echo $l; ?>" class="form-control"
                                    onchange="changeColor()">
                                <?php for ($k = 0; $k < count($poste); $k++) {
                                    if ($poste[$k]['idPoste'] == $tabPlanStd[$i]['idPoste']) { ?>

                                        <option value="<?php echo $poste[$k]['idPoste']; ?>"
                                                selected=""
                                                style="background-color: <?php echo $poste[$k]['coulGroupe'] ?>"><?php echo $poste[$k]['libPoste']; ?></option>

                                    <?php } else { ?>

                                        <option
                                            value="<?php echo $poste[$k]['idPoste']; ?>"
                                            style="background-color: <?php echo $poste[$k]['coulGroupe'] ?>"><?php echo $poste[$k]['libPoste']; ?></option>

                                    <?php }
                                } ?>
                            </select>
                            <?php $i++;
                            $l++; ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            <div class="col-lg-offset-10 col-lg-2">
                <!-- Affichage de 2 boutons -->
                <button type="submit" name="annuler" class="btn btn-success"
                        class="glyphicon glyphicon-ban-circle"><span
                        class="glyphicon glyphicon-ban-circle"></span> Annuler
                </button>
                <button type="submit" name="enregistrer" class="btn btn-warning"><span
                        class="glyphicon glyphicon-floppy-open"></span> Enregistrer
                </button>
            </div>
        </form>
    </table>
</div>


</body>
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