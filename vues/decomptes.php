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
<?php
include("../include/doctype.php");

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

$oAgent = new Agent();
$tabAgent = $oAgent->selectUser();
$oPlanStd = new PlanStd();
$tabPlanStd = $oPlanStd->selectDecPlanStd();

// Sélection des plannings réels de la semaine
$oPlanReel = new PlanReel();
$tabPlanReel = $oPlanReel->selectDecPlanReel($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);

// Sélection des jours fériés
$oFerie = new Ferie();
$jourFerie = $oFerie->selectFerie($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);

// Si $tabPlanReel contient un résultat, je remplace la date par le numéro du jour de la semaine
if (isset($tabPlanReel) || isset($jourFerie)) {
    for ($i = 0; $i < count($tabPlanReel); $i++) {
        $tabPlanReel[$i]['dateReel'] = array_search($tabPlanReel[$i]['dateReel'], $tabDatesJoursSemaines);
    }
    for ($i = 0; $i < count($jourFerie); $i++) {
        $jourFerie[$i]['dateDebFerie'] = array_search($jourFerie[$i]['dateDebFerie'], $tabDatesJoursSemaines);
    }

    /* Je remplace les données du planning standard par le planning réel (idPoste, idGroupe)
      for ($j = 0; $j < count($tabPlanStd); $j++) {
      for ($k = 0; $k < count($tabPlanReel); $k++) {
      if ($tabPlanStd[$j]['idAgent'] == $tabPlanReel[$k]['idAgent'] && $tabPlanStd[$j]['idJour'] == $tabPlanReel[$k]['dateReel'] && $tabPlanStd[$j]['horaireDeb'] == $tabPlanReel[$k]['horaireDeb'] && $tabPlanStd[$j]['horaireFin'] == $tabPlanReel[$k]['horaireFin']) {

      $tabPlanStd[$j]['idPoste'] = $tabPlanReel[$k]['idPoste'];
      $tabPlanStd[$j]['idGroupe'] = $tabPlanReel[$k]['idGroupe'];

      $k = count($tabPlanReel);
      }
      }
      }
     */

    // Je remplace les données du planning standard par le planning réel (idPoste, idGroupe)
    $i = 0;
    for ($j = 0; $j < count($tabPlanStd); $j++) {
        for ($k = 0; $k < count($tabPlanReel); $k++) {
            if ($tabPlanStd[$j]['idAgent'] == $tabPlanReel[$k]['idAgent'] && $tabPlanStd[$j]['idJour'] == $tabPlanReel[$k]['dateReel'] && $tabPlanStd[$j]['horaireDeb'] == $tabPlanReel[$k]['horaireDeb'] && $tabPlanStd[$j]['horaireFin'] == $tabPlanReel[$k]['horaireFin']) {

                $tabPlanStd[$i]['idPoste'] = $tabPlanReel[$k]['idPoste'];
                $tabPlanStd[$i]['idGroupe'] = $tabPlanReel[$k]['idGroupe'];
                $k = count($tabPlanReel);
            } else {
                $tabPlanSum[$i]['idAgent'] = $tabPlanReel[$k]['idAgent'];
                $tabPlanSum[$i]['idJour'] = $tabPlanReel[$k]['dateReel'];
                $tabPlanSum[$i]['idPoste'] = $tabPlanReel[$k]['idPoste'];
                $tabPlanSum[$i]['idGroupe'] = $tabPlanReel[$k]['idGroupe'];
                $tabPlanSum[$i]['horaireDeb'] = $tabPlanReel[$k]['horaireDeb'];
                $tabPlanSum[$i]['horaireFin'] = $tabPlanReel[$k]['horaireDeb'];
               
            }
        }
        $i++;
    }

    for ($j = 0; $j < count($tabPlanStd); $j++) { // Cas des jours fériés
        for ($k = 0; $k < count($jourFerie); $k++) {
            if ($tabPlanStd[$j]['idJour'] == $jourFerie[$k]['dateDebFerie']) {

                $tabPlanStd[$j]['idPoste'] = null;
                $tabPlanStd[$j]['idGroupe'] = null;

                $k = count($tabPlanReel);
            }
        }
    }
}
var_dump($tabPlanSum);
exit();
// On remplace dans $tabPlanStd, les idHoraires par les vrais horaires de la table horaire en les convertissant en float
// 13:30 devient 13.5 pour faciliter les calculs entre HoraireDebut et HoraireFin
$oHoraire = new Horaire();
$tabHoraires = $oHoraire->selectHoraire();

foreach ($tabPlanStd as $key => $value) {
    $posHoraireDeb = array_search($tabPlanStd[$key]['horaireDeb'], array_column($tabHoraires, 'idHoraire'), true);
    $posHoraireFin = array_search($tabPlanStd[$key]['horaireFin'], array_column($tabHoraires, 'idHoraire'), true);
    $tabPlanStd[$key]['horaireDeb'] = convertTimeStringToNumber($tabHoraires[$posHoraireDeb]['libHoraire']);
    $tabPlanStd[$key]['horaireFin'] = convertTimeStringToNumber($tabHoraires[$posHoraireFin]['libHoraire']);
}

// Calcul des heures de service public à partir du tableau $tabPlanStd
$i = 0;
$nbHeuresSp = 0;
$tabDecHeuresSp = array();

foreach ($tabPlanStd as $key => $value) {
    $tabDecHeuresSp[$i]['idAgent'] = $tabPlanStd[$key]['idAgent'];
    $tabDecHeuresSp[$i]['prenom'] = $tabPlanStd[$key]['prenom'];
    if ($tabPlanStd[$key]['idGroupe'] != null) {
        $nbHeuresSp = $tabPlanStd[$key]['horaireFin'] - $tabPlanStd[$key]['horaireDeb'];
        $tabDecHeuresSp[$i]['nbHeuresSP'] = $nbHeuresSp;
        // Cas de l'annexe qui finit à 18h au lieu de 18h30
        if ($tabPlanStd[$key]['idPoste'] == "17" && $tabPlanStd[$key]['horaireFin'] == 18.5) {
            $tabDecHeuresSp[$i]['nbHeuresSP'] -= 0.5;
        }
    }
    $i++;
}
//var_dump($tabDecHeuresSp);
//exit();

/*
  for ($i = 0; $i < count($tabPlanStd); $i++) {

  }
 */
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
        <h2 class="col-lg-offset-1 col-lg-3">         
<?php
if ($_SESSION['weekNumber'] < 10) {
    echo "Semaine n°" . "0" . $_SESSION['weekNumber'];
} else {
    echo "Semaine n°" . $_SESSION['weekNumber'];
}
?>
        </h2>
    </div>
    <div class="row">
        <h2 class="col-lg-offset-3 col-lg-10">
<?php
echo "Semaine du " . convertDateUsFr($tabDatesJoursSemaines[1]) . " au " . convertDateUsFr($tabDatesJoursSemaines[6]);
?>
        </h2>
    </div>
</div>
<?php include("../include/header_admin.php"); ?>
<!-- Affichage du titre de la page -->
<div class="col-lg-offset-3 col-lg-6">
    <h2>Temps de service public et samedis travaillés</h2>
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