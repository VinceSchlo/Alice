<div class="col-lg-offset-2 col-lg-2">
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i> <?php echo $_SESSION["prenom"] . " " . $_SESSION["nom"] . " "; ?>
                    <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href=""></i>Décomptes</a>
                    </li>

                    <li><a href=""></i>Modifier le planning</a>
                    </li>

                    <li><a href=""></i>Modifier le planning type</a>
                    </li>

                    <li><a href="../vues/mod_Agent.php"></i>Modifier les agents</a>
                    </li>

                    <li><a href="../vues/mod_VacancesJF.php"></i>Modifier les horaires des vacances et des
                            jours fériés</a>
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
