<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
         <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../css/alice.css" rel="stylesheet">
        <title>Modification des vacances et des jours fériés</title>
    </head>
    <body>
         <div>
             <img class="logo" src="../images/logo_sna_quadri.png" />
        </div>
        
        <?php
        session_start(); // Utilisation des variables $_SESSION
        ?>
        
        <?php
        require_once ('../class/ferie.php');
        require_once ('../class/vacances.php');
        
        // put your code here
        ?>
    </body>
</html>
