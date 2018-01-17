<?php include 'header.php'; ?>
<div class="container n center">
    <div class="row profile">
        <?php include 'menuCoach.php'; ?>
        <div class="col-md-9">
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Planning de <?php echo $_SESSION['utilisateur']->nom . " " . $_SESSION['utilisateur']->prenom; ?></h3>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <th>Activité</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Nb places</th>
                                <th>Nb places restantes</th>
                                <th>Prix</th>
                                <th>Participants</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($db->getAllSeanceCoachNonPassee($_SESSION['utilisateur']->id) as $s) {
                                $activite = $db->getActiviteWithId($s->idactivite);
                                $nomActivite = $activite->libelle;
                                echo "<tr>";
                                echo "<td>" . $nomActivite . "</td>";
                                echo "<td>" . $s->datedebut . "</td>";
                                echo "<td>" . $s->datefin . "</td>";
                                echo "<td>" . $s->nbplace . "</td>";
                                echo "<td>" . $db->getPlaceRestantForSeance($s->id) . "</td>";
                                echo "<td>" . $s->prix . "</td>";
                                echo "<td><input type='button' value='Voir' class='btn btn-default btn-block' onclick='document.location.href=\"gestionPlanningVueClientCoach.php?idSeance=" . $s->id . "\";'></td>";
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
