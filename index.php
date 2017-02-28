<?php
session_start(); // Utilisation des variables $_SESSION

require_once('include/alice_dao.inc.php');
require_once('include/alice_fonctions.php');
require_once('class/Agent.php');
require_once('class/PlanStd.php');
require_once('class/PlanReel.php');
require_once('class/Ferie.php');
require_once('class/horaire.php');
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
    <script src="include/alice.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"
            integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn"
            crossorigin="anonymous"></script>
    <title>ALICE</title>
</head>

<?php

$oAgent = new Agent();
$user = $oAgent->selectUser();

$plan = new PlanStd();
$tabPlanStd = $plan->selectPlanStd();
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

// Verification connexion Agent
if (isset($_POST['login']) && isset($_POST['mdp'])) {
// Création objet $connection de la class Agent
    $connexion = new Agent();

// On résupère le login et le mdp saisie pas l'agent
    $connexion->setLogin($_POST["login"]);
    $connexion->setMdp($_POST["mdp"]);

// On éxécute la fonction pour vérifier si l'agent a rentré les bonnes informations
    $agent = $connexion->connexionAgent();
// Si l'utlisateur n'éxiste pas retour a l'index
    if (!isset($agent)) {
        //Si idientifiant ou mdp faux alert JAVAscript
        ?>
        <script>alert('Mauvais login ou mdp')</script> <?php
    } else {
        // Si l'utilisateur existe garnir la variable $_SESSION
        $_SESSION = $agent;

        header("Location:vues/mod_Agent.php");
    }
} else
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
                    echo "Semaine n°" . $_SESSION['weekNumber'];
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

        <!--        Formulaire de connexion -->
        <div class="col-lg-1">
            <?php
            echo "<br />";
            ?>
            <button class="btn btn-default btn-lg color-button" onclick="connexion()"><span
                    class="glyphicon glyphicon-user"></span> Se
                connecter
            </button>
            <div id="connexion" style="display: none">
                <form class="form-group" action="index.php" method="POST">
                    <label for="login">Login</label>
                    <input class="form-control" name="login" id="login" type="text" required>

                    <label for="mdp">Mot de passe</label>
                    <input class="form-control" name="mdp" id="mdp" type="password" required>
                    <button class="glyphicon glyphicon-off btn-warning btn pull-right" name="valider"></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">

    <div class="col-lg-12">
        <table class="table border-table">
            <!--            Affichage des jours-->
            <tr class="color-grey text-size">
                <th class="border-right"></th>
                <th class="text-center border-right" colspan="2">Lundi</th>
                <th class="text-center border-right" colspan="2">Mardi</th>
                <th class="text-center border-right" colspan="3">Mercredi</th>
                <th class="text-center border-right" colspan="2">Jeudi</th>
                <th class="text-center border-right" colspan="2">Vendredi</th>
                <th class="text-center border-right" colspan="2">Samedi</th>
            </tr>
            <!--            Affichage des horraires -->
            <tr class="color-grey name-size border-right">
                <td class="border-right">Personnel</td>
                <?php
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
            <!--  Affichage du planing -->
            <?php
            $i = 0;

            while ($i < count($tabPlanStd)) { ?>
                <tr class="poste-size border-right">
                    <td class="color-grey border-right">
                        <?php echo $tabPlanStd[$i]['prenom']; ?>
                    </td>
                    <?php for ($j = 0; $j < 13; $j++) {

                        $couleur = $tabPlanStd[$i]['coulGroupe'];
                        switch ($j) {
                            case 1:
                            case 3:
                            case 6:
                            case 8:
                            case 10:
                            case 12:
                                echo "<td class='text-center border-right' style='background-color:$couleur'>";
                                break;
                            default:
                                echo "<td class='text-center border-top-bot' style='background-color:$couleur'>";
                                break;
                        }
                        echo $tabPlanStd[$i]['libPoste'];
                        echo "</td>";
                        $i++;
                    } ?>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>

</body>
</html>