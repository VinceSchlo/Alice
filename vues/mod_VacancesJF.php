<?php
session_start(); // Utilisation des variables $_SESSION

require_once('../class/ferie.php');
require_once('../class/vacances.php');
require_once('../include/alice_fonctions.php');
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
        <title>Modification des vacances et des jours fériés</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2">
                    <img class="logo" src="../images/logo_sna_quadri.png"/>
                </div>

                <?php
                // Création des objets jours fériés et Vacances
                $ferie = new Ferie();
                $vacances = new Vacances();
                // Création des tableaux issus des select en BDD pour l'affichage
                $tabFerie = $ferie->selectAllFerie();
                $tabVacances = $vacances->selectAllVacances();
                //         var_dump($tabFerie);
                //        var_dump($tabVacances);
                //          exit;

                if (isset($_POST['updateVac'])) { // Cas du bouton orange "enregistrer"
                    // var_dump($_POST);
                    // exit;
                    for ($i = 0; $i < count($tabVacances); $i++) {
                        $vacances->setIdVac($_POST['idVacForm' . $i]);
                        $vacances->setDateDebVac(convertDateFrUs($_POST['dateDebForm' . $i]));
                        $vacances->setDateFinVac(convertDateFrUs($_POST['dateFinForm' . $i]));
                        // On met à jour la BDD vacances
                        $vacances->updateVacances();
                        // On rafraîchit le select pour afficher les mdifs faites en BDD
                        $tabVacances = $vacances->selectAllVacances();
                    }
                }
                if (isset($_POST['updateFerie'])) { // Cas du bouton orange "enregistrer"
                    // var_dump($_POST);
                    // exit;
                    for ($i = count($tabVacances); $i < (count($tabVacances) + count($tabFerie)); $i++) {
                        $ferie->setIdFerie($_POST['idFerieForm' . $i]);
                        $ferie->setDateDebFerie(convertDateFrUs($_POST['dateDebForm' . $i]));
                        $ferie->setDateFinFerie(convertDateFrUs($_POST['dateFinForm' . $i]));
                        // On met à jour la BDD ferie
                        $ferie->updateFerie();
                        // On rafraîchit le select pour afficher les mdifs faites en BDD
                        $tabFerie = $ferie->selectAllFerie();
                    }
                }
                if (isset($_POST['annuler'])) {// Cas du bouton vert "annuler"
                    // Retour à la page d'accueil administrateur sans modification
                    // die('<META HTTP-equiv="refresh" content=0;URL=admin_modif_plan.php>');
                }
                ?>
                <!-- Affichage du titre de la page -->
                <h2>Modification des dates des vacances scolaires et des jours fériés</h2>
            </div>
        </div>

        <!-- Affichage des vacances -->
        <div class="container-fluid">
            <div class="col-lg-7">
                <table class="table table-bordered">
                    <tr class="color-grey">
                        <th class="thCentre">Vacances</th>
                        <th class="thCentre">Date de début</th>
                        <th class="thCentre">Date de fin</th>
                    </tr>
                    <br/>

                    <?php
                    for ($i = 0; $i < count($tabVacances); $i++) {
                        ?>
                        <form class="form-horizontal" method="POST" action="mod_VacancesJF.php">
                            <tr>
                            <input type="hidden" name="idVacForm<?php echo $i; ?>"
                                   value="<?php echo $tabVacances[$i]['idVac']; ?>">
                            <td>
                                <input class="form-control" disabled type="text" name="nomForm<?php echo $i; ?>"
                                       value="<?php echo $tabVacances[$i]['nomVac']; ?>">
                            </td>
                            <td>
                                <input class="form-control" type="text" name="dateDebForm<?php echo $i; ?>"
                                       value="<?php echo convertDateUsFr($tabVacances[$i]['dateDebVac']); ?>">
                            </td>
                            <td>
                                <input class="form-control" type="text" name="dateFinForm<?php echo $i; ?>"
                                       value="<?php echo convertDateUsFr($tabVacances[$i]['dateFinVac']); ?>">
                            </td>
                            </tr>
                        <?php } ?>
                        <!-- Affichage de 2 boutons -->
                        <button type="submit" name="annuler" class="btn btn-success">Annuler</button>
                        <button type="submit" name="updateVac" class="btn btn-warning">Enregistrer</button>
                    </form>
                </table>
            </div>
        </div>

        <!-- Affichage des jours fériés -->
        <div class="container-fluid">
            <div class="col-lg-7">
                <table class="table table-bordered">
                    <tr class="color-grey">
                        <th class="thCentre">Jours fériés</th>
                        <th class="thCentre">Date de début</th>
                        <th class="thCentre">Date de fin</th>
                    </tr>
                    <br/>

                    <?php
                    $j = 0;
                    for ($i = count($tabVacances); $i < (count($tabVacances) + count($tabFerie)); $i++) {
                        ?>
                        <form class="form-horizontal" method="POST" action="vacancesJF.php">
                            <tr>
                            <input type="hidden" name="idFerieForm<?php echo $i; ?>"
                                   value="<?php echo $tabFerie[$j]['idFerie']; ?>">
                            <td>
                                <input class="form-control" disabled type="text" name="nomForm<?php echo $i; ?>"
                                       value="<?php echo $tabFerie[$j]['nomFerie']; ?>">
                            </td>
                            <td>
                                <input class="form-control" type="text" name="dateDebForm<?php echo $i; ?>"
                                       value="<?php echo convertDateUsFr($tabFerie[$j]['dateDebFerie']); ?>">
                            </td>
                            <td>
                                <input class="form-control" type="text" name="dateFinForm<?php echo $i; ?>" value="<?php
                                if (empty($tabFerie[$j]['dateFinFerie'])) {
                                    echo convertDateUsFr($tabFerie[$j]['dateDebFerie']);
                                } else {
                                    echo convertDateUsFr($tabFerie[$j]['dateFinFerie']);
                                }
                                ?>">

                            </td>
                            </tr>
                            <?php
                            $j++;
                        }
                        ?>
                        <!-- Affichage de 2 boutons -->
                        <button type="submit" name="annuler" class="btn btn-success">Annuler</button>
                        <button type="submit" name="updateFerie" class="btn btn-warning">Enregistrer</button>
                    </form>
                </table>
            </div>
        </div>

    </body>
</html>