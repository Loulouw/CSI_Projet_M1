<?php

use phpmailer\PHPMailer;
use phpmailer\Exception;

require_once 'phpmailer/Exception.php';
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';
require_once 'idiorm-master/idiorm.php';


class db
{


    function __construct()
    {
        //Config PDO
        ORM::configure('pgsql:host=localhost;port=5432;dbname=projetcsi;user=postgres;password=');
        ORM::configure('return_result_sets', true); // returns result sets
        ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

        //override cles primaires
        ORM::configure('id_column_overrides', array(
            'abonnement' => 'id',
            'abonnementtype' => 'id',
            'activite' => 'id',
            'connexion' => 'id',
            'invitation' => 'id',
            'relance' => 'id',
            'relancetype' => 'id',
            'seance' => 'id',
            'statusutilisateur' => 'id',
            'utilisateur' => 'id',
            'utilisateurtoabonnement' => 'id',
            'utilisateurtoseance' => 'id',
        ));
    }

    function sendMail($email, $pwd)
    {
        $mail = new PHPMailer(false);
        try {
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.live.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            //$mail->Username = 'salledesport54@gmail.com';                 // SMTP username
            //$mail->Password = 'louis54000';
            $mail->Username = 'salledesport54@outlook.fr';                 // SMTP username
            $mail->Password = 'louis54000';                           // SMTP password// SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;

            $mail->setFrom('salledesport54@outlook.fr', 'Salle de sport');
            $mail->addAddress($email);

            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Inscription Salle de Sport';
            $mail->Body = "Voici vos identifiants :<br/>identifiant : $email <br/>mot de passe : $pwd";

            $mail->send();
        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }

    }

    function randomPassword()
    {
        $taille = 4;
        $alphabet = "abcdefghijkmnpqrstuwxyz23456789";
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $taille; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    function better_crypt($input, $rounds = 7)
    {
        $salt = "";
        $salt_chars = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
        for ($i = 0; $i < 22; $i++) {
            $salt .= $salt_chars[array_rand($salt_chars)];
        }
        return crypt($input, sprintf('$2a$%02d$', $rounds) . $salt);
    }

    function getLastId($table)
    {
        $id = ORM::for_table($table)->max('id') + 1;
        return $id;
    }

    function enregistrerUtilisateur($nom, $prenom, $mail, $tel, $idStatus = 4)
    {
        $utilisateurDejaPresent = ORM::for_table('utilisateur')->where('mail', $mail)->findArray();
        if (count($utilisateurDejaPresent) == 1) {
            return false;
        }

        $pwd = $this->randomPassword();
        $pwd = "root";
        $hashPwd = $this->better_crypt($pwd);

        $connexion = ORM::for_table('connexion')->create();
        $connexion->id = $this->getLastId('connexion');
        $connexion->identifiantconnexion = $mail;
        $connexion->motdepasse = $hashPwd;
        $connexion->datederniereconnexion = date("Y-m-d H:i:s");
        $connexion->save();

        $utilisateur = ORM::for_table('utilisateur')->create();
        $utilisateur->id = $this->getLastId('utilisateur');
        $utilisateur->nom = $nom;
        $utilisateur->prenom = $prenom;
        $utilisateur->mail = $mail;
        $utilisateur->telephone = $tel;
        $utilisateur->datepaiementfrais = date("Y-m-d");
        $utilisateur->nombreseancedisponible = 1;
        $utilisateur->compteactif = true;
        $utilisateur->datederniereinteraction = date("Y-m-d");
        $utilisateur->idstatusutilisateur = $idStatus;
        $utilisateur->idconnexion = $connexion->id;
        $utilisateur->datevalidationpaiement = date("Y-m-d");
        $utilisateur->save();

        $relanceAbonnement = ORM::for_table('relance')->create();
        $relanceAbonnement->id = $this->getLastId('relance');
        $relanceAbonnement->tempsavantrelance = 24;
        $relanceAbonnement->idrelancetype = 1;
        $relanceAbonnement->idutilisateur = $utilisateur->id;
        $relanceAbonnement->save();

        $relanceSeance = ORM::for_table('relance')->create();
        $relanceSeance->id = $this->getLastId('relance');
        $relanceSeance->tempsavantrelance = 1;
        $relanceSeance->idrelancetype = 2;
        $relanceSeance->idutilisateur = $utilisateur->id;
        $relanceSeance->save();


        $this->sendMail($mail, $pwd);

        return true;
    }

    function connexion($identifiant, $password)
    {
        $res = null;
        $connexion = ORM::for_table('connexion')->where('identifiantconnexion', $identifiant)->findOne();
        if ($connexion != null && password_verify($password, $connexion->motdepasse)) {
            $utilisateur = ORM::for_table('utilisateur')->where('idconnexion', $connexion->id)->findOne();
            if ($utilisateur->compteactif == true) {
                $res = $utilisateur;
            }
        }
        return $res;
    }

    function getAbonnementEnCours($idUtilisateur)
    {
        $abonnements = ORM::for_table('utilisateurtoabonnement')->where('idutilisateur', $idUtilisateur)->findArray();
        foreach ($abonnements as $a) {
            $abonnement = ORM::for_table('abonnement')->where('id', $a['idabonnement'])->findOne();

            if ($abonnement->encours) {
                return $abonnement;
            }
        }
        return null;
    }

    function getTypeAbonnement($abonnement)
    {
        return ORM::for_table('abonnementtype')->where('id', $abonnement->idabonnementtype)->findOne();
    }

    function getJoursRestantAbonnement($idUtilisateur)
    {
        $dureeJour = 0;
        $dateDebut = date("Y-m-d");
        foreach ($this->getAllAbonnement($idUtilisateur) as $a) {
            $dureeJour += $a['duree'];
            if (strtotime($a['datedebut']) < strtotime($dateDebut)) $dateDebut = $a['datedebut'];
        }
        $dateFin = date('Y-m-d', strtotime($dateDebut . ' + ' . $dureeJour . ' days'));
        $df = DateTime::createFromFormat("Y-m-d", $dateFin);
        $db = DateTime::createFromFormat("Y-m-d", date('Y-m-d'));
        $interval = $df->diff($db);
        return $interval->format('%y ans, %m mois et %d jours');
    }

    function getRelanceAbonnement($idUtilisateur)
    {
        return ORM::for_table('relance')->where('idutilisateur', $idUtilisateur)->where('idrelancetype', 1)->findOne();
    }

    function getRelanceSeance($idUtilisateur)
    {
        return ORM::for_table('relance')->where('idutilisateur', $idUtilisateur)->where('idrelancetype', 2)->findOne();
    }

    function updateRelance($prelance, $heures)
    {
        $relance = ORM::for_table('relance')->where('id', $prelance->id)->findOne();
        $relance->tempsavantrelance = $heures;
        $relance->save();
    }

    function ajout10Seance($idUtilisateur)
    {
        $utilisateur = ORM::for_table('utilisateur')->where('id', $idUtilisateur)->findOne();
        $utilisateur->nombreseancedisponible = $utilisateur->nombreseancedisponible + 10;
        $utilisateur->save();
    }

    function updateAbonnement($idUtilisateur, $idAbonnementType)
    {
        $abonnement = ORM::for_table('abonnement')->create();
        $abonnement->id = $this->getLastId('abonnement');
        if ($idAbonnementType == 1) {
            $abonnement->duree = 30;
        } else if ($idAbonnementType == 2) {
            $abonnement->duree = 365;
        }
        $abonnement->idabonnementtype = $idAbonnementType;

        $allAbonnement = $this->getAllAbonnement($idUtilisateur);

        if (sizeof($allAbonnement) <= 0) {
            $abonnement->encours = true;
            $abonnement->datedebut = date("Y-m-d");
        } else {
            $abonnementEnCours = true;
            $derniereDate = date("Y-m-d");
            foreach ($allAbonnement as $a) {
                if ($abonnementEnCours && $a->encours) {
                    $abonnementEnCours = false;
                }
                $dateFin = date('Y-m-d', strtotime($a->datedebut . ' + ' . $a->duree . ' days'));
                if (strtotime($dateFin) > strtotime($derniereDate)) {
                    $derniereDate = $dateFin;
                }
            }

            $abonnement->encours = $abonnementEnCours;
            $abonnement->datedebut = date('Y-m-d', strtotime($derniereDate . ' + ' . 1 . ' days'));
        }

        $abonnement->save();

        $uta = ORM::for_table("utilisateurtoabonnement")->create();
        $uta->id = $this->getLastId("utilisateurtoabonnement");
        $uta->idutilisateur = $idUtilisateur;
        $uta->idabonnement = $abonnement->id;
        $uta->save();
    }

    function getAllAbonnement($idUtilisateur)
    {
        $utilisateurtoabonnement = ORM::for_table("utilisateurtoabonnement")->where('idutilisateur', $idUtilisateur)->findArray();
        $abonnements = array();
        foreach ($utilisateurtoabonnement as $ua) {
            $abo = ORM::for_table("abonnement")->where("id", $ua['idabonnement'])->findOne();
            array_push($abonnements, $abo);
        }
        return $abonnements;
    }

    function getStatusUtilisateur($idUtilisateur)
    {
        $utilisateur = ORM::for_table('utilisateur')->where('id', $idUtilisateur)->findOne();
        return ORM::for_table('statusutilisateur')->where('id', $utilisateur->idstatusutilisateur)->findOne();
    }

    function changementMotDePasse($idUtilisateur, $ancienMdp, $nouveauMdp, $confirmationMdp)
    {
        $utilisateur = ORM::for_table('utilisateur')->where('id', $idUtilisateur)->findOne();
        $connexion = ORM::for_table('connexion')->where('id', $utilisateur->idconnexion)->findOne();
        $message = "";
        if (strlen($nouveauMdp) >= 4 && strcmp($nouveauMdp, $confirmationMdp) === 0) {
            if (password_verify($ancienMdp, $connexion->motdepasse)) {
                $connexion->motdepasse = $this->better_crypt($nouveauMdp);
                $connexion->save();
            } else {
                $message = "L'ancien mot de passe n'est pas le bon";
            }
        } else {
            $message = "La confirmation ne correspond pas au nouveau mot de passe, celui ci doit faire 4 caractère minimum";
        }
        return $message;
    }

    function getActivites()
    {
        return ORM::for_table("activite")->orderByAsc("libelle")->findMany();
    }

    function getActivite($nomActivite)
    {
        return ORM::for_table("activite")->where('libelle', $nomActivite)->findOne();
    }

    function getActiviteWithId($id)
    {
        return ORM::for_table("activite")->where("id", $id)->findOne();
    }

    function updateActiviteArchive($idActivite)
    {
        $activite = ORM::for_table("activite")->where("id", $idActivite)->findOne();
        $activite->archive = !$activite->archive;
        $activite->save();
    }

    function ajoutActivite($nomActivite)
    {
        $activite = ORM::for_table("activite")->create();
        $activite->id = $this->getLastId("activite");
        $activite->libelle = $nomActivite;
        $activite->archive = false;
        $activite->save();
    }

    function getEmployes()
    {
        return ORM::for_table("utilisateur")->where_any_is(array(array('idstatusutilisateur' => 2), array('idstatusutilisateur' => 3)))->orderByAsc("nom")->findMany();
    }

    function updateUtilisateurArchive($idUtilisateur)
    {
        $utilisateur = $this->getUtilisateur($idUtilisateur);
        $utilisateur->compteactif = !$utilisateur->compteactif;
        $utilisateur->save();
    }

    function getCompteActifNeedToBeNonActif()
    {
        $res = array();
        $listeUtilisateurs = ORM::for_table("utilisateur")->orderByDesc("compteactif")->orderByAsc("nom")->findMany();
        $dateMin = date('Y-m-d', strtotime(date("Y-m-d") . ' - 365 days'));
        foreach ($listeUtilisateurs as $u) {
            if (strtotime($u->datederniereinteraction) <= strtotime($dateMin)) {
                array_push($res, $u);
            }
        }
        return $res;
    }

    function updateAllUtilisateurToNonActif()
    {
        $listeUtilisateursActif = ORM::for_table("utilisateur")->where("compteactif", true)->findMany();
        $dateMin = date('Y-m-d', strtotime(date("Y-m-d") . ' - 365 days'));
        foreach ($listeUtilisateursActif as $u) {
            if (strtotime($u->datederniereinteraction) <= strtotime($dateMin)) {
                $u->compteactif = false;
                $u->save();
            }
        }
    }

    function getAllSeanceNonCommencee()
    {
        $listeSeance = ORM::for_table("seance")->whereGt('datedebut', date("Y-m-d h:i"))->findMany();
        return $listeSeance;
    }

    function getAllSeanceNonCommenceeDisponible()
    {
        $res = array();
        $listeSeance = ORM::for_table("seance")->whereGt('datedebut', date("Y-m-d h:i"))->findMany();
        foreach ($listeSeance as $s) {
            if ($this->getPlaceRestantForSeance($s->id) > 0) {
                array_push($res, $s);
            }
        }
        return $res;
    }

    function getAllSeanceNonCommenceeDisponibleClient($idClient)
    {
        $res = array();
        $arrayIds = array();
        $seances = $this->getAllSeanceNonCommenceeClient($idClient);

        foreach ($seances as $s) {
            array_push($arrayIds, $this->getseance($s->idseance)->id);
        }


        foreach ($this->getAllSeanceNonCommenceeDisponible() as $s) {
            if (!in_array($s->id, $arrayIds)) {
                array_push($res, $s);
            }
        }
        return $res;
    }

    function getAllSeanceNonCommenceeCoach($idCoach)
    {
        $listeSeance = ORM::for_table("seance")->where("idutilisateurcoach", $idCoach)->whereGt('datedebut', date("Y-m-d h:i"))->findMany();
        return $listeSeance;
    }

    function getUtilisateur($id)
    {
        return ORM::for_table("utilisateur")->where("id", $id)->find_one();
    }

    function getPlaceRestantForSeance($idSeance)
    {
        $seance = ORM::for_table("seance")->where("id", $idSeance)->findOne();
        $listeParticipant = ORM::for_table("utilisateurtoseance")->where("idseance", $idSeance)->where("participe", true)->findArray();
        return $seance->nbplace - count($listeParticipant);
    }

    function supprimerSeance($idSeance)
    {
        $seance = ORM::for_table("seance")->where("id", $idSeance)->findOne();
        $seance->delete();
        $listeParticipant = ORM::for_table("utilisateurtoseance")->where("idseance", $idSeance)->where("participe", true)->findArray();
        foreach ($listeParticipant as $p) {
            if (strcmp($p->typepaiement, "unite") == 0) {
                $u = $this->getUtilisateur($p->idutilisateur);
                $u->nombreseancedisponible = $u->nombreseancedisponible + 1;
            }
        }
    }

    function getCoachs()
    {
        return ORM::for_table("utilisateur")->where("idstatusutilisateur", 3)->orderByAsc("nom")->findMany();
    }

    function getClients()
    {
        return ORM::for_table("utilisateur")->where("idstatusutilisateur", 4)->orderByAsc("nom")->findMany();
    }

    function getUtilisateurByMail($mail)
    {
        return ORM::for_table("utilisateur")->where("mail", $mail)->findOne();
    }

    function getAllSeanceCoachNonPassee($idCoach)
    {
        return ORM::for_table("seance")->whereGt('datedebut', date("Y-m-d h:i"))->where("idutilisateurcoach", $idCoach)->findMany();
    }

    function getAllSeanceNonCommenceeClient($idClient)
    {
        return ORM::for_table("utilisateurtoseance")->where("participe", true)->where("idutilisateur", $idClient)->findMany();
    }

    function saveSeance($idActivite, $idCoach, $dateDebut, $dateFin, $nbPlace, $prix)
    {
        $message = "";

        if (strtotime($dateDebut) < strtotime(date("Y-m-d H:i:s"))) {
            $message = "La date de début doit être supérieur à la date actuelle";
        } else if (strtotime($dateDebut) > strtotime($dateFin)) {
            $message = "La date de début doit être avant la date de fin";
        } else if (!is_null($idCoach)) {
            foreach ($this->getAllSeanceCoachNonPassee($idCoach) as $s) {
                $td1 = strtotime($dateDebut);
                $tf1 = strtotime($dateFin);
                $td2 = strtotime($s->datedebut);
                $tf2 = strtotime($s->datefin);
                if (!(($td1 > $tf2) || ($tf1 < $td2))) {
                    $message = "Le coach est déjà pris pour cette période";
                    break;
                }
            }
        } else if ($nbPlace < 1) {
            $message = "Le nombre de place doit être de 1 minimum";
        } else if ($prix < 0) {
            $message = "Le prix ne peut être négatif";
        }

        if (strcmp($message, "") == 0) {
            $seance = ORM::for_table("seance")->create();
            $seance->id = $this->getLastId("seance");
            $seance->datedebut = $dateDebut;
            $seance->datefin = $dateFin;
            $seance->nbplace = $nbPlace;
            $seance->prix = $prix;
            $seance->idactivite = $idActivite;
            $seance->idutilisateurcoach = $idCoach;
            $seance->save();
        }

        return $message;
    }

    function updateCoachOnSeance($idSeance, $idCoach)
    {
        $message = "";
        $seance = ORM::for_table("seance")->where("id", $idSeance)->findOne();
        if (!is_null($idCoach) && $seance->idutilisateurcoach != $idCoach) {

            foreach ($this->getAllSeanceCoachNonPassee($idCoach) as $s) {
                if ($s->id != $idSeance) {
                    $td1 = strtotime($seance->datedebut);
                    $tf1 = strtotime($seance->datefin);
                    $td2 = strtotime($s->datedebut);
                    $tf2 = strtotime($s->datefin);
                    if (!(($td1 > $tf2) || ($tf1 < $td2))) {
                        $message = "Le coach est déjà pris pour cette période";
                        break;
                    }
                }
            }
        }
        if (strcmp($message, "") == 0) {
            $seance->idutilisateurcoach = $idCoach;
            $seance->save();
        }

        return $message;
    }

    function getSeance($idSeance)
    {
        return ORM::for_table("seance")->where("id", $idSeance)->findOne();
    }

    function deleteSeanceClient($idClient, $idSeance)
    {
        $us = ORM::for_table("utilisateurtoseance")->where("idutilisateur", $idClient)->where("idseance", $idSeance)->where("participe", true)->findOne();
        $us->participe = false;
        $us->save();
        if (strcmp($us->typepaiement, "unite") == 0) {
            $u = $this->getUtilisateur($idClient);
            $u->nombreseancedisponible = $u->nombreseancedisponible + 1;
            $u->save();
        }
    }

    function ajoutClientToSeance($idClient, $idSeance, $typepaiement)
    {
        $us = ORM::for_table("utilisateurtoseance")->create();
        $us->id = $this->getLastId("utilisateurtoseance");
        $us->idutilisateur = $idClient;
        $us->idseance = $idSeance;
        $us->participe = true;
        $us->typepaiement = $typepaiement;
        $us->save();

        if (strcmp($typepaiement, "unite") == 0) {
            $u = $this->getUtilisateur($idClient);
            $u->nombreseancedisponible = $u->nombreseancedisponible - 1;
            $u->save();
        }
    }
}