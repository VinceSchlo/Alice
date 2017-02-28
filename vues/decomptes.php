<?php
session_start(); // Utilisation des variables $_SESSION

require_once('../class/Agent.php');
require_once('../class/PlanStd.php');
require_once('../class/PlanReel.php');
require_once('../class/Ferie.php');
require_once('../class/Horaire.php');
require_once('../include/alice_dao.inc.php');
require_once('../include/alice_fonctions.php');
//
?>
<?php include("../include/doctype.php"); ?>

<!-- Affichage du titre de la page -->
<div class="col-lg-offset-2 col-lg-4">
    <h2>Temps de service public et samedis travaillés</h2>
</div>

<?php
include("../include/header_admin.php");

$oAgent = new Agent();
$tabAgent = $oAgent->selectUser();
$oPlanStd = new PlanStd();
$tabPlanStd = $oPlanStd->selectPlanStd();

$compte = 0;
$t = 10;

if (!isset($_POST['precedente']) && !isset($_POST['suivante'])) {
    $_SESSION['weekNumber'] = ltrim(date("W"), "0");
    $_SESSION['year'] = date("Y");
}

if (isset($_POST['precedente'])) {
    $_SESSION['weekNumber'] --;
    if ($_SESSION['weekNumber'] < 1) {
        $_SESSION['weekNumber'] = 52;
        $_SESSION['year'] --;
    }
}

if (isset($_POST['home'])) {
    $_SESSION['weekNumber'] = ltrim(date("W"), "0");
}

if (isset($_POST['suivante'])) {
    $_SESSION['weekNumber'] ++;
    if ($_SESSION['weekNumber'] > 52) {
        $_SESSION['weekNumber'] = 1;
        $_SESSION['year'] ++;
    }
}

// Tableau des dates réelles du dimanche au samedi au format américain
$tabDatesJoursSemaines = datesJourSemaine($_SESSION['weekNumber'], $_SESSION['year']);

// Selection des plannings réels de la semaine
$oPlanReel = new PlanReel();
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
    }

// Je remplace les données du planning standard par le planing réel (coulGroupe, idPoste, libPoste)
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

                $tabPlanStd[$j]['libPoste'] = "Férié";
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

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2">
                <img class="logo" src="images/logo_sna_quadri.png"/>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-offset-4 col-lg-3">
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
                    <h2 class="col-lg-2">
                        <?php
                        echo "<br />";
                        if ($_SESSION['weekNumber'] < 10) {
                            echo "Semaine n°" . "0" . $_SESSION['weekNumber'];
                        } else {
                            echo "Semaine n°" . $_SESSION['weekNumber'];
                        }
                        ?>
                    </h2>
                </div>
                <div class="row">
                    <h2 class="col-lg-offset-4 col-md-4">
                        <?php
                        echo "<br />";
                        echo "<br />";
                        echo "Semaine du " . convertDateUsFr($tabDatesJoursSemaines[1]) . " au " . convertDateUsFr($tabDatesJoursSemaines[6]);
                        echo "<br />";
                        ?>
                    </h2>
                </div>
            </div>
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