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
//
////////////////////////////////////////////// DECOMPTE NOMBRE HEURES DE SERVICE PUBLIC DE LA SEMAINE //////////////////////////////////////////////////////////
//
// Tableau des dates réelles du dimanche au samedi au format américain
$tabDatesJoursSemaines = datesJourSemaine($_SESSION['weekNumber'], $_SESSION['year']);
//
// Sélection des plannings stardards de la semaine
$oPlanStd = new PlanStd();
$tabPlanStd = $oPlanStd->selectPlanStdDecSp();
//
// Sélection des plannings réels de la semaine
$oPlanReel = new PlanReel();
$tabPlanReel = $oPlanReel->selectPlanReelDecSp($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);
//
// Sélection des jours fériés
$oFerie = new Ferie();
$tabJFSemaine = $oFerie->selectFerie($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);
//
if (!empty($tabJFSemaine)) { // S'il y a des jours fériés la semaine concernée
    if (!empty($tabPlanReel)) { // Si $tabPlanReel contient un résultat, on supprime les jours fériés
        foreach ($tabPlanReel as $key1 => $value1) {
            foreach ($tabJFSemaine as $key2 => $value2) {
                if ($tabPlanReel[$key1]['dateReel'] >= $tabJFSemaine[$key2]['dateDebFerie'] && $tabPlanReel[$key1]['dateReel'] <= $tabJFSemaine[$key2]['dateFinFerie']) {
                    unset($tabPlanReel[$key1]);
                }
            }
        }
        // $tabPlanReel = array_values($tabPlanReel); // On réindexe le tableau
    }
    // on supprime les jours fériés dans $tabPlanStd
    foreach ($tabPlanStd as $key1 => $value1) {
        foreach ($tabJFSemaine as $key2 => $value2) {
            if ($tabPlanStd[$key1]['idJour'] >= convertDateNumJour($tabJFSemaine[$key2]['dateDebFerie']) && $tabPlanStd[$key1]['idJour'] <= convertDateNumJour($tabJFSemaine[$key2]['dateFinFerie'])) {
                unset($tabPlanStd[$key1]);
            }
        }
    }
    // On réindexe les tableaux car à cause des unsets, il y a des trous dans les index
    // $tabPlanStd = array_values($tabPlanStd);
}
// On compare le planning réel et le planning standard et on enregistre les modifs dans le planning standard
if (!empty($tabPlanReel)) { // S'il y a du planning réel
    $i = count($tabPlanStd);
    foreach ($tabPlanReel as $key1 => $value1) {
        $compteur = 0;
        foreach ($tabPlanStd as $key2 => $value2) {
            if (isset($tabPlanReel[$key1])) {
                // Pour un même agent, le même jour aux même horaires, s'il y a du non SP dans le réel alors qu'il y a du SP sans le std, on supprime l'entrée du std et du réel
                if ($tabPlanReel[$key1]['idAgent'] == $tabPlanStd[$key2]['idAgent'] && convertDateNumJour($tabPlanReel[$key1]['dateReel']) == $tabPlanStd[$key2]['idJour'] &&
                        $tabPlanReel[$key1]['horaireDeb'] == $tabPlanStd[$key2]['horaireDeb'] && $tabPlanReel[$key1]['horaireFin'] == $tabPlanStd[$key2]['horaireFin'] &&
                        $tabPlanReel[$key1]['idGroupe'] >= '3' && $tabPlanStd[$key2]['idGroupe'] < '3') {
                    unset($tabPlanStd[$key2]);
                    $compteur--;
                } else {
                    $compteur++;
                }
            }
            if ($compteur == count($tabPlanReel) && $tabPlanReel[$key1]['idGroupe'] < '3') {
                $tabPlanStd[$i]['idAgent'] = $tabPlanReel[$key1]['idAgent'];
                $tabPlanStd[$i]['idJour'] = convertDateNumJour($tabPlanReel[$key1]['dateReel']);
                $tabPlanStd[$i]['idPoste'] = $tabPlanReel[$key1]['idPoste'];
                $tabPlanStd[$i]['idGroupe'] = $tabPlanReel[$key1]['idGroupe'];
                $tabPlanStd[$i]['horaireDeb'] = $tabPlanReel[$key1]['horaireDeb'];
                $tabPlanStd[$i]['horaireFin'] = $tabPlanReel[$key1]['horaireFin'];
                $i++;
            }
        }
    }
}
// On remplace dans $tabPlanStd, les idHoraires par les vrais horaires de la table horaire en les convertissant en float
// 13:30 devient 13.5 via la fonction convertTimeStringToNumber pour faciliter les calculs entre HoraireDebut et HoraireFin 
$oHoraire = new Horaire();
$tabHoraires = $oHoraire->selectHoraire();

foreach ($tabPlanStd as $key => $value) {
    $posHoraireDeb = array_search($tabPlanStd[$key]['horaireDeb'], array_column($tabHoraires, 'idHoraire'), true);
    $posHoraireFin = array_search($tabPlanStd[$key]['horaireFin'], array_column($tabHoraires, 'idHoraire'), true);
    $tabPlanStd[$key]['horaireDeb'] = convertTimeStringToNumber($tabHoraires[$posHoraireDeb]['libHoraire']);
    $tabPlanStd[$key]['horaireFin'] = convertTimeStringToNumber($tabHoraires[$posHoraireFin]['libHoraire']);
}
// Calcul des heures de service public à partir du tableau $tabPlanStd, 
// on les enregistre dans un nouveau tableau $tabDecHeureSp en les regroupant par idAgent 
$i = 0;
$tabDecHeureSp = array(array("idAgent" => " ", "nbHeureSp" => " "));
foreach ($tabPlanStd as $key => $value) {
    // Calcul du du nb d'heures du service public pour la semaine
    $nbHeureSp = $tabPlanStd[$key]['horaireFin'] - $tabPlanStd[$key]['horaireDeb'];
    // Cas de l'annexe qui finit à 18h au lieu de 18h30
    if ($tabPlanStd[$key]['idPoste'] == "17" && $tabPlanStd[$key]['horaireFin'] == 18.5) {
        $nbHeureSp -= 0.5;
    }
    for ($j = 0; $j < count($tabPlanStd); $j++) {
        if ($tabPlanStd[$j]['idAgent'] != $tabPlanStd[$key]['idAgent']) { // Si l'agent change lors du balayage
            // On vérifie si l'agent existe dans $tabDecHeureSp
            $posTabDecHeuresSp = array_search($tabPlanStd[$key]['idAgent'], array_column($tabDecHeureSp, 'idAgent'), true);
            if (empty($posTabDecHeuresSp)) { // On crée une nouvelle entrée dans le tableau de décompte
                // On enregistre le prénom dans le tableau
                // $posPrenom = array_search($tabPlanStd[$key]['idAgent'], array_column($tabAgent, 'idAgent'), true);
                // $tabDecHeureSp[$i]['prenom'] = $tabAgent[$posPrenom]['prenom'];
                $tabDecHeureSp[$i]['idAgent'] = $tabPlanStd[$key]['idAgent'];
                $tabDecHeureSp[$i]['nbHeureSp'] = $nbHeureSp;
                $i++;
            } else {
                $tabDecHeureSp[$posTabDecHeuresSp]['nbHeureSp'] += $nbHeureSp;
            }
            $j = count($tabPlanStd);
        }
    }
}

// var_dump($tabPlanStd);
var_dump($tabDecHeureSp);
// exit();
// 
///////////////////////////////////////////// DECOMPTE DES SAMEDIS TRAVAILLES DEPUIS LE DEBUT DE L'ANNEE ///////////////////////////////////////////////////////
//
// On recherche les jours fériés depuis le début de l'année
$dateDebutAnnee = $_SESSION['year'] . "-01-01";
$tabJFdebutAnnee = $oFerie->selectFerie($dateDebutAnnee, $tabDatesJoursSemaines[6]);
// S'il y a des jours fériés, on compte ceux qui tombent un samedi et on assigne le résultat dans une variable $nbSamediFerie
if (!empty($tabJFdebutAnnee)) {
    $nbSamediFerie = 0;
    foreach ($tabJFdebutAnnee as $key => $value) {
        $dateTest = $tabJFdebutAnnee[$key]['dateDebFerie'];
        // On compte le nombre de samedis fériés entre les dates de début et les dates de fin des jours fériés dans le cas des ponts
        while ($dateTest >= $tabJFdebutAnnee[$key]['dateDebFerie'] && $dateTest <= $tabJFdebutAnnee[$key]['dateFinFerie']) {
            if (convertDateNumJour($dateTest) == 6) {
                $nbSamediFerie ++;
            }
            $dateTest++;
        }
    }
}
$oPlanStdSamedi = new PlanStd();
$tabPlanStdSamedi = $oPlanStdSamedi->selectPlanStdSamedi();
$oPlanReelSamedi = new PlanReel();
$tabPlanReelSamedi = $oPlanReelSamedi->selectPlanReelSamedi($dateDebutAnnee, $tabDatesJoursSemaines[6]);
//
// On supprime du planning réel tous les evts hors samedis et jours fériés et les doublons dûs à un chgt de groupe dans un même samedi
if (!empty($tabJFdebutAnnee)) {
    foreach ($tabPlanReelSamedi as $key1 => $value1) {
        // On enlève les evts des jours fériés et ceux qui ne sont pas un samedi
        foreach ($tabJFdebutAnnee as $key2 => $value2) {
            if ($tabPlanReelSamedi[$key1]['dateReel'] >= $tabJFdebutAnnee[$key2]['dateDebFerie'] || $tabPlanReelSamedi[$key1]['dateReel'] <= $tabJFdebutAnnee[$key2]['dateFinFerie']) {
                unset($tabPlanReelSamedi[$key]);
                $key++;
            }
            if (array_search($tabPlanReelSamedi[$key]['dateReel'], $tabDatesJoursSemaines) != 6) {
                unset($tabPlanReelSamedi[$key]);
                $key++;
            }
        }
    }
    // Cas où le planning std contient un samedi travaillé et le réel aussi => on enlève le samedi concerné dans le réel
    foreach ($tabPlanStdSamedi as $key1 => $value1) {
        foreach ($tabPlanReelSamedi as $key2 => $value2) {
            if ($tabPlanStdSamedi[$key1]['idAgent'] == $tabPlanReelSamedi[$key2]['idAgent'] && $tabPlanReelSamedi[$key2]['idGroupe'] != "4") {
                unset($tabPlanReelSamedi[$key2]);
                $key2++;
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
// var_dump($tabPlanReelSamedi);
// On crée un nouveau tableau dans lequel on va reporter les calculs des samedis travaillés
// $tabSamediAgent = array(array('idAgent' => "", 'nbSamedi' => ""));
$i = 0;
// On recopie dans ce tableau, les calculs issus des samedis standards et réels
foreach ($tabPlanStdSamedi as $key => $value) {
    if (isset($tabPlanStdSamedi[$key])) {
        $tabSamediAgent[$i]['idAgent'] = $tabPlanStdSamedi[$key]['idAgent'];
        $tabSamediAgent[$i]['nbSamedi'] = $_SESSION['weekNumber'] - $nbSamediFerie;
        $i++;
    }
}
// Calcul du nb de samedis fériés depuis le début de l'année
$i = count($tabSamediAgent);
foreach ($tabSamediAgent as $key1 => $value1) {
    foreach ($tabPlanReelSamedi as $key2 => $value2) {
        // Si le groupe est 4 dans le planning réel, on enlève un samedi
        if ($tabSamediAgent[$key1]['idAgent'] == $tabPlanReelSamedi[$key2]['idAgent'] && $tabPlanReelSamedi[$key2]['idGroupe'] == '4') {
            $tabSamediAgent[$key1]['nbSamedi'] --;
        }
        // Si une personne qui ne travaille habituellement pas un samedi a travaillé, on l'enregistre
        if ($tabPlanReelSamedi[$key2]['idAgent'] != $tabSamediAgent[$key1]['idAgent']) {
            $tabSamediAgent[$i]['idAgent'] = $tabPlanReelSamedi[$key2]['idAgent'];
            $tabSamediAgent[$i]['nbSamedi'] = 1;
            $i++;
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
$compteur = 0;
foreach ($tabSamediAgent as $key1 => $value1) {
    foreach ($tabDecTotal as $key2 => $value2) {
        if ($tabSamediAgent[$key1]['idAgent'] == $tabDecTotal[$key2]['idAgent']) {
            $tabDecTotal[$key2]['nbSamedi'] = $tabSamediAgent[$key1]['nbSamedi'];
        } else {
            $compteur++;
        }
    }
    if ($compteur == count($tabSamediAgent)) {
        $tabDecTotal[$i]['prenom'] = "";
        $tabDecTotal[$i]['idAgent'] = $tabSamediAgent[$key1]['idAgent'];
        $tabDecTotal[$i]['nbHeureSp'] = 0;
        $tabDecTotal[$i]['nbSamedi'] = $tabSamediAgent[$key1]['nbSamedi'];
        $i++;
    }
}
// On insère les prénoms dans le tableau final en vue de l'affichage
$oAgent = new Agent();
$tabAgent = $oAgent->selectIdPrenomAgent();
foreach ($tabDecTotal as $key1 => $value1) {
    foreach ($tabAgent as $key2 => $value2) {
        if ($tabDecTotal[$key1]['idAgent'] == $tabAgent[$key2]['idAgent']) {
            $tabDecTotal[$key1]['prenom'] = $tabAgent[$key2]['prenom'];
        }
    }
}
// Tri du tableau dans l'ordre alphabétique des prénoms pour l'affichage
asort($tabDecTotal);
//
// var_dump($tabDecTotal);
// var_dump($tabAgent);
// var_dump($tabDecHeureSp);
// var_dump($tabSamediAgent);
// var_dump($tabPlanStdSamedi);
// var_dump($tabPlanReelSamedi);
exit();
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