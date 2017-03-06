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
    $_SESSION['year'] = date("Y"); // L'année est au format "2017"
}

if (isset($_POST['precedente'])) {
    $_SESSION['weekNumber'] --;
    if ($_SESSION['weekNumber'] < 1) {
        $_SESSION['weekNumber'] = 52;
        $_SESSION['year'] --; // L'année est au format "2017"
    }
}

if (isset($_POST['home'])) {
    $_SESSION['weekNumber'] = ltrim(date("W"), "0");
}

if (isset($_POST['suivante'])) {
    $_SESSION['weekNumber'] ++;
    if ($_SESSION['weekNumber'] > 52) {
        $_SESSION['weekNumber'] = 1;
        $_SESSION['year'] ++; // L'année est au format "2017"
    }
}

// Tableau des dates réelles du dimanche au samedi au format américain
$tabDatesJoursSemaines = datesJourSemaine($_SESSION['weekNumber'], $_SESSION['year']);

// Sélection des plannings stardards de la semaine
$oPlanStd = new PlanStd();
$tabPlanStd = $oPlanStd->selectPlanStdDecSp();

// Sélection des plannings réels de la semaine
$oPlanReel = new PlanReel();
$tabPlanReel = $oPlanReel->selectPlanReelDecSp($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);

// Sélection des jours fériés
$oFerie = new Ferie();
$tabJourFerie = $oFerie->selectFerie($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);

// Si $tabPlanReel contient un résultat, je remplace la date par le numéro du jour de la semaine
if (!empty($tabPlanReel)) {
    foreach ($tabPlanReel as $key => $value) {
        $tabPlanReel[$key]['dateReel'] = array_search($tabPlanReel[$key]['dateReel'], $tabDatesJoursSemaines);
    }
    // On prévoit le cas où il n'y a pas de jours fériés pour initialiser certaines variables qui vont ensuite servir dans les tests
    if (empty($tabJourFerie)) {
        $idJourDebFerie = 7;
        $idJourFinFerie = 0;
    } else {
        // S'il y a des jours fériés, on assigne dans 2 variables, le chiffre de la semaine correspondant
        $idJourDebFerie = array_search($tabJourFerie[0]['dateDebFerie'], $tabDatesJoursSemaines);
        $idJourFinFerie = array_search($tabJourFerie[0]['dateFinFerie'], $tabDatesJoursSemaines);
    }
    // On remplace les données du planning standard par le planning réel (idPoste)
    for ($j = 0; $j < count($tabPlanStd); $j++) {
        for ($k = 0; $k < count($tabPlanReel); $k++) {
            // On vérifie si les jours sont non compris dans les jours fériés (cas des ponts)
            if ($tabPlanReel[$k]['dateReel'] < $idJourDebFerie || $tabPlanReel[$k]['dateReel'] > $idJourFinFerie) {
                if ($tabPlanStd[$j]['idAgent'] == $tabPlanReel[$k]['idAgent'] && $tabPlanStd[$j]['idJour'] == $tabPlanReel[$k]['dateReel'] && $tabPlanStd[$j]['horaireDeb'] == $tabPlanReel[$k]['horaireDeb'] && $tabPlanStd[$j]['horaireFin'] == $tabPlanReel[$k]['horaireFin']) {
                    $tabPlanStd[$i]['idPoste'] = $tabPlanReel[$k]['idPoste'];
                    $k = count($tabPlanReel);
                } else { // On enregistre dans un nouveau tableau, le planning réel qui n'a pas été reporté dans le std
                    $tabPlanSum[$k]['idAgent'] = $tabPlanReel[$k]['idAgent'];
                    $tabPlanSum[$k]['idJour'] = $tabPlanReel[$k]['dateReel'];
                    $tabPlanSum[$k]['idPoste'] = $tabPlanReel[$k]['idPoste'];
                    $tabPlanSum[$k]['horaireDeb'] = $tabPlanReel[$k]['horaireDeb'];
                    $tabPlanSum[$k]['horaireFin'] = $tabPlanReel[$k]['horaireFin'];
                }
            }
        }
    }
}
// On regarde s'il existe des plannings réels reportés
if (empty($tabPlanSum)) { // Cas où il n'y a pas de planning réel non reporté
    $k = 0;
} else { // Cas où il y a pas du planning réel non reporté, on l'enregistre dans le nouveau tableau
    $k = count($tabPlanSum);
}
// On réunit dans un même tableau le planning std et le nouveau planning réel en supprimant les jours fériés
for ($j = 0; $j < count($tabPlanStd); $j++) {
    // On vérifie si les jours sont non compris dans les jours fériés (cas des ponts)
    if ($tabPlanStd[$j]['idJour'] < $idJourDebFerie || $tabPlanStd[$j]['idJour'] > $idJourFinFerie) {
        $tabPlanSum[$k]['idAgent'] = $tabPlanStd[$j]['idAgent'];
        $tabPlanSum[$k]['idJour'] = $tabPlanStd[$j]['idJour'];
        $tabPlanSum[$k]['idPoste'] = $tabPlanStd[$j]['idPoste'];
        $tabPlanSum[$k]['horaireDeb'] = $tabPlanStd[$j]['horaireDeb'];
        $tabPlanSum[$k]['horaireFin'] = $tabPlanStd[$j]['horaireFin'];
        $k++;
    }
}
// On remplace dans $tabPlanSum, les idHoraires par les vrais horaires de la table horaire en les convertissant en float
// 13:30 devient 13.5 via la fonction convertTimeStringToNumber pour faciliter les calculs entre HoraireDebut et HoraireFin 
$oHoraire = new Horaire();
$tabHoraires = $oHoraire->selectHoraire();

foreach ($tabPlanSum as $key => $value) {
    $posHoraireDeb = array_search($tabPlanSum[$key]['horaireDeb'], array_column($tabHoraires, 'idHoraire'), true);
    $posHoraireFin = array_search($tabPlanSum[$key]['horaireFin'], array_column($tabHoraires, 'idHoraire'), true);
    $tabPlanSum[$key]['horaireDeb'] = convertTimeStringToNumber($tabHoraires[$posHoraireDeb]['libHoraire']);
    $tabPlanSum[$key]['horaireFin'] = convertTimeStringToNumber($tabHoraires[$posHoraireFin]['libHoraire']);
}
// Tri du tableau en fonction des idAgent
asort($tabPlanSum);
// Calcul des heures de service public à partir du tableau $tabPlanSum, 
// on les enregistre dans un nouveau tableau $tabDecHeureSp en les regroupant par idAgent 
$i = 0;
$tabDecHeureSp = array(array("idAgent" => " ", "nbHeureSp" => " "));

foreach ($tabPlanSum as $key => $value) {
    // Calcul du du nb d'heures du service public pour la semaine
    $nbHeureSp = $tabPlanSum[$key]['horaireFin'] - $tabPlanSum[$key]['horaireDeb'];
    // Cas de l'annexe qui finit à 18h au lieu de 18h30
    if ($tabPlanSum[$key]['idPoste'] == "17" && $tabPlanSum[$key]['horaireFin'] == 18.5) {
        $nbHeureSp -= 0.5;
    }
    for ($j = 0; $j < count($tabPlanSum); $j++) {
        if ($tabPlanSum[$j]['idAgent'] != $tabPlanSum[$key]['idAgent']) { // Si l'agent change lors du balayage
            // On vérifie si l'agent existe dans $tabDecHeureSp
            $posTabDecHeuresSp = array_search($tabPlanSum[$key]['idAgent'], array_column($tabDecHeureSp, 'idAgent'), true);
            if (empty($posTabDecHeuresSp)) { // On crée une nouvelle entrée dans le tableau de décompte
                // On enregistre le prénom dans le tableau
                // $posPrenom = array_search($tabPlanSum[$key]['idAgent'], array_column($tabAgent, 'idAgent'), true);
                // $tabDecHeureSp[$i]['prenom'] = $tabAgent[$posPrenom]['prenom'];
                $tabDecHeureSp[$i]['idAgent'] = $tabPlanSum[$key]['idAgent'];
                $tabDecHeureSp[$i]['nbHeureSp'] = $nbHeureSp;
                $i++;
            } else {
                $tabDecHeureSp[$posTabDecHeuresSp]['nbHeureSp'] += $nbHeureSp;
            }
            $j = count($tabPlanSum);
        }
    }
}
// Tri du tableau en fonction des prénoms pour l'affichage
asort($tabDecHeureSp);
// var_dump($tabPlanSum);
// var_dump($tabDecHeureSp);
// exit();
// 
///////////////////////////////////////////// DECOMPTE DES SAMEDIS TRAVAILLES DEPUIS LE DEBUT DE L'ANNEE ///////////////////////////////////////////////////////
//
$oPlanStdSamedi = new PlanStd();
$tabPlanStdSamedi = $oPlanStdSamedi->selectPlanStdSamedi();
$oPlanReelSamedi = new PlanReel();
$tabPlanReelSamedi = $oPlanReelSamedi->selectPlanReelSamedi($_SESSION['year'] . "-01-01", $tabDatesJoursSemaines[6]);


// On supprime tous les evts hors samedis et jours fériés du planning réel et les doublous dûs à un chgt de groupe dans un même saemdi
if (!empty($tabPlanReelSamedi)) {
    foreach ($tabPlanReelSamedi as $key => $value) {
        // On enlève les evts des jours fériés et ceux qui ne sont pas un samedi
        if (!empty($tabJourFerie) && ($tabPlanReelSamedi[$key]['dateReel'] == $tabJourFerie[0]['dateDebFerie'] || $tabPlanReelSamedi[$key]['dateReel'] == $tabJourFerie[0]['dateFinFerie'])) {
            unset($tabPlanReelSamedi[$key]);
        }
        if (array_search($tabPlanReelSamedi[$key]['dateReel'], $tabDatesJoursSemaines) != 6) {
            unset($tabPlanReelSamedi[$key]);
        }
    }
    // Cas où le planning std contient un samedi travaillé et le réel aussi => on enlève le samedi concerné dans le réel
    foreach ($tabPlanStdSamedi as $key1 => $value1) {
        foreach ($tabPlanReelSamedi as $key2 => $value2) {
            if ($tabPlanStdSamedi[$key1]['idAgent'] == $tabPlanReelSamedi[$key2]['idAgent'] && $tabPlanReelSamedi[$key2]['idGroupe'] != "4") {
                unset($tabPlanReelSamedi[$key2]);
            }
        }
    }
    // On supprime les doublons dans le cas de 2 groupes différents pour un même samedi dans le planning réel
    foreach ($tabPlanReelSamedi as $key1 => $value1) {
        foreach ($tabPlanReelSamedi as $key2 => $value2) {
            if ($key2 != $key1 && $tabPlanReelSamedi[$key1]['idAgent'] == $tabPlanReelSamedi[$key2]['idAgent']) {
                unset($tabPlanReelSamedi[$key1]);
                $key1++;
            }
        }
    }
}
// On supprime les doublons dans le planning standard dans le cas de 2 groupes différents pour un même samedi
if (!empty($tabPlanStdSamedi)) {
    foreach ($tabPlanStdSamedi as $key1 => $value1) {
        foreach ($tabPlanStdSamedi as $key2 => $value2) {
            if ($key1 != $key2 && $tabPlanStdSamedi[$key1]['idAgent'] == $tabPlanStdSamedi[$key2]['idAgent']) {
                unset($tabPlanStdSamedi[$key1]);
                $key1++;
            }
        }
    }
}
// var_dump($tabPlanStdSamedi);
// On crée un nouveau tableau dans lequel on va reporter les calculs des samedis travaillés
// $tabSamediAgent = array(array('idAgent' => "", 'nbSamedi' => ""));
$i = 0;
// On recopie dans ce tableau, les calculs issus des samedis standards et réels
foreach ($tabPlanStdSamedi as $key => $value) {
    if (isset($tabPlanStdSamedi[$key])) {
        $tabSamediAgent[$i]['idAgent'] = $tabPlanStdSamedi[$key]['idAgent'];
        $tabSamediAgent[$i]['nbSamedi'] = $_SESSION['weekNumber'];
        $i++;
    }
}
$i = count($tabSamediAgent);
foreach ($tabSamediAgent as $key1 => $value1) {
    foreach ($tabPlanReelSamedi as $key2 => $value2) {
        // Si le groupe est 4 dans le planning réel, on enlève un samedi
        if ($tabPlanReelSamedi[$key2]['idAgent'] == $tabSamediAgent[$key1]['idAgent'] && $tabPlanReelSamedi[$key2]['idGroupe'] == '4') {
            $tabSamediAgent[$key1]['nbSamedi'] --;
        }
        // Si une personne qui ne travaille habituellement pas un samedi a travaillé, on l'enregistre
        if ($tabPlanReelSamedi[$key2]['idAgent'] != $tabSamediAgent[$key1]['idAgent']) {
            $tabSamediAgent[$i]['idAgent'] = $tabPlanReelSamedi[$key2]['idAgent'];
            $tabSamediAgent[$i]['nbSamedi'] = 1;
        }
    }
}
// On fusionne le tableau de calcul des heures de service public $tabDecHeureSp
// avec le tableau des samedis travaillés $tabSamediAgent
// $tabDecTotal = array(array('prenom' => "", 'idAgent' => "", "nbHeureSp" => "", 'nbSamedi' => ""));
$i = 0;
foreach ($tabDecHeureSp as $key => $value) {
    $tabDecTotal[$i]['prenom'] = "";
    $tabDecTotal[$i]['idAgent'] = $tabDecHeureSp[$key]['idAgent'];
    $tabDecTotal[$i]['nbHeureSp'] = $tabDecHeureSp[$key]['nbHeureSp'];
    $tabDecTotal[$i]['nbSamedi'] = 0;
    $i++;
}
$i = count($tabDecTotal);
foreach ($tabSamediAgent as $key1 => $value1) {
    foreach ($tabDecTotal as $key2 => $value2) {
        if ($tabSamediAgent[$key1]['idAgent'] == $tabDecTotal[$key2]['idAgent']) {
            $tabDecTotal[$key2]['nbSamedi'] = $tabSamediAgent[$key1]['nbSamedi'];
        } else {
            $tabDecTotal[$i]['prenom'] = "";
            $tabDecTotal[$i]['idAgent'] = $tabSamediAgent[$key1]['idAgent'];
            $tabDecTotal[$i]['nbHeureSp'] = 0;
            $tabDecTotal[$i]['nbSamedi'] = $tabSamediAgent[$key1]['nbSamedi'];
        }
    }
}

var_dump($tabDecTotal);
//var_dump($tabDecHeureSp);
var_dump($tabSamediAgent);
// var_dump($tabPlanStdSamedi);
// var_dump($tabPlanReelSamedi);
exit();
// On insère les prénoms dans le tableau final en vue de l'affichage
$oAgent = new Agent();
$tabAgent = $oAgent->selectIdPrenomAgent();
//
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