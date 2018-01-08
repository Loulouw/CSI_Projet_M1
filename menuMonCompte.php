<div class="col-md-3">
    <div class="profile-sidebar">
        <div class="profile-userpic">
            <img src="images/utilisateur.png" class="img-responsive" alt="">
        </div>
        <!-- SIDEBAR USER TITLE -->
        <div class="profile-usertitle">
            <div class="profile-usertitle-name">
                <?php
                    echo $_SESSION['utilisateur']->prenom . " " . strtoupper($_SESSION['utilisateur']->nom);
                ?>
            </div>
            <hr>
            <div class="profile-usertitle-annonce-job">
                Séances restante :
            </div>
            <div class="profile-usertitle-job">
                <?php
                    echo $_SESSION['utilisateur']->nombreseancedisponible;
                ?>
            </div>
            <div class="profile-usertitle-annonce-job">
                Durée abonnement restante :
            </div>
            <div class="profile-usertitle-job">
                <?php
                    $abonnement = $db->getAbonnementEnCours($_SESSION['utilisateur']->id);
                    if($abonnement != null){
                        echo $db->getJoursRestantAbonnement($_SESSION['utilisateur']->id);
                    }else{
                        echo "Aucun abonnement";
                    }
                ?>
            </div>
        </div>
        <hr>
        <!-- END SIDEBAR USER TITLE -->
        <!-- SIDEBAR MENU -->
        <div class="profile-usermenu">
            <ul class="nav">
                <li>
                    <a href="vueGeneralCompte.php">
                        <i class="glyphicon glyphicon-user"></i>
                        Vue General </a>
                </li>
                <li>
                    <a href="parametreRelanceCompte.php">
                        <i class="glyphicon glyphicon-time"></i>
                        Paramètre Relance </a>
                </li>
                <li>
                    <a href="gestionAbonnementCompte.php">
                        <i class="glyphicon glyphicon-credit-card"></i>
                        Gestion abonnement </a>
                </li>
            </ul>
        </div>
        <!-- END MENU -->
    </div>
</div>