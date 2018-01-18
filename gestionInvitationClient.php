<?php include 'header.php'; ?>
<div class="container n center">
    <div class="row profile">
        <?php include 'MenuClient.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if (isset($_POST['accepterInvitation'])) {
                            $idInvit = $_POST['idSeanceInvitation'];
                            $invitation = $db->getInvitation($idInvit);

                            $typepaiements = $_POST['typePaiement'];
                            $typepaiement = "prix";
                            if (strcmp($typepaiements, "Place restante") == 0) {
                                $typepaiement = "unite";
                            } else if (strcmp($typepaiements, "Abonnement") == 0) {
                                $typepaiement = "abonnement";
                            }

                            $db->accepterInvitation($idInvit,$typepaiement);

                            if (strcmp(trim($_POST['invitation']), "") != 0) {
                                $message = $db->envoyerUneInvitation($_SESSION['utilisateur']->id, $_POST['invitation'], $invitation->idseance);
                                if (strcmp($message, "Invité(e) introuvable") == 0) {
                                    echo "<div class='alert alert-danger'>" . $message . "</div>";
                                } else {
                                    echo "<div class='alert alert-success'>" . $message . "</div>";
                                }
                            }

                        }

                        if (isset($_POST['refuserInvitation'])) {
                            $idInvit = $_POST['idInvitation'];
                            $db->refuserInvitation($idInvit);
                        }
                        ?>
                    </div>
                    <div class="col-md-12">
                        <h3>Liste des invitations</h3>
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
                            $listeInvitation = $db->getAllInvitation($_SESSION['utilisateur']->id);
                            foreach ($listeInvitation as $i) {
                                $seance = $db->getSeance($i->idseance);
                                $activite = $db->getActiviteWithId($seance->idactivite);
                                $coach = $db->getUtilisateur($seance->idutilisateurcoach);

                                $nomActivite = $activite->libelle;

                                $nomCoach = "-";
                                if ($coach != false) {
                                    $nomCoach = $coach->nom;
                                }

                                echo "<form method='post' action='gestionInvitationClient.php'>";
                                echo "<tr>";
                                echo "<input type='hidden' name='idInvitation' id='idInvitation' value='" . $i->id . "'>";
                                echo "<td>" . $nomActivite . "</td>";
                                echo "<td>" . $nomCoach . "</td>";
                                echo "<td>" . $seance->datedebut . "</td>";
                                echo "<td>" . $seance->datefin . "</td>";
                                echo "<td>" . $seance->nbplace . "</td>";
                                echo "<td>" . $db->getPlaceRestantForSeance($seance->id) . "</td>";
                                echo "<td>" . $seance->prix . "</td>";
                                echo "<td><input type='button'onclick='showModalInvitation(". $i->id .")' class='btn btn-default btn-block' id='accepterInvitation' value='Accepter'><input type='submit' class='btn btn-default btn-block' id='refuserInvitation' name='refuserInvitation' value='Refuser'></td>";
                                echo "</tr>";
                                echo "</form>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal fade" id="modalInvitation" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Accepter invitation</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <form action="gestionInvitationClient.php" method="post">
                                <div class="modal-body">
                                    <div class="row">
                                        <input id="idSeanceInvitation" type='hidden' name='idSeanceInvitation' value=''>
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
                                    <input type="submit" id="accepterInvitation"
                                           name="accepterInvitation"  class="btn btn-block btn-success">
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
