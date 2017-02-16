<!DOCTYPE html>
<html>
  <head>
   <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
		<link href="test.css" rel="stylesheet">
        <title>ALICE</title>
  </head>
  <body>
  <?php

$dateJour = date("d/m/Y");
$dayNumber = date ("w");
$weekNumber = date("W");
echo "La date du jour est : ". $dateJour;
echo "<br />";
echo "Le numéro du jour est : ". $dayNumber;
echo "<br />";
echo "Le numéro de la semaine est : ". $weekNumber;
echo "<br />";
	
   ?>
   
  </body>
</html>