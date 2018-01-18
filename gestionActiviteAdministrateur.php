<?php include 'header.php'; ?>
<?php
if (isset($_POST['updateEtatActivite'])) {
    $db->updateActiviteArchive($_POST['idActivite']);
}

$messageActivite = "";

if (isset($_POST['nouvelleActiviteeSend'])) {
    if (trim($_POST['nomActivite']) === "") {
        $messageActivite = "Le nom ne peut pas être vide";
    } else if ($db->getActivite($_POST['nomActivite']) == false) {
        $db->ajoutActivite($_POST['nomActivite']);
        $messageActivite = "OK";
    } else {
        $messageActivite = "Une activité portant le même nom existe déjà";
    }
}
?>


<div class="container n center">
    <div class="row profile">
        <?php include 'menuAdministrateur.php'; ?>

        <div class="modal fade" id="modalAjoutActivite" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Ajouter une activité</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="activiteForm" method="post">
                        <div class="modal-body">
                            <div class="form-group" style="display:inline">
                                <label for="nomActivite" class="control-label">Nom de l'activité</label>
                                <input type="text" class="form-control" id="nomActivite" name="nomActivite" value=""
                                       required=""
                                       title="Nom de l'activité">
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button style="display:inline" id="buttonAjoutActivite" type="submit"
                                    name="nouvelleActiviteeSend" class="btn btn-success btn-block">Ajouter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if (strcmp($messageActivite, "OK") == 0) {
                            echo "<div class=\"alert alert-success\">L'activité " . $_POST['nomActivite'] . " a été ajoutée</div>";
                        } else if (strcmp($messageActivite, "") != 0) {
                            echo "<div class=\"alert alert-danger\">" . $messageActivite . "</div>";
                        }
                        ?>
                    </div>
                    <div class="col-md-8">
                        <h3>Liste des activités</h3>
                    </div>
                    <div class="col-md-4">
                        <button id="modalActiviteb" class="btn btn-success btn-block" type="button">
                            Ajouter une activité
                        </button>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <th>Activité</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $activites = $db->getActivites();
                            foreach ($activites as $a) {
                                echo "<form action='gestionActiviteAdministrateur.php' method='POST'>";
                                echo "<input type='hidden' name='idActivite' value='" . $a->id . "'/>";
                                $actif = $a->archive ? "Archivé" : "Actif";
                                echo "<tr>";
                                echo "<td>" . $a->libelle . "</td>";
                                echo "<td>" . $actif . "</td>";
                                echo "<td><input type='submit' class='btn btn-default btn-block' name='updateEtatActivite' value='Changer Status'></td>";
                                echo "</tr>";
                                echo "</form>";
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
