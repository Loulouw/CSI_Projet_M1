<div class="col-md-3">
    <div class="profile-sidebar">
        <!-- END SIDEBAR USER TITLE -->
        <!-- SIDEBAR MENU -->
        <div class="profile-usermenu" style="margin-top: -15px;">
            <ul class="nav">
                <li>
                    <a href="gestionPlanningClient.php">
                        <i class="glyphicon glyphicon-th-list"></i>
                        Planning</a>
                </li>
                <li>
                    <a href="gestionReservationClient.php">
                        <i class="glyphicon glyphicon-time"></i>
                        Réserver une séance</a>
                </li>
                <li>
                    <a href="gestionInvitationClient.php">
                        <i class="glyphicon glyphicon-user"></i>
                        Invitation (<?php echo count($db->getAllInvitation($_SESSION['utilisateur']->id)) ?>)</a>
                </li>
                <li>
                    <a href="gestionHitstoriqueClient.php">
                        <i class="glyphicon glyphicon-folder-close"></i>
                        Historique</a>
                </li>
            </ul>
        </div>
        <!-- END MENU -->
    </div>
</div>