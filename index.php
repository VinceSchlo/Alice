<?php
session_start(); // Utilisation des variables $_SESSION

require_once('include/alice_dao.inc.php');
require_once('include/alice_fonctions.php');
require_once('class/Agent.php');
require_once('class/PlanStd.php');
require_once('class/PlanReel.php');

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
    $_SESSION['weekNumber'] --;
    if ($_SESSION['weekNumber'] < 1) {
        $_SESSION['weekNumber'] = 52;
        $_SESSION['year'] --;
    }
}
if (isset($_POST['home'])) {
    $_SESSION['weekNumber'] = date("W");
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

// Selection des plannings réél de la semaine
$oPlanReel = new PlanReel();
$planReel = $oPlanReel->selectReel($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);
//var_dump($poste);
//var_dump($planReel);

if (isset($planReel)) {
    for ($i = 0; $i < count($planReel); $i++) {
        $planReel[$i]['dateReel'] = array_search($planReel[$i]['dateReel'], $tabDatesJoursSemaines);
    }
//    var_dump($planReel);
    for ($j = 0; $j < count($poste); $j++) {
        for ($k = 0; $k < count($planReel); $k++) {
            if ($poste[$j]['idAgent'] == $planReel[$k]['idAgent'] && $poste[$j]['idJour'] == $planReel[$k]['dateReel'] && $poste[$j]['horaireDeb'] == $planReel[$k]['horaireDeb'] && $poste[$j]['horaireFin'] == $planReel[$k]['horaireFin']) {
//                var_dump($poste[$j]);

                $poste[$j]['libPoste'] = $planReel[$k]['libPoste'];
                $poste[$j]['idPoste'] = $planReel[$k]['idPoste'];
                $poste[$j]['coulGroupe'] = $planReel[$k]['coulGroupe'];
//                var_dump($poste[$j]);
                $k = count($planReel);
            }
        }
    }
}
//var_dump($poste);
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
        <title>ALICE</title>
    </head>
    <body>
        <div class="container-fluid">
            <div>
                <img class="logo" src="images/logo_sna_quadri.png"/>
            </div>
            <table>
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

            <h2>
                <?php
// var_dump($tabDatesJoursSemaines);
                echo "Semaine n°" . $_SESSION['weekNumber'];
                echo "<br />";
                echo "Semaine du " . convertDateUsFr($tabDatesJoursSemaines[1]) . " au " . convertDateUsFr($tabDatesJoursSemaines[6]);
                echo "<br />";
                ?>
            </h2>
        </div>
        <div class="container">

            <div class="col-lg-2" >
                <table class="table table-bordered">
                    <tr>
                        <th>Personnel</th>
                    </tr>
                    <?php foreach ($user as $cle => $valeur) { ?>
                        <tr>
                            <td><?php echo $user[$cle]['prenom'] ?></td>
                        </tr>

                    <?php } ?>
                </table>
            </div>

            <div class="col-lg-10">
                <table class="table table-bordered">
                    <tr>
                        <th colspan="2">Lundi</th>
                        <th colspan="2">Mardi</th>
                        <th colspan="3">Mercredi</th>
                        <th colspan="2">Jeudi</th>
                        <th colspan="2">Vendredi</th>
                        <th colspan="2">Samedi</th>
                    </tr>
                    <tr>
                        <?php
                        for ($i = 0; $i < count($poste); $i++) {
                            $couleur = $poste[$i]['coulGroupe'];
                            echo "<td style='background-color:$couleur'>";
                            echo $poste[$i]['libPoste'];
                            echo "</td>";
                            $compte++;
                            if ($compte == 13) {
                                echo "</tr>";
                                echo "<tr>";
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