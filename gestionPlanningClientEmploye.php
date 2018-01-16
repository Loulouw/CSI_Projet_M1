<?php include 'header.php'; ?>
<div class="container n center">
    <div class="row profile">
        <?php include 'menuEmploye.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <form action="gestionPlanningClientEmploye.php" method="post">
                            <div class="row">
                                <div class="form-group">
                                    <label for="client" class="control-label"><h3>Liste Clients</h3></label>
                                    <select class="form-control" name="client" id="client" onchange="this.form.submit();">
                                        <?php
                                        $first = true;
                                        $idClient = 0;
                                        if (isset($_POST['client'])) {
                                            $tab = explode(":", $_POST['client']);
                                            $util = $db->getUtilisateurByMail(trim($tab[1]));
                                            $idClient = $util->id;
                                        }
                                        foreach ($db->getClients() as $c) {
                                            if ($idClient == $c->id || ($first && $idClient == 0)) {
                                                $first = false;
                                                $idClient = $c->id;
                                                echo "<option selected='selected'>";
                                            } else {
                                                echo "<option>";
                                            }
                                            echo $c->nom . " " . $c->prenom . " : " . $c->mail . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <th>Activité</th>
                                <th>Coach</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Nb places</th>
                                <th>Nb places restantes</th>
                                <th>Prix</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $listeSeances = $db->getAllSeanceNonCommenceeClient($idClient);
                            foreach ($listeSeances as $s) {

                                $seance = $db->getSeance($s->idseance);

                                $nomCoach = "-";
                                $coach = $db->getUtilisateur($seance->idutilisateurcoach);
                                if ($coach) {
                                    $nomCoach = $coach->nom;
                                }

                                $activite = $db->getActiviteWithId($seance->idactivite);
                                $nomActivite = $activite->libelle;


                                echo "<tr>";
                                echo "<td>" . $nomActivite . "</td>";
                                echo "<td>" . $nomCoach . "</td>";
                                echo "<td>" . $seance->datedebut . "</td>";
                                echo "<td>" . $seance->datefin . "</td>";
                                echo "<td>" . $seance->nbplace . "</td>";
                                echo "<td>" . $db->getPlaceRestantForSeance($seance->id) . "</td>";
                                echo "<td>" . $seance->prix . "</td>";
                                echo "<td></td>";
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
