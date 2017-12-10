<?php include 'header.php'; ?>
    <div class="container n center">
        <div class="row profile">
            <?php include 'menuMonCompte.php'; ?>
            <div class="col-md-9">
                <div class="profile-content">
                    <?php
                    $relanceSeance = $db->getRelanceSeance($_SESSION['utilisateur']->id);
                    $relanceAbonnement = $db->getRelanceAbonnement($_SESSION['utilisateur']->id);

                    if (isset($_POST['submitRelanceAbonnement'])) {
                        $nbHeures = $_POST['tempsRelanceAbonnement'];
                        $relanceAbonnement->tempsavantrelance = $nbHeures;
                        $db->updateRelance($relanceAbonnement,$nbHeures);
                        echo "<div class=\"alert alert-success\">Temps de relance pour l'abonnement mis à jour.</div>";
                    }

                    if (isset($_POST['submitRelanceSeance'])) {
                        $nbHeures = $_POST['tempsRelanceSeance'];
                        $relanceSeance->tempsavantrelance = $nbHeures;
                        $db->updateRelance($relanceSeance,$nbHeures);
                        echo "<div class=\"alert alert-success\">Temps de relance pour les séances mis à jour.</div>";
                    }


                    echo "<br><form id='relanceSeanceForm' method='POST' action='parametreRelanceCompte.php'><div class='row'>";
                    echo "<div class='col-md-7'>Temps avant relance pour une séance : <div class='heuresRelance'>";
                    echo "<input type='number' min='0' max='999' class='form-control numberRelance' id='tempsRelanceSeance' name='tempsRelanceSeance' value='".$relanceSeance->tempsavantrelance."' required=''>";
                    echo "</div> heures</div>";
                    echo "<div class='col-md-5'><button type='submit' name='submitRelanceSeance' class='btn btn-info btnmodifrelance'>Modifier</button></div>";
                    echo "</div></form><br>";
                    echo "<hr>";
                    echo "<br><form id='relanceAbonnementForm' method='POST' action='parametreRelanceCompte.php'><div class='row'>";
                    echo "<div class='col-md-7'>Temps avant relance pour une Abonnement : <div class='heuresRelance'>";
                    echo "<input type='number' min='0' max='999' class='form-control numberRelance' id='tempsRelanceAbonnement' name='tempsRelanceAbonnement' value='".$relanceAbonnement->tempsavantrelance."' required=''>";
                    echo "</div> heures</div>";
                    echo "<div class='col-sm-5'><button type='submit' name='submitRelanceAbonnement' class='btn btn-info btnmodifrelance'>Modifier</button></div>";
                    echo "</div></form><br>";
                    ?>

                </div>
            </div>
        </div>

    </div>

<?php include 'footer.php'; ?>