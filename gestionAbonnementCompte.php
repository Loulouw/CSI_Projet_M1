<?php include 'header.php'; ?>
    <div class="container n center">
        <div class="row profile">
            <?php
            if (isset($_POST['abo10jfb'])) {
                $db->ajout10Seance($_SESSION['utilisateur']->id);
                $_SESSION['utilisateur']->nombreseancedisponible = $_SESSION['utilisateur']->nombreseancedisponible + 10;
            }

            if (isset($_POST['abo1mfb'])) {
                $db->updateAbonnement($_SESSION['utilisateur']->id, 1);
            }

            if (isset($_POST['abo1afb'])) {
                $db->updateAbonnement($_SESSION['utilisateur']->id, 2);
            }
            ?>

            <?php include 'menuMonCompte.php'; ?>

            <div class="col-md-9">
                <div class="profile-content">
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                            if (isset($_POST['abo10jfb']) || isset($_POST['abo1mfb']) || isset($_POST['abo1afb'])) {
                                echo "<div class=\"alert alert-success\">Votre compte a bien été mis à jour.</div>";
                            }
                            ?>
                        </div>
                        <div class="col-md-12" style="margin-bottom: 20px;">
                            <?php
                            $abonnement = $db->getAbonnementEnCours($_SESSION['utilisateur']->id);
                            if ($abonnement != null) {
                                $type = $db->getTypeAbonnement($abonnement);
                                echo "<p>Vous avez un abonnement de type : <strong>" . $type->libelle . "</strong></p>";
                                echo "<p>Il vous reste <strong> " . $db->getJoursRestantAbonnement($_SESSION['utilisateur']->id) . "</strong></p>";
                            } else {
                                echo "Vous n'avez actuellement <strong> aucun abonnement</strong>";
                            }
                            ?>
                        </div>
                        <div class="col-md-4 portfolio-item">
                            <div class="card h-100">
                                <img class="card-img-top imgabo" src="images/abo10.png" alt="">
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <a href="#">Carte 10 séances</a>
                                    </h4>
                                    <p class="card-text">Avec la carte 10 séances, profitez de votre salle de sport avec
                                        vos amis pour seulement 14€.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 portfolio-item">
                            <div class="card h-100">
                                <a href="#"><img class="card-img-top imgabo" src="images/abo1m.png" alt=""></a>
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <a href="#">Abonnement mensuel</a>
                                    </h4>
                                    <p class="card-text">L'abonnement mensuel à 22,5€ par mois vous permet de profiter
                                        de votre salle de sport quand vous le voulez !</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 portfolio-item">
                            <div class="card h-100">
                                <a href="#"><img class="card-img-top imgabo" src="images/abo1a.png" alt=""></a>
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <a href="#">Abonnement annuel</a>
                                    </h4>
                                    <p class="card-text text-justify">C'est l'abonnement ultime, il vous permettra de
                                        profiter de votre salle de sport toute l'année et pour seulement 230€ soit 19€
                                        par mois !</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button id="modal10jb" class="btn btn-success btn-block" type="button">
                                Souscrire
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-success btn-block" data-toggle="modal" data-target="#modal1m">
                                Souscrire
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-success btn-block" data-toggle="modal" data-target="#modal1a">
                                Souscrire
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'modalAbonnement.php'; ?>
        </div>
    </div>
<?php include 'footer.php'; ?>