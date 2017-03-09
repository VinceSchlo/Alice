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
    }
    // on supprime les jours fériés dans $tabPlanStd
    foreach ($tabPlanStd as $key1 => $value1) {
        foreach ($tabJFSemaine as $key2 => $value2) {
            if ($tabPlanStd[$key1]['idJour'] >= convertDateNumJour($tabJFSemaine[$key2]['dateDebFerie']) && $tabPlanStd[$key1]['idJour'] <= convertDateNumJour($tabJFSemaine[$key2]['dateFinFerie'])) {
                unset($tabPlanStd[$key1]);
            }
        }
    }
    // On réindexe les tableaux car il y a des trous dans les index à cause des unsets
    $tabPlanReel = array_values($tabPlanReel);
    $tabPlanStd = array_values($tabPlanStd);
}
// On compare le planning réel et le planning standard et on enregistre les modifs dans le planning standard
if (!empty($tabPlanReel)) { // S'il y a du planning réel
    $i = count($tabPlanStd);
    foreach ($tabPlanReel as $key1 => $value1) {
        $compteur = 0;
        foreach ($tabPlanStd as $key2 => $value2) {
            if (isset($tabPlanReel[$key1])) {
                // Pour un même agent, le même jour aux même horaires, s'il y a du non SP dans le réel alors qu'il y a du SP dans le std, on supprime l'entrée du std
                if ($tabPlanReel[$key1]['idAgent'] == $tabPlanStd[$key2]['idAgent'] && convertDateNumJour($tabPlanReel[$key1]['dateReel']) == $tabPlanStd[$key2]['idJour'] &&
                        $tabPlanReel[$key1]['horaireDeb'] == $tabPlanStd[$key2]['horaireDeb'] && $tabPlanReel[$key1]['horaireFin'] == $tabPlanStd[$key2]['horaireFin'] &&
                        $tabPlanReel[$key1]['idGroupe'] >= '3' && $tabPlanStd[$key2]['idGroupe'] < '3') {
                    unset($tabPlanStd[$key2]);
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
//
$oHoraire = new Horaire();
$tabHoraires = $oHoraire->selectHoraire();
//
// Calcul des heures de service public à partir du tableau $tabPlanStd, 
// on les enregistre dans un nouveau tableau $tabDecHeureSp en les regroupant par idAgent 
$tabDecHeureSp = array(array("idAgent" => " ", "nbHeureSp" => " "));
foreach ($tabPlanStd as $key1 => $value1) {
    // On remplace dans $tabPlanStd, les idHoraires par les vrais horaires de la table horaire en les convertissant en float
    // via la fonction convertTimeStringToNumber pour faciliter les calculs entre HoraireDebut et HoraireFin. 13:30:00 devient 13.5 
    foreach ($tabHoraires as $key3 => $value3) {
        if ($tabPlanStd[$key1]['horaireDeb'] == $tabHoraires[$key3]['idHoraire']) {
            $tabPlanStd[$key1]['horaireDeb'] = convertTimeStringToNumber($tabHoraires[$key3]['libHoraire']);
        }
        if ($tabPlanStd[$key1]['horaireFin'] == $tabHoraires[$key3]['idHoraire']) {
            $tabPlanStd[$key1]['horaireFin'] = convertTimeStringToNumber($tabHoraires[$key3]['libHoraire']);
        }
    }
    // Calcul du nb d'heures du service public pour la semaine
    $nbHeureSp = $tabPlanStd[$key1]['horaireFin'] - $tabPlanStd[$key1]['horaireDeb'];
    // Cas de l'annexe qui finit à 18h au lieu de 18h30
    if ($tabPlanStd[$key1]['idPoste'] == "17" && $tabPlanStd[$key1]['horaireFin'] == 18.5) {
        $nbHeureSp -= 0.5;
    }
    // on les enregistre dans un nouveau tableau $tabDecHeureSp, les heures de SP en les regroupant par idAgent 
    $compteur = 0;
    foreach ($tabDecHeureSp as $key2 => $value2) {
        if (empty(array_search($tabPlanStd[$key1]['idAgent'], array_column($tabDecHeureSp, 'idAgent'))) && ($key1 == $key2 ||
                !isset($tabDecHeureSp[$key1 + 1]))) {
            $compteur++;
        }
        if ($tabDecHeureSp[$key2]['idAgent'] == $tabPlanStd[$key1]['idAgent']) {
            $tabDecHeureSp[$key2]['nbHeureSp'] += $nbHeureSp;
            $compteur--;
        }
    }
    if ($compteur == count($tabDecHeureSp)) {
        $tabDecHeureSp[$key1]['idAgent'] = $tabPlanStd[$key1]['idAgent'];
        $tabDecHeureSp[$key1]['nbHeureSp'] = $nbHeureSp;
    }
}
// var_dump($tabPlanStd);
// var_dump($tabDecHeureSp);
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
                $nbSamediFerie++;
            }
            $dateTest++;
        }
    }
}
// On charge le planning standard et le planning réel depuis la BDD. On ne prend que les samedis dans le cas du standard
$oPlanStdSamedi = new PlanStd();
$tabPlanStdSamedi = $oPlanStdSamedi->selectPlanStdSamedi();
$oPlanReelSamedi = new PlanReel();
$tabPlanReelSamedi = $oPlanReelSamedi->selectPlanReelSamedi($dateDebutAnnee, $tabDatesJoursSemaines[6]);
//
// On supprime du planning réel tous les evts hors samedis et jours fériés et les doublons dûs à un chgt de groupe dans un même samedi
if (!empty($tabJFdebutAnnee)) {
    if (!empty($oPlanReelSamedi)) {
        foreach ($tabJFdebutAnnee as $key1 => $value1) {
            foreach ($tabPlanReelSamedi as $key2 => $value2) {
                if (isset($tabPlanReelSamedi[$key2]) && convertDateNumJour($tabPlanReelSamedi[$key2]['dateReel']) != 6) {
                    unset($tabPlanReelSamedi[$key2]);
                }
                if (isset($tabPlanReelSamedi[$key2]) && $tabPlanReelSamedi[$key2]['dateReel'] >= $tabJFdebutAnnee[$key1]['dateDebFerie'] &&
                        $tabPlanReelSamedi[$key2]['dateReel'] <= $tabJFdebutAnnee[$key1]['dateFinFerie']) {
                    unset($tabPlanReelSamedi[$key2]);
                }
            }
        }
    }
    // Cas où le planning réel contient un samedi travaillé et le std aussi => on enlève le samedi concerné dans le réel sauf si c'est du groupe 4
    foreach ($tabPlanStdSamedi as $key1 => $value1) {
        foreach ($tabPlanReelSamedi as $key2 => $value2) {
            if (isset($tabPlanReelSamedi[$key2]) && $tabPlanStdSamedi[$key1]['idAgent'] == $tabPlanReelSamedi[$key2]['idAgent'] &&
                    $tabPlanReelSamedi[$key2]['idGroupe'] != '4') {
                unset($tabPlanReelSamedi[$key2]);
            }
        }
    }
    // On supprime les doublons pour un même samedi dans le planning réel (cas de 2 groupes différents pour un même samedi)
    foreach ($tabPlanReelSamedi as $key1 => $value1) {
        foreach ($tabPlanReelSamedi as $key2 => $value2) {
            if (isset($tabPlanReelSamedi[$key2]) && isset($tabPlanReelSamedi[$key1]) &&
                    $key2 != $key1 &&
                    $tabPlanReelSamedi[$key1]['idAgent'] == $tabPlanReelSamedi[$key2]['idAgent'] &&
                    $tabPlanReelSamedi[$key1]['dateReel'] == $tabPlanReelSamedi[$key2]['dateReel']) {
                unset($tabPlanReelSamedi[$key2]);
            }
        }
    }
}
// On supprime les doublons dans le planning standard dans le cas de 2 groupes différents pour un même samedi
if (!empty($tabPlanStdSamedi)) {
    foreach ($tabPlanStdSamedi as $key1 => $value1) {
        foreach ($tabPlanStdSamedi as $key2 => $value2) {
            if (isset($tabPlanStdSamedi[$key2]) && isset($tabPlanStdSamedi[$key1]) &&
                    $key2 != $key1 && $tabPlanStdSamedi[$key1]['idAgent'] == $tabPlanStdSamedi[$key2]['idAgent']) {
                unset($tabPlanStdSamedi[$key2]);
            }
        }
    }
}
// On crée un nouveau tableau dans lequel on va reporter les calculs des samedis travaillés
// $tabSamediAgent = array(array('idAgent' => "", 'nbSamedi' => ""));
$i = 0;
// On recopie dans ce tableau, les calculs issus des samedis standards et réels
foreach ($tabPlanStdSamedi as $key => $value) {
    $tabSamediAgent[$i]['idAgent'] = $tabPlanStdSamedi[$key]['idAgent'];
    $tabSamediAgent[$i]['nbSamedi'] = $_SESSION['weekNumber'] - $nbSamediFerie;
    $i++;
}
// On décrémente le nb de samedis travaillés si dans le réel, on a des samedis du groupe 4 
// cas d'absence de samedis dans le réel alors que l'agent travaille habituellement le samedi
$i = count($tabSamediAgent);
foreach ($tabPlanReelSamedi as $key1 => $value1) {
    $compteur = 0;
    foreach ($tabSamediAgent as $key2 => $value2) {
        if ($tabSamediAgent[$key2]['idAgent'] == $tabPlanReelSamedi[$key1]['idAgent']) {
            // Si le groupe est 4 dans le planning réel, on enlève un samedi
            if ($tabPlanReelSamedi[$key1]['idGroupe'] == '4') {
                $tabSamediAgent[$key2]['nbSamedi'] --;
            } else { // Sinon, on rajoute un samedi
                $tabSamediAgent[$key2]['nbSamedi'] ++;
            }
        } else {
            $compteur++;
        }
    }
// Si une personne qui ne travaille habituellement pas un samedi a travaillé, on l'enregistre sauf si c'est du groupe 4
    if ($compteur == $i && $tabPlanReelSamedi[$key1]['idAgent'] != $tabSamediAgent[$key2]['idAgent'] && $tabPlanReelSamedi[$key1]['idGroupe'] != '4') {
        $tabSamediAgent[$i]['idAgent'] = $tabPlanReelSamedi[$key1]['idAgent'];
        $tabSamediAgent[$i]['nbSamedi'] = 1;
        $i++;
    }
}
// On fusionne le tableau de calcul des heures de service public $tabDecHeureSp
// avec le tableau des samedis travaillés $tabSamediAgent
$i = 0;
foreach ($tabDecHeureSp as $key => $value) {
    $tabDecTotal[$i]['prenom'] = "";
    $tabDecTotal[$i]['idAgent'] = $tabDecHeureSp[$key]['idAgent'];
    $tabDecTotal[$i]['nbHeureSp'] = $tabDecHeureSp[$key]['nbHeureSp'];
    $tabDecTotal[$i]['nbSamedi'] = 0;
    $i++;
}
//
$i = count($tabDecTotal);
foreach ($tabSamediAgent as $key1 => $value1) {
    $compteur = 0;
    foreach ($tabDecTotal as $key2 => $value2) {
        if ($tabSamediAgent[$key1]['idAgent'] == $tabDecTotal[$key2]['idAgent']) {
            $tabDecTotal[$key2]['nbSamedi'] = $tabSamediAgent[$key1]['nbSamedi'];
        } else {
            $compteur++;
        }
    }
    if ($compteur == count($tabDecTotal)) {
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
sort($tabDecTotal);
// array_multisort($tabDecTotal['prenom'], SORT_ASC, SORT_STRING);
// var_dump($tabDecTotal);
// var_dump($tabAgent);
// var_dump($tabDecHeureSp);
// var_dump($tabSamediAgent);
// var_dump($tabPlanStdSamedi);
// var_dump($tabPlanReelSamedi);
// exit();
//
?>
<body>
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
    <!-- Affichage des heures de service public et des samedis -->
    <div class="container-fluid col-lg-offset-2">
        <div class="col-lg-7">
            <table class="table table-bordered">
                <tr class="color-grey">
                    <th class="thCentre">Personnel</th>
                    <th class="thCentre">Nombre d'heures de service public cette semaine</th>
                    <th class="thCentre">Nombre de samedis travaillés depuis le début de l'année</th>
                </tr>
                <br/>
                <?php foreach ($tabDecTotal as $key => $value) {
                    ?>
                    <tr>
                        <td> <?php echo $tabDecTotal[$key]['prenom']; ?> </td>
                        <td> <?php echo $tabDecTotal[$key]['nbHeureSp'] . " h"; ?> </td>
                        <td> <?php
                            if ($tabDecTotal[$key]['nbSamedi'] <= 1) {
                                echo $tabDecTotal[$key]['nbSamedi'] . " samedi";
                            } else {
                                echo $tabDecTotal[$key]['nbSamedi'] . " samedis";
                            }
                            ?> </td>
                    </tr>
                <?php } ?>
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