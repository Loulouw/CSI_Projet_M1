<?php include 'header.php'; ?>

<div class="container n center">
    <div class="row profile">
        <?php include 'menuAdministrateur.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">

                <div class="modal fade" id="modalAjoutEmploye" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Ajouter une activité</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="employeAjoutForm" method="post">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Nom">Nom</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon glyphicon glyphicon-user"></span>
                                                    <input type="text" class="form-control" name="nom" id="nom"
                                                           placeholder="Nom" required="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Prenom">Prénom</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon glyphicon glyphicon-user"></span>
                                                    <input type="text" class="form-control" name="prenom" id="prenom"
                                                           placeholder="Prénom"
                                                           required="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class=" col-md-8">
                                            <div class="form-group">
                                                <label for="Email">Adresse email</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon glyphicon glyphicon-send"></span>
                                                    <input type="email" class="form-control" name="email" id="email"
                                                           placeholder="example@gmail.com"
                                                           required="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="telephone">Téléphone</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon glyphicon glyphicon-earphone"></span>
                                                    <input id="telephone" type="text" class="form-control"
                                                           name="telephone" placeholder="Téléphone"
                                                           aria-describedby="basic-addon1" required="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-offset-1 col-lg-6">
                                            <div class="form-group">
                                                <label for="statusEmploye">Status</label>
                                                <select class="form-control" name="statusEmploye" id="statusEmploye">
                                                    <option>Employé</option>
                                                    <option>Coach</option>
                                                    <option>Administrateur</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button id="buttonAjoutEmploye" type="submit" name="ajoutEmployeSend"
                                            class="btn btn-success">Ajouter l'employé
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php

                        if (isset($_POST['updateEtatEmploye'])) {
                            $db->updateUtilisateurArchive($_POST['idEmploye']);
                        }

                        if (isset($_POST['ajoutEmployeSend'])) {
                            $statusText = $_POST['statusEmploye'];
                            $idStatus = 4;
                            if (strcmp($statusText, "Employé") == 0) {
                                $idStatus = 2;
                            } else if (strcmp($statusText, "Coach") == 0) {
                                $idStatus = 3;
                            } else if (strcmp($statusText, "Administrateur") == 0) {
                                $idStatus = 1;
                            }
                            if (!$db->enregistrerUtilisateur($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['telephone'], $idStatus)) {
                                echo "<div class=\"alert alert-danger\">Une erreur est survenue durant l'ajout de l'employé</div>";
                            } else {
                                echo "<div class=\"alert alert-success\">Vous avez ajouté l'employé, un mail avec son mot de passe lui sera envoyé</div>";
                            }
                        }

                        ?>
                    </div>
                    <div class="col-md-8">
                        <h3>Liste des employés</h3>
                    </div>
                    <div class="col-md-4">
                        <button id="modalAjoutEmployeb" class="btn btn-block btn-success" type="button">Ajouter
                            Employé
                        </button>
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
                            $employes = $db->getEmployes();
                            foreach ($employes as $e) {
                                echo "<form action='gestionEmployeAdministrateur.php' method='POST'>";
                                echo "<input type='hidden' id='idEmploye' name='idEmploye' value='" . $e->id . "'/>";
                                $actif = $e->compteactif ? "Actif" : "Archivé";
                                $status = $db->getStatusUtilisateur($e->id);

                                echo "<tr>";
                                echo "<td>" . $status->libelle . "</td>";
                                echo "<td>" . strtoupper($e->nom) . " " . $e->prenom . "</td>";
                                echo "<td>" . $e->mail . "</td>";
                                echo "<td>" . $e->telephone . "</td>";
                                echo "<td>" . $actif . "</td>";
                                echo "<td><input type='submit' class='btn btn-default btn-block' name='updateEtatEmploye' value='Changer Status'></td>";
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
