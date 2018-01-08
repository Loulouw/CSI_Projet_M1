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
        $id = ORM::for_table($table)->count() + 1;
        return $id;
    }

    function enregistrerUtilisateur($nom, $prenom, $mail, $tel)
    {
        $utilisateurDejaPresent = ORM::for_table('utilisateur')->where('mail', $mail)->findArray();
        if (count($utilisateurDejaPresent) == 1) {
            return false;
        }

        $pwd = $this->randomPassword();
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
        $utilisateur->idstatusutilisateur = 4;
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
            $res = $utilisateur;
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

}