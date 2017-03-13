<?php
if (!isset($_SESSION['idAgent'])) {
    header("Location:../index.php");
}
?>
<div class="col-xs-2">
    <!-- Navigation -->
    <nav class="navbar navbar-static-top background-color-admin" role="navigation">
        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i> <?php echo $_SESSION["prenom"] . " " . $_SESSION["nom"] . " "; ?>
                    <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="../vues/decomptes.php">Décomptes</a>
                    </li>

                    <li><a href="../vues/mod_Plan_Reel.php">Modifier le planning</a>
                    </li>

                    <li><a href="../vues/mod_Plan_Std.php">Modifier le planning idéal</a>
                    </li>

                    <li><a href="../vues/mod_Agent.php">Modifier les agents</a>
                    </li>

                    <li><a href="../vues/mod_VacancesJF.php">Modifier dates vacances et jours fériés</a>
                    </li>

                    <li class="divider"></li>
                    <li><a href="../vues/alice_logout.php"><i class="fa fa-sign-out fa-fw"></i> Se
                            déconnecter</a>
                    </li>
                </ul>
            </li> 
        </ul>
    </nav>
</div>
</div>
</div>
</header>
