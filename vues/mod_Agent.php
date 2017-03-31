<?php
session_start(); // Utilisation des variables $_SESSION

require_once('../class/agent.php');
require_once('../include/alice_fonctions.php');
require_once('../include/alice_dao.inc.php');
?>

<?php
include("../include/doctype.php");
?>
<div class="col-xs-8">
    <br />
    <div class="row">
        <!-- Affichage du titre de la page -->
        <div class="col-md-12 border-table">
            <h2>Modification des agents</h2>
        </div>
    </div>
    <div class="row">
    </div>
</div>
<?php
include("../include/header_admin.php");

// Création d'un objet agent
$agent = new Agent();
// Création d'un tableau issu du select en BDD pour l'affichage
$tabAgent = $agent->selectAgentByName();

if (isset($_POST['updateAgent'])) { // Cas du bouton orange "enregistrer"
    $lengthTabAgent = count($tabAgent);
    for ($i = 0; $i < $lengthTabAgent; $i++) {
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
    }
}

if (isset($_POST['deleteAgent'])) {
    $agent->setIdAgent($_POST['deleteAgent']);
    // On met à jour la BDD agent
    $agent->deleteAgent();
}

if (isset($_POST['insertAgent'])) {
    header("Location: add_Agent.php");
}

if (isset($_POST['annuler'])) {// Cas du bouton vert "annuler"
    // Retour à la page d'accueil administrateur sans modification
    // die('<META HTTP-equiv="refresh" content=0;URL=admin_modif_plan.php>');
}
// On rafraîchit le select pour afficher les modifs faites en BDD
$tabAgent = $agent->selectAgentByName();
//
?>
<body class="background-color-admin">
    <!-- Affichage des agents -->
    <div class="container-fluid col-md-12 col-lg-offset-2 col-lg-8">
        <table class="table table-bordered">
            <thead class="theadFH">
                <tr class="color-grey">
                    <th class="thCentre">Nom</th>
                    <th class="thCentre">Prénom</th>
                    <th class="thCentre">Login</th>
                    <th class="thCentre">Mot de passe</th>
                    <th class="thCentre width-input-agent">Statut</th>
                    <th class="thCentre">Supprimer</th>
                </tr>
            </thead>
            <br/>
            <tbody>
                <?php
                $lengthTabAgent = count($tabAgent);
                for ($i = 0; $i < $lengthTabAgent; $i++) {
                    ?>
                <form class="form-horizontal" method="POST" action="mod_Agent.php">
                    <tr class="name-size-admin">
                    <input type="hidden" name="idAgentForm<?php echo $i; ?>"
                           value="<?php echo $tabAgent[$i]['idAgent']; ?>">
                    <td>
                        <input class="form-control" type="text" name="nomForm<?php echo $i; ?>"
                               value="<?php echo $tabAgent[$i]['nom']; ?>">
                    </td>
                    <td>
                        <input class="form-control" type="text" name="prenomForm<?php echo $i; ?>"
                               value="<?php echo $tabAgent[$i]['prenom']; ?>">
                    </td>
                    <td>
                        <input class="form-control" type="text" name="loginForm<?php echo $i; ?>"
                               value="<?php echo $tabAgent[$i]['login']; ?>">
                    </td>
                    <td>
                        <input class="form-control" type="password" name="mdpForm<?php echo $i; ?>"
                               value="<?php echo $tabAgent[$i]['mdp']; ?>">
                    </td>
                    <td class="width-input-agent">
                        <input type="checkbox" name="statutForm<?php echo $i; ?>" id="checkboxA" value="A"
                        <?php
                        if ($tabAgent[$i]['statut'] == "A") {
                            echo " checked";
                        }
                        ?>> Administrateur
                        <br />
                        <input type="checkbox" name="statutForm<?php echo $i; ?>" id="checkboxI" value="I"
                        <?php
                        if ($tabAgent[$i]['statut'] == "I") {
                            echo " checked";
                        }
                        ?>> Inactif
                    </td>
                    <td>
                        <!-- Bouton Supprimer -->
                        <button type="submit" name="deleteAgent" class="btn btn-danger"
                                value="<?php echo $tabAgent[$i]['idAgent']; ?>"
                                onclick="return confirmDeleteAgent<?php echo $i; ?>()"><span class="glyphicon glyphicon-trash"></span> Supprimer
                        </button>
                        <!-- fonction pour activer une fenétre de suppression avec le prenom et le nom -->
                        <script type="text/javascript">
                            function confirmDeleteAgent<?php echo $i; ?>() {
                                return confirm("Etes-vous sûr de vouloir supprimer <?php echo $tabAgent[$i]['prenom'] . " " . $tabAgent[$i]['nom']; ?> ?");
                            }
                        </script>
                    </td>
                    </tr>
                <?php } ?>
                <!-- Affichage de 3 boutons -->
                <div class="col-md-6 pull-right text-right">
                    <button type="submit" name="annuler" class="btn btn-success"><span
                            class="glyphicon glyphicon-ban-circle"></span> Annuler
                    </button>
                    <button type="submit" name="updateAgent" class="btn btn-warning"><span
                            class="glyphicon glyphicon-floppy-open"></span> Enregistrer
                    </button>
                    <button type="submit" name="insertAgent" class="btn btn-primary"><span
                            class="glyphicon glyphicon-user"></span> Nouvel Agent
                    </button>
                </div>
                </td>
            </form>
            </tbody>
        </table>
    </div>

    <!-- Header flottant -->
    <script type="text/javascript">
        var tables = document.getElementsByTagName('table');
        lrStickyHeader(tables[0]);
    </script>

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