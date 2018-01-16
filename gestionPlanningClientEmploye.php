<?php include 'header.php'; ?>
<div class="container n center">
    <div class="row profile">
        <?php include 'menuEmploye.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if (isset($_POST['supprimerSeanceClient'])) {
                            $db->deleteSeanceClient($_POST['idClient'], $_POST['idSeance']);
                        }

                        if (isset($_POST['assignationSeanceUtilisateur'])) {
                            if (strcmp($_POST['seance'],"-") != 0) {
                                $tab = explode(":", $_POST['seance']);
                                $util = $db->getSeance(trim($tab[0]));
                                $idSeance = $util->id;

                                $idClient = $_POST['idClient'];

                                $typepaiements = $_POST['typePaiement'];
                                $typepaiement = "prix";
                                if (strcmp($typepaiements, "Place restante") == 0) {
                                    $typepaiement = "unite";
                                } else if (strcmp($typepaiements, "Abonnement") == 0) {
                                    $typepaiement = "abonnement";
                                }

                                $db->ajoutClientToSeance($idClient, $idSeance, $typepaiement);
                            }
                        }

                        ?>
                    </div>
                    <div class="col-md-8">
                        <form action="gestionPlanningClientEmploye.php" method="post">
                            <div class="form-group">
                                <label for="client" class="control-label"><h3>Liste Clients</h3></label>
                                <select class="form-control" name="client" id="client"
                                        onchange="this.form.submit();">
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
                        </form>
                    </div>
                    <div class="col-md-4">
                        <button id="ajoutSeanceClient" name="ajoutSeanceClient" class="btn btn-block btn-success"
                                type="button">
                            Ajouter Séance au Client
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

                                echo "<form action='gestionPlanningClientEmploye.php' method='POST'>";
                                echo "<input type='hidden' id='idClient' name='idClient' value='" . $idClient . "'/>";
                                echo "<input type='hidden' id='idSeance' name='idSeance' value='" . $seance->id . "'/>";
                                echo "<tr>";
                                echo "<td>" . $nomActivite . "</td>";
                                echo "<td>" . $nomCoach . "</td>";
                                echo "<td>" . $seance->datedebut . "</td>";
                                echo "<td>" . $seance->datefin . "</td>";
                                echo "<td>" . $seance->nbplace . "</td>";
                                echo "<td>" . $db->getPlaceRestantForSeance($seance->id) . "</td>";
                                echo "<td>" . $seance->prix . "</td>";
                                echo "<td><input type='submit' class='btn btn-default btn-block' name='supprimerSeanceClient' value='Supprimer'></td>";
                                echo "</tr>";
                                echo "</form>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal fade" id="modalAjoutSeanceClient" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Ajouter une séance</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <form action="gestionPlanningClientEmploye.php" method="post">
                                <div class="modal-body">
                                    <div class="row">
                                        <?php echo "<input type='hidden' name='idClient' value='" . $idClient . "'>" ?>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="seance" class="control-label">Activité</label>
                                                <select class="form-control" name="seance" id="seance">
                                                    <option>-</option>
                                                    <?php
                                                    foreach ($db->getAllSeanceNonCommenceeDisponibleClient($idClient) as $a) {
                                                        $activite = $db->getActiviteWithId($a->idactivite);
                                                        echo "<option>" . $a->id . " : " . $activite->libelle . " de " . $a->datedebut . " à " . $a->datefin . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="typePaiement" class="control-label">Type paiement</label>
                                                <select class="form-control" name="typePaiement" id="typePaiement">
                                                    <?php
                                                    echo "<option>Prix unité</option>";
                                                    $utilisateur = $db->getUtilisateur($idClient);
                                                    if ($utilisateur->nombreseancedisponible > 0) {
                                                        echo "<option>Place restante</option>";
                                                    }
                                                    if (!is_null($db->getAbonnementEnCours($idClient))) {
                                                        echo "<option>Abonnement</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" id="assignationSeanceUtilisateur"
                                           name="assignationSeanceUtilisateur" class="btn btn-block btn-success">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
