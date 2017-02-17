<!DOCTYPE html>
<html>
  <head>
   <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
		<link href="alice.css" rel="stylesheet">
        <title>ALICE</title>
  </head>
  <body>
	<div>
		<img class="logo" src="../images/logo_sna_quadri.png" />
   </div>
  
<?php
session_start(); // Utilisation des variables $_SESSION
?>

<?php
require_once ('../include/alice_fonctions.php');

// $dateJour = date("d/m/Y");
// $dayNumber = date ("w");

if (!isset($_POST['precedente']) && !isset($_POST['suivante'])) {
$_SESSION['weekNumber'] = date("W");
$_SESSION['year'] = date("Y");
}
// echo "La date du jour est : ". $dateJour;
// echo "<br />";
// echo "Le numéro du jour est : ". $dayNumber;
// echo "<br />";

	
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
<input type="submit" value =" " name="precedente" class="leftArrow">
</form>
</td>
<td>
<form action="" method="post">
<input type="submit" value =" " name="home" class="house">
</form>
</td>
<td>
<form action="" method="post">
<input type="submit" value =" " name ="suivante" class="rightArrow">
</form>
</td>
</tr>
</table>

<h2>
<?php
// Tableau des dates réelles du dimanche au samedi au format américain
$tabDatesJoursSemaines = datesJourSemaine($_SESSION['weekNumber'], $_SESSION['year']);
// var_dump($tabDatesJoursSemaines);
echo "Semaine n°". $_SESSION['weekNumber'];
echo "<br />";
echo "Semaine du ". convertDateUsFr($tabDatesJoursSemaines[1])." au ". convertDateUsFr($tabDatesJoursSemaines[6]);
echo "<br />";
?>
</h2>  
 
  </body>
</html>