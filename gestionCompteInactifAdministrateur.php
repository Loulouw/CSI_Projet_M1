<?php include 'header.php'; ?>
<div class="container n center">
    <div class="row profile">
        <?php include 'menuAdministrateur.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if (isset($_POST['updateEtatCompte'])) {
                            $db->updateUtilisateurArchive($_POST['idEmploye']);
                        }

                        if(isset($_POST['changementStatus'])){
                            $db->updateAllUtilisateurToNonActif();
                        }
                        ?>
                    </div>
                    <div class="col-md-8">
                        <h3>Liste des comptes inactifs</h3>
                    </div>
                    <div class="col-md-4">
                        <form action='gestionCompteInactifAdministrateur.php' method='POST'>
                            <button id="modalAjoutEmployeb" name="changementStatus" class="btn btn-block btn-success" type="submit">
                                Mettre à jour les status
                            </button>
                        </form>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <th>Type</th>
                                <th>Nom</th>
                                <th>Mail</th>
                                <th>Téléphone</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $comptesInactifs = $db->getCompteActifNeedToBeNonActif();
                            foreach ($comptesInactifs as $e) {
                                echo "<form action='gestionCompteInactifAdministrateur.php' method='POST'>";
                                echo "<input type='hidden' id='idEmploye' name='idEmploye' value='" . $e->id . "'/>";
                                $actif = $e->compteactif ? "Actif" : "Archivé";
                                $status = $db->getStatusUtilisateur($e->id);

                                echo "<tr>";
                                echo "<td>" . $status->libelle . "</td>";
                                echo "<td>" . strtoupper($e->nom) . " " . $e->prenom . "</td>";
                                echo "<td>" . $e->mail . "</td>";
                                echo "<td>" . $e->telephone . "</td>";
                                echo "<td>" . $actif . "</td>";
                                echo "<td><input type='submit' class='btn btn-default btn-block' name='updateEtatCompte' value='Changer Status'></td>";
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
