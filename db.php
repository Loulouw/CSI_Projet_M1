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
        $utilisateur->datepaiementfrais = date("Y-m-d H:i:s");
        $utilisateur->nombreseancedisponible = 1;
        $utilisateur->compteactif = true;
        $utilisateur->datederniereinteraction = date("Y-m-d H:i:s");
        $utilisateur->idstatusutilisateur = 4;
        $utilisateur->idconnexion = $connexion->id;
        $utilisateur->datevalidationpaiement = date("Y-m-d H:i:s");
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

    function connexion($identifiant,$password){
        $res = 0;
        $connexion = ORM::for_table('connexion')->where('identifiantconnexion',$identifiant)->findOne();
        if($connexion != null && password_verify($password,$connexion->motdepasse)){
            $utilisateur = ORM::for_table('utilisateur')->where('idconnexion',$connexion->id)->findOne();
            $res = $utilisateur->id;
        }
        return $res;
    }






    function updateUser($mail)
    {
        $user = ORM::for_table('utilisateur')->where('mail', $mail)->find_one();
        $user->datepaiement = date('Y-m-d');
        $user->save();
    }

    function getBureau()
    {
        $id = ORM::for_table('histbureau')->count('*');
        $bureau = ORM::for_table('histbureau')->where('idbureau', $id)->find_one();
        return array($bureau['id1'], $bureau['id2'], $bureau['id3'], $bureau['id4'], $bureau['id5'], $bureau['id6'], $bureau['id7']);
    }

    function getAllStatut()
    {
        return ORM::for_table('statut')->findArray();
    }

    function getStatut($id)
    {
        return ORM::for_table('statut')->where('idstatut', $id)->findArray()[0]["nomstatut"];
    }

    function canDeleteComment($idUser)
    {
        $statut = $this->getStatut($idUser);
        $idCanDelete = array(1, 2, 3, 4, 5, 6, 7);
        return in_array($statut, $idCanDelete);
    }

    function getLatestEvent()
    {
        return ORM::for_table('evenement')->find_one()->order_by_asc('datedebut')->where('valide', 'true');
    }

    function getPendingEvent()
    {
        return ORM::for_table('evenement')->where('valide', 'false')->findArray();
    }

    function getAllMembers()
    {
        return ORM::for_table('utilisateur')->order_by_asc('idutilisateur')->findArray();
    }

    function getAllMembersDate()
    {
        return ORM::for_table('utilisateur')->order_by_desc('dateinscr')->findArray();
    }

    function getEvent($id)
    {
        return ORM::for_table('evenement')->where('idevenement', $id)->findArray()[0];
    }

    function getUser($id)
    {
        return ORM::for_table('utilisateur')->where('idutilisateur', $id)->findArray()[0];
    }

    function getAllEvents()
    {
        return ORM::for_table('evenement')->findArray();
    }

    function getEventId()
    {
        return ORM::for_table('evenement')->max('idevenement') + 1;
    }

    function addEvent($id, $titre, $datedebut, $datefin, $nbplaces, $tarifmembre, $tarifbase, $idorga, $desc, $lieu)
    {
        $event = ORM::for_table('evenement')->create();
        $event->idevenement = $id;
        $event->titreevenement = $titre;
        $event->datedebut = $datedebut;
        $event->datefin = $datefin;
        $event->fini = 'false';
        $event->nbplaces = $nbplaces;
        $event->discute = 'false';
        $event->valide = 'false';
        $event->tarifmembre = $tarifmembre;
        $event->tarifbase = $tarifbase;
        $event->idorganisateur = $idorga;
        $event->description = $desc;
        $event->lieu = $lieu;
        $event->save();
    }

    function getUserId($mail)
    {
        return ORM::for_table('utilisateur')->where_like('mail', "$mail%")->findArray()[0]['idutilisateur'];
    }

    function isRoot($mail)
    {
        $id = $this->getUserId($mail);
        return !empty(ORM::for_table('histbureau')
            ->where_any_is(array(
                array('id1' => $id),
                array('id2' => $id),
                array('id3' => $id),
                array('id4' => $id),
                array('id5' => $id),
                array('id6' => $id),
                array('id7' => $id)))
            ->findArray());
    }

    function acceptAd($id)
    {
        $user = ORM::for_table('utilisateur')->where('idutilisateur', $id)->find_one();
        $user->idstatut = 9;
        $user->datedelabdeadh = date('Y-m-d');
        $user->save();
    }

    function getPendingAd()
    {
        return ORM::for_table('utilisateur')->where('idstatut', 8)->where_not_null('datepaiement')->findArray();
    }

    function getAllParticipations()
    {
        return ORM::for_table('participe')->where('paye', 'true')->findArray();
    }

    function valideEvent($id)
    {
        $event = ORM::for_table('evenement')->where('idevenement', $id)->find_one();
        $event->valide = true;
        $event->save();
    }

    function cancelEvent($id)
    {
        ORM::for_table('commente')->where('idevenement', $id)->delete_many();
        ORM::for_table('administre')->where('idevenement', $id)->delete_many();
        ORM::for_table('participe')->where('idevenement', $id)->delete_many();
        ORM::for_table('evenement')->where('idevenement', $id)->delete_many();
    }

    function register($nom, $prenom, $dob, $adresse, $cp, $ville, $mail, $pwd)
    {
        $id = ORM::for_table('utilisateur')->count();
        $id++;

        $person = ORM::for_table('utilisateur')->create();

        $person->idutilisateur = $id;
        $person->nom = $nom;
        $person->prenom = $prenom;
        $person->datenaiss = $dob;
        $person->adresse = $adresse;
        $person->codepostal = $cp;
        $person->ville = $ville;
        //$person->dateinscr = '19/10/1995';
        $person->idstatut = 2;
        $person->mail = $mail;
        $person->mdp = $pwd;
        $person->save();
    }

    function connect($mail, $pwd)
    {
        $count = ORM::for_table('utilisateur')->where(array(
            'mail' => $mail,
            'mdp' => $pwd
        ))->count('*');
        return $count;
    }

    function getComments($id)
    {
        $test = ORM::for_table('commente')->where('idevenement', $id)->find_array();
        return $test;
    }

    function deleteComment($idEvent, $idUser)
    {
        ORM::for_table('commente')->where(array(
            "idutilisateur" => $idUser,
            "idevenement" => $idEvent
        ))->delete_many();
    }

    function getNameFromComment($id)
    {
        $user = ORM::for_table('utilisateur')->find_one($id);
        return $user->prenom;
    }

    function getTarifForUser($mail, $idevent)
    {
        $count = ORM::for_table('utilisateur')->where(array(
            'mail' => $mail,
            'idstatut' => '3'
        ))->count('*');
        if ($count > 0) {
            $event = ORM::for_table('evenement')->find_one($idevent);
            return $event['tarifmembre'];
        } else {
            $event = ORM::for_table('evenement')->find_one($idevent);
            return $event['tarifbase'];
        }
    }

    function createCommente($mail, $idevent, $comment)
    {
        $user = ORM::for_table('utilisateur')->where('mail', $mail)->find_one();
        // return array($user['idUtilisateur'], $idevent, $comment);

        $commente = ORM::for_table('commente')->create();

        $commente['idutilisateur'] = $user['idutilisateur'];
        $commente->idevenement = $idevent;
        $commente->texte = $comment;

        $commente->save();
    }

    function getNbParticipants($id)
    {
        return ORM::for_table('participe')->where('idevenement', $id)->count();
    }

    function addParticipe($idevent, $mail)
    {
        $user = ORM::for_table('utilisateur')->where('mail', $mail)->find_one();
        $participe = ORM::for_table('participe')->create();

        $participe->idutilisateur = $user['idutilisateur'];
        $participe->idevenement = $idevent;
        $particip['paye'] = 'true';

        $participe->save();
    }
}