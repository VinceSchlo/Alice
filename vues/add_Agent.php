<?php
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
        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
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


                <!-- Affichage du titre de la page -->
                <h2>Ajout d'un agent</h2>
            </div>
        </div>
        <!-- Affichage des agents -->
        <div class="container-flui col-lg-8">
            <table class="table table-bordered">
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
                        <br />
                        <br />
                        <!-- Prénom de l'agent-->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="prenom">Prénom</label>  
                            <div class="col-md-4">
                                <input size="20" id="prenom" name="prenomForm" placeholder="Prénom" class="form-control input-md" required="" type="text">

                            </div>
                        </div>
                        <br />
                        <br />
                        <!-- 2 Checkboxes statut -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="checkboxes">Statut</label>
                            <div class="col-md-4">
                                <label class="checkbox-inline" for="checkboxes-0">
                                    <input name="statutForm" id="checkboxA" value="A" type="checkbox">
                                    Administrateur
                                </label>
                                <label class="checkbox-inline" for="checkboxes-1">
                                    <input name="statutForm" id="checkboxI" value="I" type="checkbox">
                                    Inactif
                                </label>
                            </div>
                        </div>
                        <br />
                        <br />
                        <!-- Button (Double) -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="button1id"></label>
                            <div class="col-md-8">
                                <button name="annuler" class="btn btn-info">Retour</button>
                                <button name="insertAgent" class="btn btn-success">Validez</button>
                            </div>
                        </div>

                    </fieldset>
                </form>
            </table>
        </div>
        <?php
        if (isset($_POST['insertAgent']) && ($_POST['statutForm'] == 'A')) { // Cas du bouton orange "enregistrer"
            //var_dump($_POST);
            //exit;
            ?>
            <!-- Formulaire du login et du mot de passe -->
            <form class="form-horizontal" action="add_Agent.php" method="POST">
                <!-- Saisie du login -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="mdp">Identifiant de connexion</label>  
                    <div class="col-md-4">
                        <input size="20" type="text" id="login" name="loginForm" placeholder="Login" required="">
                    </div>
                </div>
                <br />
                <br />
                <!-- Saisie du MDP -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="mdp">Mot de passe</label>  
                    <div class="col-md-4">
                        <input size="20" type="text" id="mdp1" name="mdp1Form" required="">
                    </div>
                </div>
                <br />
                <div class="form-group">
                    <label class="col-md-4 control-label" for="mdp">Confirmez le de passe</label>  
                    <div class="col-md-4">
                        <input size="20" type="text" id="mdp2" name="mdp2Form" required="">
                    </div>
                </div>
                <br />
            </form>
            <?php
        }
        if (isset($_POST['insertAgent']) && !isset($_POST['statutForm'])) { // Cas du bouton orange "enregistrer"
            // var_dump($_POST);
            // exit;
            // Création d'un objet agent
            $agent = new Agent();
            $agent->setIdAgent($_POST['idAgentForm' . $i]);
            $agent->setNom(addslashes(detecTiret($_POST['nomForm' . $i])));
            $agent->setPrenom(addslashes(detecTiret($_POST['prenomForm' . $i])));
            $agent->setLogin(addslashes(trim($_POST['loginForm' . $i])));
            $agent->setMdp(addslashes(trim($_POST['mdpForm' . $i])));
            // Dans le cas où le statut n'existe pas : ni A, ni I, 
            // on passe par une autre variable vide pour remplir l'objet 
            if (!isset($_POST['statutForm' . $i])) {
                $statut = " ";
            } else {
                $statut = $_POST['statutForm' . $i];
            }
            $agent->setStatut($statut);
            // On met à jour la BDD agent
            $agent->updateAgent();
            // On rafraîchit le select pour afficher les mdifs faites en BDD
            $tabAgent = $agent->selectAllAgent();
            // Retour à la page de modification des agents
            header("Location: mod_Agents.php");
        }

        if (isset($_POST['annuler'])) {// Cas du bouton vert "annuler"
            // Retour à la page de modification des agents
            die('<META HTTP-equiv="refresh" content=0;URL=mod_Agent.php>');
        }
        ?>
    </body>
</html>