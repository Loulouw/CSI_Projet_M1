<?php include 'header.php'; ?>
<div class="contrainer n center">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <div class="well">
                <?php
                if(isset($_POST['connexionsend'])){
                    $utilisateur = $db->connexion($_POST['username'],$_POST['password']);
                    if($utilisateur != null){
                        $_SESSION['utilisateur'] = $utilisateur;
                        header('Location: index.php');
                    }else{
                        echo "<div class=\"alert alert-danger\">L'identifiant n'existe pas ou le mote de passe est incorrect.</div>";
                    }
                }
                ?>
                <form id="loginForm" method="POST" action="">
                    <div class="form-group">
                        <label for="username" class="control-label">Identifiant</label>
                        <input type="email" class="form-control" id="username" name="username" value="" required=""
                               title="Entrez votre identifiants" placeholder="example@gmail.com">
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label">Mot de Passe</label>
                        <input type="password" class="form-control" id="password" name="password" value="" required=""
                               title="Entrez votre mot de passe">
                        <span class="help-block"></span>
                    </div>
                    <button id="buttonConnexion" type="submit" name="connexionsend" class="btn btn-success btn-block">Connexion</button>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="well">
                <?php
                if (isset($_POST['inscriptionsend'])) {
                    if (!$db->enregistrerUtilisateur($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['telephone'])) {
                        echo "<div class=\"alert alert-danger\">Une erreur est survenue durant votre inscription.</div>";
                    } else {
                        echo "<div class=\"alert alert-success\">Vous êtes inscrit, un mail va vous être envoyé avec vos identifiants de connexion.</div>";
                    }
                }
                ?>
                <form id="inscriptionForm" method="post" action="connexion.php">
                    <p class="lead">Inscrivez vous maintenant pour seulement <span class="text-success">20 €</span></p>
                    <ul class="list-unstyled" style="line-height: 2">
                        <li><span class="fa fa-check text-success"></span> Réservez en ligne</li>
                        <li><span class="fa fa-check text-success"></span> Accéder à nos activitées</li>
                        <li><span class="fa fa-check text-success"></span> Faites vous coacher</li>
                        <li><span class="fa fa-check text-success"></span> 1 séance gratuite
                            <small>(Seulement pour les nouveaux membres)</small>
                        </li>
                    </ul>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Nom">Nom</label>
                                <div class="input-group">
                                    <span class="input-group-addon glyphicon glyphicon-user"></span>
                                    <input type="text" class="form-control" name="nom" id="nom" placeholder="Nom" required="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Prenom">Prénom</label>
                                <div class="input-group">
                                    <span class="input-group-addon glyphicon glyphicon-user"></span>
                                    <input type="text" class="form-control" name="prenom" id="prenom" placeholder="Prénom"
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
                                    <input type="email" class="form-control" name="email" id="email" placeholder="example@gmail.com"
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
                                    <input id="telephone" type="text" class="form-control" name="telephone" placeholder="Téléphone"
                                           aria-describedby="basic-addon1" required="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <hr/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="numerocarte">Numéro carte</label>
                                <input type="text" class="form-control" id="numerocarte"
                                       placeholder="XXXX-XXXX-XXXX-XXXX" required="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="codesecurite">Code sécurité</label>
                                <input type="text" class="form-control" id="codesecurite"
                                       placeholder="XXX" required=""/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="validité">Date expiration</label>
                                <input type="text" class="form-control" id="validité"
                                       placeholder="MM/AA" required=""/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button id="buttonInscription" type="submit" name="inscriptionsend" class="btn btn-success">Je m'inscris !</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>


<?php include 'footer.php'; ?>
