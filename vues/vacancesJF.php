<?php
session_start(); // Utilisation des variables $_SESSION

require_once ('../class/ferie.php');
require_once ('../class/vacances.php');
require_once('../include/alice_fonctions.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/alice.css" rel="stylesheet">
        <title>Modification des vacances et des jours fériés</title>
    </head>
    <body>
        <div>
            <img class="logo" src="../images/logo_sna_quadri.png" />
        </div>


        <?php
        if (isset($_POST['enregistrerVac'])) {
            $vacances->setNomVac($_POST['nomVacMod']);
            $vacances->setDateDebVac(convertDateFrUs($_POST['dateDebVacMod']));
            $vacances->setDateFinVac(convertDateFrUs($_POST['dateFinVacMod']));
            // On met à jour la BDD vacances
            $vacances->updateVacances();
        }
        if (isset($_POST['enregistrerFerie'])) {
            $ferie->setNomFerie($_POST['nomFerieMod']);
            $ferie->setDateDebFerie(convertDateFrUs($_POST['dateDebFerieMod']));
            if (isset($_POST['dateFinFerieMod'])) {
                $ferie->setDateFinFerie(convertDateFrUs($_POST['dateFinFerieMod']));
            }
            // On met à jour la BDD ferie
            $ferie->updateFerie();
        }
        if (isset($_POST['annuler'])) {
            // Retour à la page d'accueil administrateur sans modification
            // die('<META HTTP-equiv="refresh" content=0;URL=admin_modif_plan.php>');
        }

        // Création des objets jours fériés et Vacances
        $ferie = new Ferie();
        $vacances = new Vacances();
        // Création des tableaux issus des select en BDD
        $tabFerie = $ferie->selectFerie();
        $tabVacances = $vacances->selectVacances();
        // var_dump($tabFerie);
        // var_dump($tabVacances);
        ?>
        <!-- Affichage du titre de la page -->
        <h2>Modification des dates des vacances scolaires et des jours fériés</h2>

        <!-- Affichage des vacances -->
        <table>
            <tr>
                <th class="thCentre">Vacances</th>
                <th class="thCentre">Date de début</th>
                <th class="thCentre">Date de fin</th>
            </tr>
            <br />

            <?php foreach ($tabVacances as $cle => $valeur) { ?>
                <form class="form-horizontal" method="POST" action="vacancesJF.php">
                    <tr>
                        <td>
                            <input disabled type="text" name="nomVacMod" value="<?php echo $tabVacances[$cle]['nomVac']; ?>">
                        </td>
                        <td> 
                            <input type="text" name="dateDebVacMod" value="<?php echo convertDateUsFr($tabVacances[$cle]['dateDebVac']); ?>">
                        </td>
                        <td> 
                            <input type="text" name="dateFinVacMod" value="<?php echo convertDateUsFr($tabVacances[$cle]['dateFinVac']); ?>">
                        </td>
                        <td>
                            <!-- Affichage de 2 boutons -->
                            <button type="submit" name="annuler" class="btn btn-success">Annuler</button>
                            <button type="submit" name="enregistrerVac" class="btn btn-warning">Enregistrer</button>
                        </td>
                    </tr>
                </form>
            <?php } ?>
        </table>
        <hr />

        <!-- Affichage des jours fériés -->
        <table>
            <tr>
                <th class="thCentre">Jours fériés</th>
                <th class="thCentre">Date de début</th>
                <th class="thCentre">Date de fin</th>
            </tr>
            <br />

            <?php foreach ($tabFerie as $cle => $valeur) { ?>
                <form class="form-horizontal" method="POST" action="vacancesJF.php">
                    <tr>
                        <td>
                            <input disabled type="text" name="nomVacMod" value="<?php echo $tabFerie[$cle]['nomFerie']; ?>">
                        </td>
                        <td> 
                            <input type="text" name="dateDebVacMod" value="<?php echo convertDateUsFr($tabFerie[$cle]['dateDebFerie']); ?>">
                        </td>
                        <td> 
                            <input type="text" name="dateFinVacMod" value="<?php echo convertDateUsFr($tabFerie[$cle]['dateFinFerie']); ?>">
                        </td>
                    </tr>
                </form>
            <?php } ?>
        </table>

    </body>
</html>
