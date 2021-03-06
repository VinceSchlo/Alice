<?php
session_start(); // Utilisation des variables $_SESSION

require_once('class/Agent.php');
require_once('class/PlanStd.php');
require_once('class/PlanReel.php');
require_once('class/Ferie.php');
require_once('class/Vacances.php');
require_once('class/Horaire.php');
require_once('include/alice_fonctions.php');
require_once('include/alice_dao.inc.php');
//
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
        <link rel="stylesheet" type="text/css" href="css/calendrier.css"/>
        <script type="text/javascript" src="include/jsSimpleDatePickr.2.1.js"></script>
        <script src="include/alice.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
              integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ"
              crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"
                integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn"
        crossorigin="anonymous"></script>
        <title>ALICE</title>
    </head>

    <?php
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

    if (isset($_POST['btnCalendar'])) {
        $_SESSION['weekNumber'] = ltrim(date('W', strtotime($_POST['dateCalendrier'])), "0");
        $_SESSION['year'] = date('Y', strtotime($_POST['dateCalendrier']));
    }

// Tableau des dates réelles du dimanche au samedi au format américain
    $tabDatesJoursSemaines = datesJourSemaine($_SESSION['weekNumber'], $_SESSION['year']);
// // Selection des agents
    $oAgent = new Agent();
    $user = $oAgent->selectPrenomAgent();
// Selection du planning standard de la semaine
    $oPlanStd = new PlanStd();
    $tabPlanStd = $oPlanStd->selectPlanStdInactif();
// Selection des plannings réels de la semaine
    $oPlanReel = new PlanReel();
    $planReel = $oPlanReel->selectPlanReel($tabDatesJoursSemaines[1], $tabDatesJoursSemaines[6]);
// Selection des jours fériés
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

// Je remplace les données du planning standard par le planning réel (coulGroupe, idPoste, libPoste)
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

// On résupère le login et le mdp saisis par l'agent
        $connexion->setLogin($_POST["login"]);
        $connexion->setMdp($_POST["mdp"]);

// On éxécute la fonction pour vérifier si l'agent a rentré les bonnes informations
        $agent = $connexion->connexionAgent();
// Si l'utlisateur n'éxiste pas retour a l'index
        if (!isset($agent)) {
            //Si identifiant ou mdp faux alert JAVAscript
            ?>
            <script>alert('Mauvais login ou mot de passe,\nveuillez réessayer')</script> <?php
        } else {
// Si l'utilisateur existe garnir la variable $_SESSION
            $_SESSION = $agent;
            header("Location:vues/mod_Plan_Reel.php");
        }
    } else
        
        ?>

    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2">
                    <div class="row">
                        <img class="logo" src="images/logo_sna_Alice.png"/>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="row">
                        <div class="center-block">
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
                        <div class="center-block">
                            <h2>
                                <?php
                                if ($_SESSION['weekNumber'] < 10) {
                                    echo "Semaine n°" . "0" . $_SESSION['weekNumber'] . " du " . convertDateUsFr($tabDatesJoursSemaines[1]) . " au " . convertDateUsFr($tabDatesJoursSemaines[6]);
                                } else {
                                    echo "Semaine n°" . $_SESSION['weekNumber'] . " du " . convertDateUsFr($tabDatesJoursSemaines[1]) . " au " . convertDateUsFr($tabDatesJoursSemaines[6]);
                                }
                                ?>
                            </h2>
                        </div>
                    </div>
                </div>
                <!-- Calendrier -->
                <div class="col-md-3 btn-calendar">
                    <div class="row">
                        <div class="calendar-position pull-left">
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

                        <div class="calendar-position">
                            <form action="index.php" method="post">
                                <input type="text" hidden id="dateCalendrier" name="dateCalendrier">
                                <button type="submit" class="btn btn-lg btn-default btn-primary" name="btnCalendar">
                                    <span class="glyphicon glyphicon-fast-forward"></span> Allez à
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Fin Calendrier-->
                <!-- Formulaire de connexion -->
                <div class="col-md-1">
                    <?php
                    echo "<br />";
                    ?>
                    <button class="btn btn-default btn-lg color-button" onclick="connexion()">
                        <span class="glyphicon glyphicon-user"></span> Se connecter
                    </button>
                    <div id="connexion" style="display: none">
                        <form class="form-group" action="index.php" method="POST">
                            <label for="login">Login</label>
                            <input class="form-control" name="login" id="login" type="text" required>
                            <label for="mdp">Mot de passe</label>
                            <div class="form-group">
                                <input class="form-control" name="mdp" id="mdp" type="password" required>
                                <button class="glyphicon glyphicon-log-in btn-warning btn" name="valider"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="col-lg-12">
            <table class="table border-table">
                <!-- Affichage des jours -->
                <tr class="color-grey size-day">
                    <th class="border-right"></th>
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
                <!-- Affichage des horaires -->
                <tr class="color-grey size-hour border-right">
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
                <!-- Affichage du planning -->
                <?php
                $i = 0;
                while ($i < count($tabPlanStd)) {
                    ?>
                    <tr class="poste-size border-right">
                        <td class="color-grey border-right name-size">
                            <?php echo $tabPlanStd[$i]['prenom']; ?>
                        </td>
                        <?php
                        for ($j = 0; $j < 13; $j++) {
                            $couleur = $tabPlanStd[$i]['coulGroupe'];
                            switch ($j) {
                                case 1:
                                case 3:
                                case 6:
                                case 8:
                                case 10:
                                case 12:
                                    echo "<td class='text-center border-right name-size' style='background-color:$couleur'>";
                                    break;
                                default:
                                    echo "<td class='text-center border-top-bot name-size' style='background-color:$couleur'>";
                                    break;
                            }
                            echo $tabPlanStd[$i]['libPoste'];
                            echo "</td>";
                            $i++;
                        }
                        ?>
                    </tr>
                <?php } ?>
                <!--            Affichage des horaires -->
                <tr class="color-grey size-hour border-right">
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
                <!-- Affichage des jours en bas -->
                <tr class="color-grey size-day">
                    <th class="border-right"></th>
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
        </div>
    </table>
</div>
</div>
</body>
</html>