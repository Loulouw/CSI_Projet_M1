<?php include 'header.php'; ?>
    <div class="container n center">
        <div class="row profile">
            <?php include 'menuMonCompte.php'; ?>
            <div class="col-md-9">
                <div class="profile-content">
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                            if (isset($_POST['changepasswordsend'])) {
                                $message = $db->changementMotDePasse($_SESSION['utilisateur']->id, $_POST['ancienpass'], $_POST['nouveaupass'], $_POST['confirmpass']);
                                if (strcmp($message, "") === 0) {
                                    echo "<div class=\"alert alert-success\">Votre mot de passe a bien été mis à jour.</div>";
                                } else {
                                    echo "<div class=\"alert alert-danger\">" . $message . "</div>";
                                }
                            }
                            ?>
                        </div>
                        <div class="col-md-6">
                            <h2>Mon compte</h2>
                            <p>
                                Nom : <?php echo $_SESSION['utilisateur']->nom; ?><br><br>
                                Prénom : <?php echo $_SESSION['utilisateur']->prenom; ?><br><br>
                                Mail : <?php echo $_SESSION['utilisateur']->mail; ?><br><br>
                                Téléphone : <?php echo $_SESSION['utilisateur']->telephone ?><br><br>
                                Date inscription : <?php echo $_SESSION['utilisateur']->datepaiementfrais ?><br><br>
                                Statut : <?php $status = $db->getStatusUtilisateur($_SESSION['utilisateur']->id);
                                echo $status->libelle ?><br><br>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h2>Mot de passe</h2>
                            <form id="changePassword" method="POST" action="">
                                <div class="form-group">
                                    <label for="ancienpass" class="control-label">Ancien mot de passe</label>
                                    <input type="password" class="form-control" id="ancienpass" name="ancienpass"
                                           value="" required=""
                                           title="Entrez votre ancien mot de passe">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group">
                                    <label for="nouveaupass" class="control-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="nouveaupass" name="nouveaupass"
                                           value="" required=""
                                           title="Entrez votre nouveau mot de passe">
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group">
                                    <label for="confirmpass" class="control-label">Confirmation mot de passe</label>
                                    <input type="password" class="form-control" id="confirmpass" name="confirmpass"
                                           value="" required=""
                                           title="Confirmer votre nouveau mot de passe">
                                    <span class="help-block"></span>
                                </div>
                                <button id="buttonChangePassword" type="submit" name="changepasswordsend"
                                        class="btn btn-success btn-block">Mettre à jour
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include 'footer.php'; ?>