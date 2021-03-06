<?php include 'header.php'; ?>

    <div id="headerwrap">
        <div class="container">
            <div class="row centered">
                <div class="col-lg-8 col-lg-offset-2">
                    <h1>Prenez votre <b>Santé</b> en <b>Main</b></h1>
                    <h2>Adhérez à notre salle de sport</h2>
                </div>
            </div><!-- row -->
        </div><!-- container -->
    </div><!-- headerwrap -->

    <div class="w container">
        <div class="row centered">
            <br><br>
            <div class="col-lg-4">
                <i class="fa fa-heart"></i>
                <h4>Activités</h4>
                <p>Grâce à notre multitude d'activités, vous trouverez votre bonheur dans notre salle de sport</p>
            </div><!-- col-lg-4 -->

            <div class="col-lg-4">
                <i class="fa fa-laptop"></i>
                <h4>Connectée</h4>
                <p>Notre salle de sport est connectée, réservez vos séances, adhérez à un abonnement pour économiser du
                    temps et de l'argent et tout ça rapidement grâce à notre site web</p>
            </div><!-- col-lg-4 -->

            <div class="col-lg-4">
                <i class="fa fa-trophy"></i>
                <h4>Gagner</h4>
                <p>Nos activités sont encadrées par des coachs qui vous permettront d'atteindre vos objectifs et de
                    réaliser vos rêves</p>
            </div><!-- col-lg-4 -->

            <div class="col-md-12">
                <hr>
                <h3>Liste des activités disponibles</h3><br><br>
                <?php
                echo "<div class='row'>";
                foreach ($db->getActiviteActive() as $a) {
                    echo "<div class='col-md-3 text-center'>";
                    echo "<h4>" . $a->libelle . "</h4><br>";
                    echo "</div>";
                }
                echo "</div>";
                ?>
            </div>
        </div><!-- row -->
        <br>
        <br>
    </div><!-- container -->
<?php include 'footer.php'; ?>