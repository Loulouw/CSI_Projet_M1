<?php include 'header.php'; ?>
<div class="container n center">
    <div class="row profile">
        <?php include 'menuEmploye.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">

                <div class="modal fade" id="modalModifCoach" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <form id="modifCoachForm" method="post">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">Modifier Coach</h3>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <input type="hidden" id="idCoachModal" name="idSeance" value="">
                                    <div class="form-group">
                                        <label for="coach" class="control-label">Coach</label>
                                        <select class="form-control" name="coach" id="coach">
                                            <option>-</option>
                                            <?php
                                            foreach ($db->getCoachs() as $c) {
                                                echo "<option>" . $c->nom . " " . $c->prenom . " : " . $c->mail . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button id="buttonModifCoach" type="submit"
                                            name="modifCoachModal" class="btn btn-success btn-block">Modifier Coach
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <?php
                            if(isset($_POST['modifCoachModal'])){
                                $idCoach = null;
                                if (strcmp($_POST['coach'], "-") != 0) {
                                    $tab = explode(":", $_POST['coach']);
                                    $util = $db->getUtilisateurByMail(trim($tab[1]));
                                    $idCoach = $util->id;
                                }

                                $idSeance = $_POST['idSeance'];
                                $message = $db->updateCoachOnSeance($idSeance,$idCoach);
                                if (strcmp($message, "") != 0) {
                                    echo "<div class=\"alert alert-danger\">" . $message . "</div>";
                                }
                            }
                        ?>
                    </div>
                    <div class="col-md-12">
                        <h3>Liste des séances à venir</h3>
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
                            $listeSeances = $db->getAllSeanceNonCommencee();
                            foreach ($listeSeances as $s) {
                                $nomCoach = "-";
                                $coach = $db->getUtilisateur($s->idutilisateurcoach);
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
                                echo "<td><input type='button' class='btn btn-default btn-block' onclick='showModalCoach(" . $s->id . ")' id='modifCoach' name='modifCoach' value='Modifier Coach'></td>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    </divrow>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
