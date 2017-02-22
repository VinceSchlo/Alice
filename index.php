<?php
session_start(); // Utilisation des variables $_SESSION

require_once('include/alice_dao.inc.php');
require_once('include/alice_fonctions.php');
require_once('class/Agent.php');
require_once('class/PlanStd.php');
require_once('class/PlanReel.php');
require_once('class/Ferie.php');
require_once('class/horaire.php');

$plan = new PlanStd();
$user = $plan->selectUser();
$poste = $plan->selectPlanStd();
$compte = 0;
$t = 10;

if (!isset($_POST['precedente']) && !isset($_POST['suivante'])) {
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
$oPlanReel = new PlanReel();
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
    for ($j = 0; $j < count($poste); $j++) {
        for ($k = 0; $k < count($planReel); $k++) {
            if ($poste[$j]['idAgent'] == $planReel[$k]['idAgent'] && $poste[$j]['idJour'] == $planReel[$k]['dateReel'] && $poste[$j]['horaireDeb'] == $planReel[$k]['horaireDeb'] && $poste[$j]['horaireFin'] == $planReel[$k]['horaireFin']) {

                $poste[$j]['libPoste'] = $planReel[$k]['libPoste'];
                $poste[$j]['idPoste'] = $planReel[$k]['idPoste'];
                $poste[$j]['coulGroupe'] = $planReel[$k]['coulGroupe'];

                $k = count($planReel);
            }
        }
    }
    for ($j = 0; $j < count($poste); $j++) {
        for ($k = 0; $k < count($jourFerie); $k++) {
            if ($poste[$j]['idJour'] == $jourFerie[$k]['dateDebFerie']) {

                $poste[$j]['libPoste'] = "Ferie";
                $poste[$j]['coulGroupe'] = null;
                $poste[$j]['idPoste'] = null;

                $k = count($planReel);
            }
        }
    }
}

$oHoraire = new Horaire();
$time = $oHoraire->selectHoraire();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/alice.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"
            integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn"
            crossorigin="anonymous"></script>
    <title>ALICE</title>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2">
            <img class="logo" src="images/logo_sna_quadri.png"/>
        </div>
        <div class="col-lg-10">
            <div class="row">
                <div class="col-lg-offset-5 col-lg-3">
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
                    echo "Semaine n°" . $_SESSION['weekNumber'];
                    ?>
                </h2>
            </div>
            <div class="row">
                <h2 class="col-lg-offset-5 col-lg-3">
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
<div class="container-fluid">

    <div class="col-lg-1">
        <table class="table table-bordered color-grey border-black">
            <tr>
                <th class="text-center text-size">Alice</th>
            </tr>
            <tr>
                <th class="text-center poste-size">Personnel</th>
            </tr>
            <?php foreach ($user as $cle => $valeur) { ?>
                <tr class="name-size">
                    <td><?php echo $user[$cle]['prenom'] ?></td>
                </tr>

            <?php } ?>
        </table>
    </div>

    <div class="col-lg-10 marge-two">
        <table class="table table-bordered">
            <!--            Affichage des jours-->
            <tr class="color-grey text-size">
                <th class="text-center" colspan="2">Lundi</th>
                <th class="text-center" colspan="2">Mardi</th>
                <th class="text-center" colspan="3">Mercredi</th>
                <th class="text-center" colspan="2">Jeudi</th>
                <th class="text-center" colspan="2">Vendredi</th>
                <th class="text-center" colspan="2">Samedi</th>
            </tr>
            <!--            Affichage des horraires -->
            <tr class="color-grey name-size">
                <?php
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
                        echo substr($time[5]['libHoraire'], 0, 5);
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
                echo substr($time[5]['libHoraire'], 0, 5);
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
                        echo substr($time[5]['libHoraire'], 0, 5);
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
            <!--            Affichage du planing -->
            <tr class="poste-size">
                <?php
                for ($i = 0; $i < count($poste); $i++) {
                    $couleur = $poste[$i]['coulGroupe'];
                    echo "<td class=\"text-center\" style='background-color:$couleur'>";
                    echo $poste[$i]['libPoste'];
                    echo "</td>";
                    $compte++;
                    if ($compte == 13) {
                        echo "</tr>";
                        echo "<tr class='poste-size'>";
                        $compte = 0;
                    }
                }
                ?>
            </tr>

        </table>
    </div>

</div>

</body>
</html>