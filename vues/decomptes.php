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

// Sélection des plannings stardards de la semaine
$oPlanStd = new PlanStd();
$tabPlanStd = $oPlanStd->selectDecPlanStd();

// Sélection des plannings réels de la semaine
$oPlanReel = new PlanReel();
$tabPlanReel = $oPlanReel->selectDecPlanReel($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);

// Sélection des jours fériés
$oFerie = new Ferie();
$tabJourFerie = $oFerie->selectFerie($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);

// Si $tabPlanReel contient un résultat, je remplace la date par le numéro du jour de la semaine
if (!empty($tabPlanReel)) {
    for ($i = 0; $i < count($tabPlanReel); $i++) {
        $tabPlanReel[$i]['dateReel'] = array_search($tabPlanReel[$i]['dateReel'], $tabDatesJoursSemaines);
    }
    // On prévoit le cas où il n'y a pas de jours fériés pour initialiser certaines variables qui vont ensuite servir dans les tests
    if (empty($tabJourFerie)) {
        $jourDebFerie = 7;
        $jourFinFerie = 0;
    } else {
        for ($i = 0; $i < count($tabJourFerie); $i++) {
            $tabJourFerie[$i]['dateDebFerie'] = array_search($tabJourFerie[$i]['dateDebFerie'], $tabDatesJoursSemaines);
            $tabJourFerie[$i]['dateFinFerie'] = array_search($tabJourFerie[$i]['dateFinFerie'], $tabDatesJoursSemaines);
            $jourDebFerie = $tabJourFerie[$i]['dateDebFerie'];
            $jourFinFerie = $tabJourFerie[$i]['dateFinFerie'];
        }
    }

    // On remplace les données du planning standard par le planning réel (idPoste, idGroupe)
    for ($j = 0; $j < count($tabPlanStd); $j++) {
        for ($k = 0; $k < count($tabPlanReel); $k++) {
            // On vérifie si les jours sont non compris dans les jours fériés (cas des ponts)
            if ($tabPlanReel[$k]['dateReel'] < $jourDebFerie || $tabPlanReel[$k]['dateReel'] > $jourFinFerie) {
                if ($tabPlanStd[$j]['idAgent'] == $tabPlanReel[$k]['idAgent'] && $tabPlanStd[$j]['idJour'] == $tabPlanReel[$k]['dateReel'] && $tabPlanStd[$j]['horaireDeb'] == $tabPlanReel[$k]['horaireDeb'] && $tabPlanStd[$j]['horaireFin'] == $tabPlanReel[$k]['horaireFin']) {
                    $tabPlanStd[$i]['idPoste'] = $tabPlanReel[$k]['idPoste'];
                    $tabPlanStd[$i]['idGroupe'] = $tabPlanReel[$k]['idGroupe'];
                    $k = count($tabPlanReel);
                } else { // On enregistre dans un nouveau tableau, le planning réel qui n'a pas été reporté dans le std
                    $tabPlanSum[$k]['idAgent'] = $tabPlanReel[$k]['idAgent'];
                    $tabPlanSum[$k]['idJour'] = $tabPlanReel[$k]['dateReel'];
                    $tabPlanSum[$k]['idPoste'] = $tabPlanReel[$k]['idPoste'];
                    $tabPlanSum[$k]['idGroupe'] = $tabPlanReel[$k]['idGroupe'];
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
    if ($tabPlanStd[$j]['idJour'] < $jourDebFerie || $tabPlanStd[$j]['idJour'] > $jourFinFerie) {
        $tabPlanSum[$k]['idAgent'] = $tabPlanStd[$j]['idAgent'];
        // $tabPlanSum[$k]['prenom'] = $tabPlanStd[$j]['prenom'];
        $tabPlanSum[$k]['idJour'] = $tabPlanStd[$j]['idJour'];
        $tabPlanSum[$k]['idPoste'] = $tabPlanStd[$j]['idPoste'];
        $tabPlanSum[$k]['idGroupe'] = $tabPlanStd[$j]['idGroupe'];
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
// on les enregistre dans un nouveau tableau $tabDecHeuresSp en les regroupant par idAgent 
// On insère également les prénoms en vue de l'affichage
$oAgent = new Agent();
$tabAgent = $oAgent->selectIdPrenomAgent();
$i = 0;
$tabDecHeuresSp = array(array("prenom" => " ", "idAgent" => " ", "nbHeuresSp" => 0));

foreach ($tabPlanSum as $key => $value) {
    // Calcul du du nb d'heures du service public pour la semaine
    $nbHeuresSp = $tabPlanSum[$key]['horaireFin'] - $tabPlanSum[$key]['horaireDeb'];
    // Cas de l'annexe qui finit à 18h au lieu de 18h30
    if ($tabPlanSum[$key]['idPoste'] == "17" && $tabPlanSum[$key]['horaireFin'] == 18.5) {
        $nbHeuresSp -= 0.5;
    }
    for ($j = 0; $j < count($tabPlanSum); $j++) {
        if ($tabPlanSum[$j]['idAgent'] != $tabPlanSum[$key]['idAgent']) { // Si l'agent change lors du balayage
            // On vérifie si l'agent existe dans $tabDecHeuresSp
            $posTabDecHeuresSp = array_search($tabPlanSum[$key]['idAgent'], array_column($tabDecHeuresSp, 'idAgent'), true);
            if (empty($posTabDecHeuresSp)) { // On crée une nouvelle entrée dans le tableau de décompte
                // On enregistre le prénom dans le tableau
                $posPrenom = array_search($tabPlanSum[$key]['idAgent'], array_column($tabAgent, 'idAgent'), true);
                $tabDecHeuresSp[$i]['prenom'] = $tabAgent[$posPrenom]['prenom'];
                $tabDecHeuresSp[$i]['idAgent'] = $tabPlanSum[$key]['idAgent'];
                $tabDecHeuresSp[$i]['nbHeuresSp'] = $nbHeuresSp;
                $i++;
            } else {
                $tabDecHeuresSp[$posTabDecHeuresSp]['nbHeuresSp'] += $nbHeuresSp;
            }
            $j = count($tabPlanSum);
        }
    }
}
// Tri du tableau en fonction des prénoms pour l'affcihage
asort($tabDecHeuresSp);
// var_dump($tabPlanSum);
var_dump($tabDecHeuresSp);
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