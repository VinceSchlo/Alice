<?php
session_start(); // Utilisation des variables $_SESSION

require_once('../class/Agent.php');
require_once('../class/Poste.php');
require_once('../class/PlanStd.php');
require_once('../class/Horaire.php');
require_once('../class/PlanReel.php');
require_once('../class/Ferie.php');
require_once('../class/Vacances.php');
require_once('../include/alice_fonctions.php');
require_once('../include/alice_dao.inc.php');
?>

<?php
include("../include/doctype.php");

$oPlanStd = new PlanStd();
$tabPlanStd = $oPlanStd->selectPlanStd();

$oVacances = new Vacances();
$listeVacances = $oVacances->selectAllVacances();

$oFerie = new Ferie();
$listeFerie = $oFerie->selectAllFerie();

$anneeVac = substr($listeVacances[1]['dateDebVac'], 0, 4);
$anneeFerie = substr($listeFerie[0]['dateDebFerie'], 0, 4);

$annee = date('Y');

if ($anneeVac < $annee || $anneeFerie < $annee) {
    ?>
    <script>
        alert("Pensez à mettre à jour les dates des vacances et des jours fériés pour l'année <?php echo $annee ?>");
    </script>
    <?php
}

$oPlanReel = new PlanReel();
if (isset($_POST['enregistrer'])) { // Cas du bouton orange "enregistrer"
// var_dump($_POST);
// die();
    $i = 0;
    while ($i < count($tabPlanStd)) {
        $j = 0;
        while ($j < count($tabPlanStd)) {
            if ($_POST['idAgentForm' . $i] == $tabPlanStd[$j]['idAgent'] &&
                $_POST['idJourForm' . $i] == $tabPlanStd[$j]['idJour'] &&
                $_POST['horaireDebForm' . $i] == $tabPlanStd[$j]['horaireDeb'] &&
                $_POST['horaireFinForm' . $i] == $tabPlanStd[$j]['horaireFin'] &&
                $_POST['idPosteForm' . $i] != $tabPlanStd[$j]['idPoste'] &&
                $_POST['idPosteForm' . $i] != "Férié"
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
            } else if ($_POST['idAgentForm' . $i] == $tabPlanStd[$j]['idAgent'] &&
                $_POST['idJourForm' . $i] == $tabPlanStd[$j]['idJour'] &&
                $_POST['horaireDebForm' . $i] == $tabPlanStd[$j]['horaireDeb'] &&
                $_POST['horaireFinForm' . $i] == $tabPlanStd[$j]['horaireFin'] &&
                $_POST['idPosteForm' . $i] == $tabPlanStd[$j]['idPoste'] &&
                $_POST['idPosteForm' . $i] != "Férié"
            ) {
                $oPlanReel->setIdAgent($_POST['idAgentForm' . $i]);
                $oPlanReel->setDateReel($_POST['dateReelForm' . $i]);
                $oPlanReel->setHoraireDeb($_POST['horaireDebForm' . $i]);
                $oPlanReel->setHoraireFin($_POST['horaireFinForm' . $i]);
                $oPlanReel->setIdPoste($_POST['idPosteForm' . $i]);

                $oPlanReel->deletePlanReel();
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

$oPlan = new PlanStd();
$tabPlanStd = $oPlan->selectPlanStd();
$listePlanStd = $oPlan->selectPlanStd();
$compte = 0;
$t = 10;

if (!isset($_POST['precedente']) && !isset($_POST['suivante']) && !isset($_POST['enregistrer']) && !isset($_POST['annuler'])) {
    $_SESSION['weekNumber'] = ltrim(date("W"), "0");
    $_SESSION['year'] = date("Y"); // L'année est au format "2017"
}

if (isset($_POST['precedente'])) {
    $_SESSION['weekNumber']--;
    if ($_SESSION['weekNumber'] < 1) {
        $_SESSION['weekNumber'] = 52;
        $_SESSION['year']--; // L'année est au format "2017"
    }
}

if (isset($_POST['home'])) {
    $_SESSION['weekNumber'] = ltrim(date("W"), "0");
}

if (isset($_POST['suivante'])) {
    $_SESSION['weekNumber']++;
    if ($_SESSION['weekNumber'] > 52) {
        $_SESSION['weekNumber'] = 1;
        $_SESSION['year']++; // L'année est au format "2017"
    }
}

if (isset($_POST['btnCalendar'])) {
    $_SESSION['weekNumber'] = ltrim(date('W', strtotime($_POST['dateCalendrier'])), "0");
    $_SESSION['year'] = date('Y', strtotime($_POST['dateCalendrier']));
}

// Tableau des dates réelles du dimanche au samedi au format américain
$tabDatesJoursSemaines = datesJourSemaine($_SESSION['weekNumber'], $_SESSION['year']);

// Selection des plannings réels de la semaine
$planReel = $oPlanReel->selectPlanReel($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);
$oFerie = new Ferie();
$jourFerie = $oFerie->selectFerie($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);

// Si $planReel contient un résultat, je remplace la date par le numéro du jour de la semaine
if (isset($planReel) || isset($jourFerie)) {
    for ($i = 0; $i < count($planReel); $i++) {
        $planReel[$i]['dateReel'] = array_search($planReel[$i]['dateReel'], $tabDatesJoursSemaines);
    }
    for ($i = 0; $i < count($jourFerie); $i++) {
        $jourFerie[$i]['dateDebFerie'] = array_search($jourFerie[$i]['dateDebFerie'], $tabDatesJoursSemaines);
        $jourFerie[$i]['dateFinFerie'] = array_search($jourFerie[$i]['dateFinFerie'], $tabDatesJoursSemaines);
    }

// Je remplace les données du planing standard par le planing réel (coulGroupe, idPoste, libPoste)
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
            if ($tabPlanStd[$j]['idJour'] >= $jourFerie[$k]['dateDebFerie'] && $tabPlanStd[$j]['idJour'] <= $jourFerie[$k]['dateFinFerie']) {

                $tabPlanStd[$j]['libPoste'] = "Férié";
                $tabPlanStd[$j]['coulGroupe'] = null;
                $tabPlanStd[$j]['idPoste'] = 22;

                $k = count($planReel);
            }
        }
    }
}
//
$oHoraire = new Horaire();
$time = $oHoraire->selectHoraire();
?>
<div class="col-md-5">
    <div class="row">
        <div class="col-md-offset-4 col-md-4">
            <table class="table">
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
    </div>
    <div class="row">
        <div class="center-block border-table">
            <h2>Modification du planning réel</h2>
        </div>
    </div>
    <div class="row">
        <div class="center-block">
            <h3>
                <?php
                if ($_SESSION['weekNumber'] < 10) {
                    echo "Semaine n°" . "0" . $_SESSION['weekNumber'] . " du " . convertDateUsFr($tabDatesJoursSemaines[1]) . " au " . convertDateUsFr($tabDatesJoursSemaines[6]);
                } else {
                    echo "Semaine n°" . $_SESSION['weekNumber'] . " du " . convertDateUsFr($tabDatesJoursSemaines[1]) . " au " . convertDateUsFr($tabDatesJoursSemaines[6]);
                }
                ?>
            </h3>
        </div>
    </div>
</div>
<div class="col-md-3 ">
    <div class="row">
        <div class="pull-left calendar-position">
            <div id="calendarMain" class="calendarMain"></div>
        </div>

        <script type="text/javascript">
            //<![CDATA[
            var myCalendar = new jsSimpleDatePickr();
            myCalendar.CalAdd({
                'divId': 'calendarMain',
                'inputFieldId': 'dateCalendrier',
                'dateMask': 'AAAA-MM-JJ',
                'dateCentury': 20,
                'titleMask': 'M AAAA',
                'navType': '01',
                'classTable': 'jsCalendar',
                'classDay': 'day',
                'classDaySelected': 'selectedDay',
                'monthLst': ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                'dayLst': ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
                'hideOnClick': false,
                'showOnLaunch': true
            });
            //]]>
        </script>

        <div class="btn-calendar-position">
            <form action="mod_Plan_Reel.php" method="post">
                <input type="text" hidden id="dateCalendrier" name="dateCalendrier">
                <button type="submit" class="btn btn-primary" name="btnCalendar"><span
                        class="glyphicon glyphicon-fast-forward"></span> Allez à
                </button>
            </form>
        </div>
    </div>
</div>

<?php include("../include/header_admin.php"); ?>

<div class="container-fluid background-color-admin">
    <table class="table border-table" id="table2">
        <thead class="theadFHPlan">
        <!--            Affichage des jours-->
        <tr class="color-grey size-hour">
            <th class=" border-right"></th>
            <th class="text-center border-right" colspan="2">
                Lundi <?php echo substr(convertDateUsFr($tabDatesJoursSemaines[1]), 0, 5) ?></th>
            <th class="text-center border-right" colspan="2">
                Mardi <?php echo substr(convertDateUsFr($tabDatesJoursSemaines[2]), 0, 5) ?></th>
            <th class="text-center border-right" colspan="3">
                Mercredi <?php echo substr(convertDateUsFr($tabDatesJoursSemaines[3]), 0, 5) ?></th>
            <th class="text-center border-right" colspan="2">
                Jeudi <?php echo substr(convertDateUsFr($tabDatesJoursSemaines[4]), 0, 5) ?></th>
            <th class="text-center border-right" colspan="2">
                Vendredi <?php echo substr(convertDateUsFr($tabDatesJoursSemaines[5]), 0, 5) ?></th>
            <th class="text-center border-right" colspan="2">
                Samedi <?php echo substr(convertDateUsFr($tabDatesJoursSemaines[6]), 0, 5) ?></th>
        </tr>
        <!--            Affichage des horaires -->
        <tr class="color-grey border-right name-size-admin">
            <td class="border-right">Personnel</td>
            <?php
            $oHoraire = new Horaire();
            $time = $oHoraire->selectHoraire();

            for ($i = 0; $i < 4; $i++) {
                if ($i % 2 == 0) {
                    echo "<td class=\"text-center border-top-bot\">";
                    echo substr($time[1]['libHoraire'], 0, 5), " - ";
                    echo substr($time[3]['libHoraire'], 0, 5);
                    echo "</td>";
                }
                if ($i % 2 != 0) {
                    echo "<td class=\"text-center border-right\">";
                    echo substr($time[3]['libHoraire'], 0, 5), " - ";
                    echo substr($time[6]['libHoraire'], 0, 5);
                    echo "</td>";
                }
            }
            echo "<td class=\"text-center border-top-bot\">";
            echo substr($time[0]['libHoraire'], 0, 5), " - ";
            echo substr($time[2]['libHoraire'], 0, 5);
            echo "</td>";
            echo "<td class=\"text-center border-top-bot\">";
            echo substr($time[2]['libHoraire'], 0, 5), " - ";
            echo substr($time[3]['libHoraire'], 0, 5);
            echo "</td>";
            echo "<td class=\"text-center border-right\">";
            echo substr($time[3]['libHoraire'], 0, 5), " - ";
            echo substr($time[6]['libHoraire'], 0, 5);
            echo "</td>";
            for ($i = 0; $i < 4; $i++) {
                if ($i % 2 == 0) {
                    echo "<td class=\"text-center border-top-bot\">";
                    echo substr($time[1]['libHoraire'], 0, 5), " - ";
                    echo substr($time[3]['libHoraire'], 0, 5);
                    echo "</td>";
                }
                if ($i % 2 != 0) {
                    echo "<td class=\"text-center border-right\">";
                    echo substr($time[3]['libHoraire'], 0, 5), " - ";
                    echo substr($time[6]['libHoraire'], 0, 5);
                    echo "</td>";
                }
            }
            echo "<td class=\"text-center border-top-bot\">";
            echo substr($time[0]['libHoraire'], 0, 5), " - ";
            echo substr($time[2]['libHoraire'], 0, 5);
            echo "</td>";
            echo "<td class=\"text-center border-top-bot\">";
            echo substr($time[2]['libHoraire'], 0, 5), " - ";
            echo substr($time[4]['libHoraire'], 0, 5);
            echo "</td>";
            ?>
        </tr>
        </thead>
        <tbody>
        <form class="form-horizontal background-color-admin" action="mod_Plan_Reel.php" method="post">
            <?php
            $oPoste = new Poste();
            $poste = $oPoste->selectAllPoste();
            $nbrPoste = count($poste);
            $i = 0;
            $l = 0;
            while ($i < count($tabPlanStd)) {
                ?>
                <tr>
                    <td class="border-right name-size-admin color-grey">
                        <?php echo $tabPlanStd[$i]['prenom']; ?>
                    </td>
                    <?php
                    for ($j = 0; $j < 13; $j++) {
                        switch ($j) {
                            case 1:
                            case 3:
                            case 6:
                            case 8:
                            case 10:
                            case 12:
                                echo "<td class='text-center border-right'>";
                                break;
                            default:
                                echo "<td class='text-center border-top-bot'>";
                                break;
                        }

                        switch ($j) {

                            case 0:
                            case 1:
                                ?>
                                <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                       value="<?php echo $tabDatesJoursSemaines[1]; ?>">
                                <?php
                                break;
                            case 2:
                            case 3:
                                ?>
                                <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                       value="<?php echo $tabDatesJoursSemaines[2]; ?>">
                                <?php
                                break;
                            case 4:
                            case 5:
                            case 6:
                                ?>
                                <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                       value="<?php echo $tabDatesJoursSemaines[3]; ?>">
                                <?php
                                break;
                            case 7:
                            case 8:
                                ?>
                                <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                       value="<?php echo $tabDatesJoursSemaines[4]; ?>">
                                <?php
                                break;
                            case 9:
                            case 10:
                                ?>
                                <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                       value="<?php echo $tabDatesJoursSemaines[5]; ?>">
                                <?php
                                break;
                            case 11:
                            case 12:
                                ?>
                                <input type="hidden" name="dateReelForm<?php echo $l; ?>"
                                       value="<?php echo $tabDatesJoursSemaines[6]; ?>">
                                <?php
                                break;
                        }
                        ?>

                        <input type="hidden" name="idAgentForm<?php echo $l; ?>"
                               value="<?php echo $tabPlanStd[$i]['idAgent']; ?>">
                        <input type="hidden" name="idJourForm<?php echo $l; ?>"
                               value="<?php echo $tabPlanStd[$i]['idJour']; ?>">
                        <input type="hidden" name="horaireDebForm<?php echo $l; ?>"
                               value="<?php echo $tabPlanStd[$i]['horaireDeb']; ?>">
                        <input type="hidden" name="horaireFinForm<?php echo $l; ?>"
                               value="<?php echo $tabPlanStd[$i]['horaireFin']; ?>">

                        <?php
                        switch ($tabPlanStd[$i]['libPoste']) {
                            case "Férié":
                                ?>
                                <input type="hidden" name="idPosteForm<?php echo $l; ?>"
                                       value="<?php echo $tabPlanStd[$i]['libPoste']; ?>">

                                <select disabled style="width: 100%; font-weight: bold" class="form-control">

                                    <option value="<?php echo $tabPlanStd[$i]['libPoste']; ?>"
                                            selected=""><?php echo $tabPlanStd[$i]['libPoste']; ?></option>
                                </select>
                                <?php
                                break;
                            default:
                                for ($k = 0; $k < $nbrPoste; $k++) {
                                    if ($poste[$k]['idPoste'] == $tabPlanStd[$i]['idPoste']) {
                                        $couleur = $tabPlanStd[$i]['coulGroupe'];
                                    }
                                }
                                ?>
                                <!-- Liste contenant tout les postes -->
                                <select id="selectPlan<?php echo $l; ?>" name="idPosteForm<?php echo $l; ?>"
                                        class="form-control" onchange="changeColor<?php echo $l; ?>()"
                                        style="background-color: <?php echo $couleur ?>; font-weight: bold">

                                    <!-- Javascript pour changer la couleur du select en fonction du poste choisi -->
                                    <script type="text/javascript">
                                        function changeColor<?php echo $l; ?>() {
                                            var selectPlan = document.getElementById("selectPlan<?php echo $l; ?>");
                                            selectPlan.style.backgroundColor = selectPlan.options[selectPlan.selectedIndex].style.backgroundColor;
                                        }
                                    </script>

                                    <!-- Pour mettre le poste attribué en "selected" -->
                                    <?php
                                    for ($k = 0; $k < $nbrPoste; $k++) {
                                        if ($poste[$k]['idPoste'] == $tabPlanStd[$i]['idPoste']) {
                                            if ($tabPlanStd[$i]['idPoste'] == $listePlanStd[$i]['idPoste']) { ?>
                                                <option value="<?php echo $poste[$k]['idPoste']; ?>"
                                                        selected=""
                                                        style="background-color: <?php echo $poste[$k]['coulGroupe'] ?>; font-weight: bold; border: double;"><?php echo $poste[$k]['libPoste']; ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo $poste[$k]['idPoste']; ?>"
                                                        selected=""
                                                        style="background-color: <?php echo $poste[$k]['coulGroupe'] ?>; font-weight: bold;"><?php echo $poste[$k]['libPoste']; ?></option>
                                            <?php }
                                        } else if ($poste[$k]['idPoste'] == $listePlanStd[$i]['idPoste']) { ?>
                                            <option value="<?php echo $poste[$k]['idPoste']; ?>"
                                                    style="background-color: <?php echo $poste[$k]['coulGroupe'] ?>; font-weight: bold; border: double;"><?php echo $poste[$k]['libPoste']; ?></option>
                                            <?php
                                        } else { ?>
                                            <option value="<?php echo $poste[$k]['idPoste']; ?>"
                                                    style="background-color: <?php echo $poste[$k]['coulGroupe'] ?>; font-weight: bold"><?php echo $poste[$k]['libPoste']; ?></option>
                                        <?php }
                                    }
                                    ?>
                                </select>
                                <?php
                                break;
                        }
                        $i++;
                        $l++;
                        ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>

            <!-- Affichage des 2 boutons -->
            <div class="col-md-3 pull-right text-right">
                <button type="submit" name="annuler" class="btn btn-success"
                        class="glyphicon glyphicon-ban-circle"><span
                        class="glyphicon glyphicon-ban-circle"></span> Annuler
                </button>
                <button type="submit" name="enregistrer" onclick="toast('Enregistrement en cours', '#ec971f')"
                        class="btn btn-warning"><span
                        class="glyphicon glyphicon-floppy-open"></span> Enregistrer
                </button>
            </div>
        </form>
        <tbody>
    </table>
</div>

<!-- Header flottant -->
<script type="text/javascript">
    var table = document.getElementById('table2');
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