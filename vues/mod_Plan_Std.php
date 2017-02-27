<?php
session_start(); // Utilisation des variables $_SESSION

require_once('../class/Agent.php');
require_once('../class/Poste.php');
require_once('../class/PlanStd.php');
require_once('../class/Horaire.php');
require_once('../include/alice_fonctions.php');
require_once('../include/alice_dao.inc.php');
?>

<?php include("../include/doctype.php"); ?>

<!-- Affichage du titre de la page -->
<div class="col-lg-offset-2 col-lg-3">
    <h2>Modification du planning standard</h2>
</div>
<?php include("../include/header_admin.php"); ?>

<body>
<div class="container-fluid">
    <table class="table table-bordered">
        <!--            Affichage des jours-->
        <tr class="color-grey text-size">
            <th></th>
            <th class="text-center" colspan="2">Lundi</th>
            <th class="text-center" colspan="2">Mardi</th>
            <th class="text-center" colspan="3">Mercredi</th>
            <th class="text-center" colspan="2">Jeudi</th>
            <th class="text-center" colspan="2">Vendredi</th>
            <th class="text-center" colspan="2">Samedi</th>
        </tr>
        <!--            Affichage des horraires -->
        <tr class="color-grey name-size">
            <td>Personnel</td>
            <?php

            $oHoraire = new Horaire();
            $time = $oHoraire->selectHoraire();

            for ($i = 0; $i < 4; $i++) {
                if ($i % 2 == 0) {
                    echo "<td class=\"text-center\">";
                    echo substr($time[1]['libHoraire'], 0, 5), " - ";
                    echo substr($time[3]['libHoraire'], 0, 5);
                    echo "</td>";
                }
                if ($i % 2 != 0) {
                    echo "<td class=\"text-center\">";
                    echo substr($time[3]['libHoraire'], 0, 5), " - ";
                    echo substr($time[6]['libHoraire'], 0, 5);
                    echo "</td>";
                }
            }
            echo "<td class=\"text-center\">";
            echo substr($time[0]['libHoraire'], 0, 5), " - ";
            echo substr($time[2]['libHoraire'], 0, 5);
            echo "</td>";
            echo "<td class=\"text-center\">";
            echo substr($time[2]['libHoraire'], 0, 5), " - ";
            echo substr($time[3]['libHoraire'], 0, 5);
            echo "</td>";
            echo "<td class=\"text-center\">";
            echo substr($time[3]['libHoraire'], 0, 5), " - ";
            echo substr($time[6]['libHoraire'], 0, 5);
            echo "</td>";
            for ($i = 0; $i < 4; $i++) {
                if ($i % 2 == 0) {
                    echo "<td class=\"text-center\">";
                    echo substr($time[1]['libHoraire'], 0, 5), " - ";
                    echo substr($time[3]['libHoraire'], 0, 5);
                    echo "</td>";
                }
                if ($i % 2 != 0) {
                    echo "<td class=\"text-center\">";
                    echo substr($time[3]['libHoraire'], 0, 5), " - ";
                    echo substr($time[6]['libHoraire'], 0, 5);
                    echo "</td>";
                }
            }
            echo "<td class=\"text-center\">";
            echo substr($time[0]['libHoraire'], 0, 5), " - ";
            echo substr($time[2]['libHoraire'], 0, 5);
            echo "</td>";
            echo "<td class=\"text-center\">";
            echo substr($time[2]['libHoraire'], 0, 5), " - ";
            echo substr($time[4]['libHoraire'], 0, 5);
            echo "</td>";
            ?>
        </tr>
        <form class="form-horizontal" action="mod_Plan_Std.php" method="post">
            <?php

            $oPlanStd = new PlanStd();
            $tabPlanStd = $oPlanStd->selectPlanStd();

            $oPoste = new Poste();
            $poste = $oPoste->selectAllPoste();

            if (isset($_POST['enregistrer'])) { // Cas du bouton orange "enregistrer"
//                 var_dump($_POST);
//                 exit;
                for ($i = 0; $i < count($tabPlanStd); $i++) {
                    $oPlanStd->setIdAgent($_POST['idAgentForm' . $i]);
                    $oPlanStd->setIdJour($_POST['idJourForm' . $i]);
		            $oPlanStd->setHoraireDeb($_POST['horaireDebForm' . $i]);
		            $oPlanStd->setHoraireFin($_POST['horaireFinForm' . $i]);
		            $oPlanStd->setIdPoste($_POST['idPosteForm' . $i]);
                    // On met à jour la BDD planstd
                    $oPlanStd->insertPlanStd();
                }
            }

            if (isset($_POST['annuler'])) {// Cas du bouton vert "annuler"
                // Retour à la page d'accueil administrateur sans modification
                // die('<META HTTP-equiv="refresh" content=0;URL=admin_modif_plan.php>');
            }
            
            // On rafraîchit le select pour afficher les modifs faites en BDD
            $tabPlanStd = $oPlanStd->selectPlanStd();

            $i = 0;
            $l = 0;

            while ($i < count($tabPlanStd)) { ?>
                <tr>
                    <td>
                        <?php echo $tabPlanStd[$i]['prenom']; ?>
                    </td>
                    <?php for ($j = 0; $j < 13; $j++) { ?>
                        <td>
                            <input type="hidden" name="idAgentForm<?php echo $l; ?>"
                                   value="<?php echo $tabPlanStd[$i]['idAgent']; ?>">
                            <input type="hidden" name="idJourForm<?php echo $l; ?>"
                                   value="<?php echo $tabPlanStd[$i]['idJour']; ?>">
                            <input type="hidden" name="horaireDebForm<?php echo $l; ?>"
                                   value="<?php echo $tabPlanStd[$i]['horaireDeb']; ?>">
                            <input type="hidden" name="horaireFinForm<?php echo $l; ?>"
                                   value="<?php echo $tabPlanStd[$i]['horaireFin']; ?>">

                            <select name="idPosteForm<?php echo $l; ?>" class="form-control">
                                <?php for ($k = 0; $k < count($poste); $k++) {
                                    if ($poste[$k]['idPoste'] == $tabPlanStd[$i]['idPoste']) { ?>

                                        <option value="<?php echo $poste[$k]['idPoste']; ?>"
                                                selected=""
                                                style="background-color: <?php echo $poste[$k]['coulGroupe'] ?>"><?php echo $poste[$k]['libPoste']; ?></option>

                                    <?php } else { ?>

                                        <option
                                            value="<?php echo $poste[$k]['idPoste']; ?>"
                                            style="background-color: <?php echo $poste[$k]['coulGroupe'] ?>"><?php echo $poste[$k]['libPoste']; ?></option>

                                    <?php }
                                } ?>
                            </select>
                            <?php $i++;
                            $l++; ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            <div class="col-lg-offset-10 col-lg-2">
                <!-- Affichage de 2 boutons -->
                <button type="submit" name="annuler" class="btn btn-success"
                        class="glyphicon glyphicon-ban-circle"><span
                        class="glyphicon glyphicon-ban-circle"></span> Annuler
                </button>
                <button type="submit" name="enregistrer" class="btn btn-warning"><span
                        class="glyphicon glyphicon-floppy-open"></span> Enregistrer
                </button>
            </div>
        </form>
    </table>
</div>


</body>
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