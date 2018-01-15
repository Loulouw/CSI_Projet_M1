<?php include 'header.php'; ?>
<div class="container n center">
    <div class="row profile">
        <?php include 'menuEmploye.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <form action="gestionPlanningCoachEmploye.php" method="post">
                            <div class="row">
                                <div class="form-group">
                                    <label for="coach" class="control-label"><h3>Liste Coachs</h3></label>
                                    <select class="form-control" name="coach" id="coach" onchange="this.form.submit();">
                                        <?php
                                        $first = true;
                                        $idCoach = 0;
                                        if (isset($_POST['coach'])) {
                                            $tab = explode(":", $_POST['coach']);
                                            $util = $db->getUtilisateurByMail(trim($tab[1]));
                                            $idCoach = $util->id;
                                        }
                                        foreach ($db->getCoachs() as $c) {
                                            if ($idCoach == $c->id || ($first && $idCoach == 0)) {
                                                $first = false;
                                                $idCoach = $c->id;
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
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $listeSeances = $db->getAllSeanceNonCommenceeCoach($idCoach);
                            foreach ($listeSeances as $s) {
                                $nomCoach = "-";
                                $coach = $db->getUtilisateur($idCoach);
                                if ($coach) {
                                    $nomCoach = $coach->nom;
                                }

                                $activite = $db->getActiviteWithId($s->idactivite);
                                $nomActivite = $activite->libelle;

                                echo "<tr>";
                                echo "<td>" . $nomActivite . "</td>";
                                echo "<td>" . $nomCoach . "</td>";
                                echo "<td>" . $s->datedebut . "</td>";
                                echo "<td>" . $s->datefin . "</td>";
                                echo "<td>" . $s->nbplace . "</td>";
                                echo "<td>" . $db->getPlaceRestantForSeance($s->id) . "</td>";
                                echo "<td>" . $s->prix . "</td>";
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
