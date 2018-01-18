<!-- Fixed navbar -->
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">SP<i class="fa fa-circle"></i>RT</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="index.php">Accueil</a></li>
                <?php
                    if(isset($_SESSION['utilisateur'])){
                        $rang = $_SESSION['utilisateur']->idstatusutilisateur;
                        if($rang == 4){
                            echo "<li><a href='gestionPlanningClient.php'>Client</a></li>";
                        }
                        if($rang == 3){
                            echo "<li><a href='gestionPlanningCoach.php'>Coach</a></li>";
                        }
                        if($rang <= 2){
                            echo "<li><a href='gestionPlanningClientEmploye.php'>Employé</a></li>";
                        }
                        if($rang == 1){
                            echo "<li><a href='gestionActiviteAdministrateur.php'>Administration</a></li>";
                        }
                        echo "<li><a href='vueGeneralCompte.php'>Mon compte</a></li>";
                        echo "<li><a href='deconnexion.php'>Deconnexion</a></li>";
                    }else{
                        echo "<li><a href='connexion.php'>Connexion/Inscription</a></li>";
                    }
                ?>

            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>