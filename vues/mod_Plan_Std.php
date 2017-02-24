<?php
session_start(); // Utilisation des variables $_SESSION

require_once('../class/agent.php');
require_once('../include/alice_fonctions.php');
require_once('../include/alice_dao.inc.php');
?>

<?php include("../include/doctype.php"); ?>

<!-- Affichage du titre de la page -->
<div class="col-lg-offset-2 col-lg-3">
    <h2>Modification planning standard</h2>
</div>
<?php include("../include/header_admin.php"); ?>

<body>
<div class="container-fluid">
    <table class="table table-bordered">
        <form class="form-horizontal" action="mod_Plan_Std.php" method="post">
            <?php
            $i = 0;
            $l = 0;
            while ($i < count($planStd)) { ?>
                <tr>
                    <td>
                        <?php echo $planStd[$i]['prenom']; ?>
                    </td>
                    <?php for ($j = 0; $j < 13; $j++) { ?>
                        <td>
                            <input type="hidden" name="idAgent<?php echo $l; ?>"
                                   value="<?php echo $planStd[$i]['idAgent']; ?>">
                            <input type="hidden" name="idJour<?php echo $l; ?>"
                                   value="<?php echo $planStd[$i]['idJour']; ?>">
                            <input type="hidden" name="horaireDeb<?php echo $l; ?>"
                                   value="<?php echo $planStd[$i]['horaireDeb']; ?>">
                            <input type="hidden" name="horaireFin<?php echo $l; ?>"
                                   value="<?php echo $planStd[$i]['horaireFin']; ?>">

                            <select name="idPoste<?php echo $l; ?>" class="form-control">
                                <?php for ($k = 0; $k < count($poste); $k++) {
                                    if ($poste[$k]['idPoste'] == $planStd[$i]['idPoste']) { ?>

                                        <option value="<?php echo $poste[$k]['idPoste']; ?>"
                                                selected=""><?php echo $poste[$k]['libPoste']; ?></option>

                                    <?php } else { ?>

                                        <option
                                            value="<?php echo $poste[$k]['idPoste']; ?>"><?php echo $poste[$k]['libPoste']; ?></option>

                                    <?php }
                                } ?>
                            </select>
                            <?php $i++ ?>
                        </td>
                    <?php }
                    $i++;
                    $l++; ?>
                </tr>
            <?php } ?>
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