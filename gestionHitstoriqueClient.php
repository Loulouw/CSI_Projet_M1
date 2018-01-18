<?php include 'header.php'; ?>
<div class="container n center">
    <div class="row profile">
        <?php include 'MenuClient.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Historique</h3>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <th>Activité</th>
                                <th>Coach</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($db->getSeancePasseeClient($_SESSION['utilisateur']->id) as $s) {
                                $activite = $db->getActiviteWithId($s->idactivite);
                                $nomActivite = $activite->libelle;
                                $nomCoach = "-";
                                $coach = $db->getUtilisateur($s->idutilisateurcoach);
                                if($coach){
                                    $nomCoach = $coach->nom;
                                }
                                echo "<tr>";
                                echo "<td>" . $nomActivite . "</td>";
                                echo "<td>" . $nomCoach . "</td>";
                                echo "<td>" . $s->datedebut . "</td>";
                                echo "<td>" . $s->datefin . "</td>";
                                echo "</tr>";
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