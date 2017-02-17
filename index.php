<?php

session_start(); // Utilisation des variables $_SESSION

require_once('include/alice_dao.inc.php');
require_once('class/Agent.php');
require_once('class/PlanStd.php');
require_once('include/alice_fonctions.php');

$plan = new PlanStd();
$user = $plan->selectUser();
$poste = $plan->selectPlanStd();
$j = 0;
$t = 10;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="css/alice.css" rel="stylesheet">
    <title>ALICE</title>
</head>
<body>
<div>
    <img class="logo" src="images/logo_sna_quadri.png"/>
</div>

<?php


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
?>

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
    // Tableau des dates réelles du dimanche au samedi au format américain
    $tabDatesJoursSemaines = datesJourSemaine($_SESSION['weekNumber'], $_SESSION['year']);
    // var_dump($tabDatesJoursSemaines);
    echo "Semaine n°" . $_SESSION['weekNumber'];
    echo "<br />";
    echo "Semaine du " . convertDateUsFr($tabDatesJoursSemaines[1]) . " au " . convertDateUsFr($tabDatesJoursSemaines[6]);
    echo "<br />";
    ?>
</h2>

<table style="display: inline-block;">
    <tr>
        <th colspan="2">Lundi</th>
        <th colspan="2">Mardi</th>
        <th colspan="3">Mercredi</th>
        <th colspan="2">Jeudi</th>
        <th colspan="2">Vendredi</th>
        <th colspan="2">Samedi</th>
    </tr>
    <tr>
        <?php for ($i = 0; $i < count($poste); $i++) {
            $couleur = $poste[$i]['coulGroupe'];
            echo "<td style='background-color:$couleur'>";
            echo $poste[$i]['libPoste'];
            echo "</td>";
            $j++;
            if ($j == 13) {
                echo "</tr>";
                echo "<tr>";
                $j = 0;
            }
        } ?>
    </tr>

</table>

<table style="float: left;">
    <tr>
        <th>Personnel</th>
    </tr>
    <?php foreach ($user as $cle => $valeur) { ?>
        <tr>
            <td><?php echo $user[$cle]['prenom'] ?></td>
        </tr>

    <?php } ?>
</table>


</body>
</html>