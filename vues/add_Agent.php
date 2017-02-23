﻿<?php
session_start(); // Utilisation des variables $_SESSION
header('Content-Type:text/html; charset=UTF8');

require_once('../class/agent.php');
require_once('../include/alice_fonctions.php');
require_once('../include/alice_dao.inc.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap Core CSS -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Alice CSS -->
    <link href="../css/alice.css" rel="stylesheet">
    <!-- Chemin vers les librairies JavaScript -->
    <script src="../include/alice.js"></script>
    <title>Ajout d'un agent</title>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2">
            <img class="logo" src="../images/logo_sna_quadri.png"/>
        </div>

        <?php
        if (isset($_POST['nomForm']) && isset($_POST['prenomForm']) && isset($_POST['insertAgent'])) { // Cas du bouton orange "enregistrer"
            // var_dump($_POST);
            // exit;
            // Création d'un objet agent
            $agent = new Agent();

            $agent->setNom(addslashes(detecTiret($_POST['nomForm'])));
            $agent->setPrenom(addslashes(detecTiret($_POST['prenomForm'])));
            // Dans le cas où le statut n'existe pas : ni A, ni I,
            // on passe par une autre variable vide pour remplir l'objet
            /*
              if (!isset($_POST['statutForm'])) {
              $statut = " ";
              } else {
              $statut = $_POST['statutForm'];
              }
             * */
            $agent->setStatut($_POST['statutForm']);
            if (trim($_POST['mdp1Form']) == trim($_POST['mdp2Form'])) {
                $agent->setLogin(addslashes(trim($_POST['loginForm'])));
                $agent->setMdp(addslashes(trim($_POST['mdp2Form'])));
            }
            // Tous les agents appartiennent à la bibliothèque de VERNON -> A MODIFIER sur UNE MISE A JOUR
            $agent->setIdBiblio("V");
            // On met à jour la BDD agent
            $agent->insertAgent();
            // Retour à la page de modification des agents
            header("Location: mod_Agent.php");

        } else if (isset($_POST['annuler'])) {// Cas du bouton vert "annuler"
            // Retour à la page de modification des agents
            die('<META HTTP-equiv="refresh" content=0;URL=mod_Agent.php>');
        }
        ?>

        <!-- Affichage du titre de la page -->
        <h2>Ajout d'un agent</h2>
    </div>
</div>
<!-- Affichage des agents -->
<div class="container-flui col-lg-8">

    <!-- Formulaire des coordonnées d'un nouvel agent -->
    <form class="form-horizontal" action="add_Agent.php" method="POST">
        <fieldset>
            <!-- Nom de l'agent-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="nom">Nom</label>
                <div class="col-md-4">
                    <input size="20" id="nom" name="nomForm" placeholder="Nom" class="form-control input-md" required="" type="text">
                </div>
            </div>
            <!-- Prénom de l'agent-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="prenom">Prénom</label>
                <div class="col-md-4">
                    <input size="20" id="prenom" name="prenomForm" placeholder="Prénom" class="form-control input-md" required="" type="text">
                </div>
            </div>
            <!-- 2 Checkboxes statut -->
            <div class="form-group">
                <label class="col-md-4 control-label">Statut</label>
                <div class="col-md-4">
                    <label class="checkbox-inline">
                        <input name="statutForm" id="checkboxA" value="A" type="checkbox" onclick="activeLoginMdpForm()">
                        Administrateur
                    </label>
                    <label class="checkbox-inline">
                        <input name="statutForm" id="checkboxI" value="I" type="checkbox">
                        Inactif
                    </label>
                </div>
            </div>
            <!-- Saisie du login -->
            <div class="form-group">
                <label class="col-md-4 control-label"></label>
                <div class="col-md-4" id="divLogin" style='display:none;'>
                    <input size="20" type="text" id="login" name="loginForm" placeholder="Identifiant de connexion" class="form-control input-md">
                </div>
            </div>
            <!-- Saisie du MDP -->
            <div class="form-group">
                <label class="col-md-4 control-label"></label>
                <div class="col-md-4" id="divMdp1" style='display:none;'>
                    <input size="20" type="password" id="mdp1" name="mdp1Form" placeholder="Mot de passe" class="form-control input-md">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label"></label>
                <div class="col-md-4" id="divMdp2" style='display:none;'>
                    <input size="20" type="password" id="mdp2" name="mdp2Form" placeholder="Confirmez le mot de passe" class="form-control input-md">
                </div>
            </div>
            <!-- Button (Double) -->
            <div class="form-group">
                <label class="col-md-4 control-label"></label>
                <div class="col-md-8">
                    <button name="annuler" class="btn btn-success"><span class="glyphicon glyphicon-ban-circle"></span> Annuler</button>
                    <button name="insertAgent" class="btn btn-warning"><span class="glyphicon glyphicon-floppy-open"></span> Enregistrer</button>
                </div>
            </div>

        </fieldset>
    </form>
</div>

</body>
</html>