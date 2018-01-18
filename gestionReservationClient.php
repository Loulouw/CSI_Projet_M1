<?php include 'header.php'; ?>
<div class="container n center">
    <div class="row profile">
        <?php include 'MenuClient.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if (isset($_POST['reservationClient'])) {
                            $typepaiements = $_POST['typePaiement'];
                            $typepaiement = "prix";
                            if (strcmp($typepaiements, "Place restante") == 0) {
                                $typepaiement = "unite";
                            } else if (strcmp($typepaiements, "Abonnement") == 0) {
                                $typepaiement = "abonnement";
                            }

                            $idClient = $_SESSION['utilisateur']->id;

                            $idSeance = $_POST['idSeance'];

                            $db->ajoutClientToSeance($idClient, $idSeance, $typepaiement);

                            if (strcmp(trim($_POST['invitation']), "") != 0) {
                                $message = $db->envoyerUneInvitation($_SESSION['utilisateur']->id, $_POST['invitation'], $idSeance);
                                if (strcmp($message, "Invité(e) introuvable") == 0) {
                                    echo "<div class='alert alert-danger'>" . $message . "</div>";
                                } else {
                                    echo "<div class='alert alert-success'>" . $message . "</div>";
                                }
                            }
                        }
                        ?>
                    </div>
                    <div class="col-md-12">
                        <form action="gestionReservationClient.php" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="activite" class="control-label"><h3>Liste Activités</h3></label>
                                        <select class="form-control" name="activite" id="activite"
                                                onchange="this.form.submit();">
                                            <?php
                                            $first = true;
                                            $idActivite = 0;
                                            if (isset($_POST['activite'])) {
                                                $util = $db->getActivite($_POST['activite']);
                                                $idActivite = $util->id;
                                            }
                                            foreach ($db->getActiviteActive() as $a) {
                                                if ($idActivite == $a->id || ($first && $idActivite == 0)) {
                                                    $first = false;
                                                    $idActivite = $a->id;
                                                    echo "<option selected='selected'>";
                                                } else {
                                                    echo "<option>";
                                                }
                                                echo $a->libelle . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-condensed">
                                        <thead>
                                        <tr>
                                            <th>Coach</th>
                                            <th>Date début</th>
                                            <th>Date fin</th>
                                            <th>Nb places</th>
                                            <th>Nb places restantes</th>
                                            <th>Prix</th>
                                            <th>Réserver</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $listeSeances = $db->getAllSeanceNonCommenceeActivite($idActivite);
                                        foreach ($listeSeances as $s) {
                                            $nomCoach = "-";
                                            $coach = $db->getUtilisateur($s->idutilisateurcoach);
                                            if ($coach) {
                                                $nomCoach = $coach->nom;
                                            }

                                            $disabled = "";
                                            if ($db->getPlaceRestantForSeance($s->id) == 0) $disabled = "disabled";
                                            if ($db->utilisateurHaveSeance($_SESSION['utilisateur']->id, $s->id)) $disabled = "disabled";

                                            echo "<tr>";
                                            echo "<td>" . $nomCoach . "</td>";
                                            echo "<td>" . $s->datedebut . "</td>";
                                            echo "<td>" . $s->datefin . "</td>";
                                            echo "<td>" . $s->nbplace . "</td>";
                                            echo "<td>" . $db->getPlaceRestantForSeance($s->id) . "</td>";
                                            echo "<td>" . $s->prix . "</td>";
                                            echo "<td><input value='Réserver' onclick='showModalReserver(" . $s->id . ")' type='button' class='btn btn-block btn-default' " . $disabled . "></td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="modal fade" id="modalReservation" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Réserver une séance</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <form action="gestionReservationClient.php" method="post">
                                <div class="modal-body">
                                    <div class="row">
                                        <input id="hiddenSeance" type='hidden' name='idSeance' value=''>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="typePaiement" class="control-label">Type paiement</label>
                                                <select class="form-control" name="typePaiement" id="typePaiement">
                                                    <?php
                                                    echo "<option>Prix unité</option>";
                                                    $utilisateur = $db->getUtilisateur($_SESSION['utilisateur']->id);
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
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="invitation" class="control-label">Inviter un(e) ami(e)
                                                    ?</label>
                                                <input type="email" class="form-control" id="invitation"
                                                       name="invitation" value=""
                                                       title="Entrez l'adresse mail" placeholder="example@mail.com">
                                                <span class="help-block"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" id="reservationClient"
                                           name="reservationClient" class="btn btn-block btn-success">
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
