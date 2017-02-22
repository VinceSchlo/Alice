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
        <title>Modification des agents</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2">
                    <img class="logo" src="../images/logo_sna_quadri.png"/>
                </div>

                <?php
                // Création d'un objet agent
                $agent = new Agent();
                // Création d'un tableau issu du select en BDD pour l'affichage
                $tabAgent = $agent->selectAllAgent();
                // var_dump($tabAgent);
                // exit;

                if (isset($_POST['updateAgent'])) { // Cas du bouton orange "enregistrer"
                    var_dump($_POST);
                    exit;
                    for ($i = 0; $i < count($tabAgent); $i++) {
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
                    }
                }

                if (isset($_POST['deleteAgent'])) {
                    /*
                     * ?>
                      <script type="text/javascript">
                      confirm("Attention, vous allez supprimer un agent");
                      </script>
                      <?php
                     * 
                     */
                    $agent->setIdAgent($_POST['deleteAgent']);
                    // On met à jour la BDD agent
                    $agent->deleteAgent();
                    // On rafraîchit le select pour afficher les mdifs faites en BDD
                    $tabAgent = $agent->selectAllAgent();
                }

                if (isset($_POST['insertAgent'])) {
                    header("Location: add_Agent.php");
                }

                if (isset($_POST['annuler'])) {// Cas du bouton vert "annuler"
                    // Retour à la page d'accueil administrateur sans modification
                    // die('<META HTTP-equiv="refresh" content=0;URL=admin_modif_plan.php>');
                }
                ?>
                <!-- Affichage du titre de la page -->
                <h2>Modification des agents</h2>
            </div>
        </div>
        <!-- Affichage des agents -->
        <div class="container-flui col-lg-8">
            <table class="table table-bordered">
                <tr class="color-grey">
                    <th class="thCentre">Nom</th>
                    <th class="thCentre">Prénom</th>
                    <th class="thCentre">Login</th>
                    <th class="thCentre">Mot de passe</th>
                    <th class="thCentre">Statut</th>
                    <th class="thCentre">Supprimer</th>
                </tr>
                <br/>

                <?php
                for ($i = 0; $i < count($tabAgent); $i++) {
                    ?>
                    <form class="form-horizontal" method="POST" action="mod_Agent.php">
                        <tr>
                        <input type="hidden" name="idAgentForm<?php echo $i; ?>" value="<?php echo $tabAgent[$i]['idAgent']; ?>">
                        <td>
                            <input class="form-control" type="text" name="nomForm<?php echo $i; ?>" value="<?php echo $tabAgent[$i]['nom']; ?>">
                        </td>
                        <td>
                            <input class="form-control" type="text" name="prenomForm<?php echo $i; ?>" value="<?php echo $tabAgent[$i]['prenom']; ?>">
                        </td>
                        <td>
                            <input class="form-control" type="text" name="loginForm<?php echo $i; ?>" value="<?php echo $tabAgent[$i]['login']; ?>">
                        </td>
                        <td>
                            <input class="form-control" type="text" name="mdpForm<?php echo $i; ?>" value="<?php echo $tabAgent[$i]['mdp']; ?>">
                        </td>
                        <td>
                            <input type="checkbox" class="checkBox" name="statutForm<?php echo $i; ?>" id="checkboxA" value="A"
                            <?php
                            if ($tabAgent[$i]['statut'] == "A") {
                                echo " checked";
                            }
                            ?>>Administrateur
                            <input type="checkbox" class="checkBox" name="statutForm<?php echo $i; ?>" id="checkboxI" value="I"
                            <?php
                            if ($tabAgent[$i]['statut'] == "I") {
                                echo " checked";
                            }
                            ?>>Inactif
                        </td>
                        <td>
                            <!-- Bouton Supprimer -->
                            <button type="submit" name="deleteAgent" class="croixRouge" value="<?php echo $tabAgent[$i]['idAgent']; ?>"></button>
                        </td>
                        </tr>
                    <?php } ?>
                    <!-- Affichage de 4 boutons -->
                    <button type="submit" name="annuler" class="btn btn-success">Annuler</button>
                    <button type="submit" name="updateAgent" class="btn btn-warning">Enregistrer</button>
                    <button type="submit" name="insertAgent" class="btn btn-primary">Nouvel Agent</button>
                    </td>
                </form>
            </table>
        </div>

    </body>
</html>