<?php include 'header.php'; ?>
<div class="container n center">
    <div class="row profile">
        <?php include 'menuCoach.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">
                <?php
                if (isset($_GET['idSeance'])) {
                    $seance = $db->getSeance($_GET['idSeance']);
                    if (!$seance) {
                        header('Location: index.php');
                    }else{
                        $activite = $db->getActiviteWithId($seance->idactivite);
                    }
                } else {
                    header('Location: index.php');
                }
                ?>

                <div class="row">
                    <div class="col-md-12">
                        <h3>Activité : <?php echo $activite->libelle; ?></h3>
                    </div>
                    <div class="col-md-12">
                        <?php echo "<h4>Du " . $seance->datedebut . " au " . $seance->datefin . "</h4>"?>
                    </div>
                    <div class="col-md-12">
                        <br/>
                        <h3>Liste des participants :</h3>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Mail</th>
                                <th>Téléphone</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach ($db->getUtilisateurForSeance($seance->id) as $u){
                                    echo "<tr>";
                                    echo "<td>" . $u->nom . "</td>";
                                    echo "<td>" . $u->prenom . "</td>";
                                    echo "<td>" . $u->mail . "</td>";
                                    echo "<td>" . $u->telephone . "</td>";
                                    echo "</tr>";
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
