﻿<?php
session_start(); // Utilisation des variables $_SESSION

require_once('../class/agent.php');
require_once('../include/alice_fonctions.php');
require_once('../include/alice_dao.inc.php');
?>

<?php include("../include/doctype.php"); ?>

<!-- Affichage du titre de la page -->
<div class="col-lg-offset-2 col-lg-3">
    <h2>Ajout d'un agent</h2>
</div>
<?php include("../include/header_admin.php"); ?>

<?php
if (isset($_POST['nomForm']) && isset($_POST['prenomForm']) && isset($_POST['insertAgent'])) { // Cas du bouton orange "enregistrer"
    // var_dump($_POST);
    // exit;
    // Création d'un objet agent
    $agent = new Agent();
    $agent->setNom(addslashes(detecTiret($_POST['nomForm'])));
    $agent->setPrenom(addslashes(detecTiret($_POST['prenomForm'])));
    $agent->setStatut($_POST['statutForm']);
    $agent->setLogin(addslashes(trim($_POST['loginForm'])));
    $agent->setMdp(addslashes(trim($_POST['mdp2Form'])));
    // Tous les agents appartiennent à la bibliothèque de VERNON -> A MODIFIER sur UNE MISE A JOUR
    $agent->setIdBiblio("V");
    // On met à jour la BDD agent
    $agent->insertAgent();
    // Retour à la page de modification des agents
    header("Location: mod_Agent.php");
    //
} else if (isset($_POST['annuler'])) {// Cas du bouton vert "annuler"
    // Retour à la page de modification des agents
    die('<META HTTP-equiv="refresh" content=0;URL=mod_Agent.php>');
}
?>
<body>
<!-- Formulaire des coordonnées d'un nouvel agent -->
<form class="form-horizontal" action="add_Agent.php" onsubmit="return verif2MdpForm()" method="POST">
    <fieldset>
        <!-- Nom de l'agent-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="nom">Nom</label>
            <div class="col-md-4">
                <input size="20" id="nom" name="nomForm" placeholder="Nom" class="form-control input-md" required=""
                       type="text">
            </div>
        </div>
        <!-- Prénom de l'agent-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="prenom">Prénom</label>
            <div class="col-md-4">
                <input size="20" id="prenom" name="prenomForm" placeholder="Prénom" class="form-control input-md"
                       required="" type="text">
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
                <input size="20" type="text" id="login" name="loginForm" placeholder="Identifiant de connexion"
                       class="form-control input-md">
            </div>
        </div>
        <!-- Saisie du MDP -->
        <div class="form-group">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-4" id="divMdp1" style='display:none;'>
                <input size="20" type="password" id="mdp1" name="mdp1Form" placeholder="Mot de passe"
                       class="form-control input-md">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-4" id="divMdp2" style='display:none;'>
                <input size="20" type="password" id="mdp2" name="mdp2Form" placeholder="Confirmez le mot de passe"
                       class="form-control input-md">
            </div>
        </div>
        <!-- Button (Double) -->
        <div class="form-group">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-8">
                <button name="annuler" class="btn btn-success"><span class="glyphicon glyphicon-ban-circle"></span>
                    Annuler
                </button>
                <button name="insertAgent" class="btn btn-warning"><span class="glyphicon glyphicon-floppy-open"></span>
                    Enregistrer
                </button>
            </div>
        </div>

    </fieldset>
</form>
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