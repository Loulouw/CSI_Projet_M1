<?php include 'header.php'; ?>
<?php


?>

<div class="container n center">
    <div class="row profile">
        <?php include 'menuAdministrateur.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">

                <div class="modal fade" id="modalAjoutSeance" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Ajouter une Séance</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="seanceForm" method="post">
                                <div class="modal-body">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="activite" class="control-label">Activité</label>
                                                <select class="form-control" name="activite" id="activite">
                                                    <?php
                                                    foreach ($db->getActivites() as $a) {
                                                        echo "<option>" . $a->libelle . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="dateDebut" class="control-label">Date début</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control" id="dateDebut"
                                                               name="dateDebut"/>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" id="heureDebut"
                                                               name="heureDebut"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="dateFin" class="control-label">Date fin</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control" id="dateFin"
                                                               name="dateFin"/>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" id="heureFin"
                                                               name="heureFin"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nbPlace" class="control-label">Nombre de places</label>
                                                <input type="number" class="form-control" id="nbPlace" name="nbPlace">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="prix" class="control-label">Prix</label>
                                                <input type="number" class="form-control" id="prix" name="prix">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="coach" class="control-label">Coach (Facultatif)</label>
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
                                    </div>


                                </div>
                                <div class="modal-footer">
                                    <button id="buttonAjoutSeanceb" type="submit"
                                            name="ajoutSeanceSend" class="btn btn-success btn-block">Ajouter Séance
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <row>
                    <div class="col-md-12">
                        <?php
                        if (isset($_POST["deleteSeance"])) {
                            $db->supprimerSeance($_POST["idSeance"]);
                        }

                        if (isset($_POST['ajoutSeanceSend'])) {
                            $dateDebut = $_POST['dateDebut'] . " " . $_POST['heureDebut'];
                            $dateFin = $_POST['dateFin'] . " " . $_POST['heureFin'];
                            $activite = $db->getActivite($_POST['activite']);
                            $idActivite = $activite->id;

                            $idCoach = null;
                            if (strcmp($_POST['coach'], "-") != 0) {
                                $tab = explode(":", $_POST['coach']);
                                $util = $db->getUtilisateurByMail(trim($tab[1]));
                                $idCoach = $util->id;
                            }

                            $message = $db->saveSeance($idActivite, $idCoach, $dateDebut, $dateFin, $_POST['nbPlace'], $_POST['prix']);
                            if (strcmp($message, "") != 0) {
                                echo "<div class=\"alert alert-danger\">" . $message . "</div>";
                            }

                        }
                        ?>
                    </div>
                    <div class="col-md-8">
                        <h3>Liste des séances à venir</h3>
                    </div>
                    <div class="col-md-4">
                        <button id="modalAjoutSeanceb" class="btn btn-block btn-success"
                                type="button">
                            Ajouter séance
                        </button>
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

                                echo "<form action='gestionSeanceAdministrateur.php' method='POST'>";
                                echo "<input type='hidden' id='idSeance' name='idSeance' value='" . $s->id . "'/>";

                                echo "<tr>";
                                echo "<td>" . $nomActivite . "</td>";
                                echo "<td>" . $nomCoach . "</td>";
                                echo "<td>" . $s->datedebut . "</td>";
                                echo "<td>" . $s->datefin . "</td>";
                                echo "<td>" . $s->nbplace . "</td>";
                                echo "<td>" . $db->getPlaceRestantForSeance($s->id) . "</td>";
                                echo "<td>" . $s->prix . "</td>";
                                echo "<td><input type='submit' class='btn btn-default btn-block' name='deleteSeance' value='Supprimer'></td>";
                                echo "</tr>";

                                echo "</form>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </row>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
