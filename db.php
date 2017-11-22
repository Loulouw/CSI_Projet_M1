<?php


require_once 'idiorm-master/idiorm.php';

class db {



    function __construct()
    {
        //Config PDO
        ORM::configure('pgsql:host=localhost;port=5432;dbname=csi;user=postgres;password=pmtlouor');
        ORM::configure('return_result_sets', true); // returns result sets
        ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

        //override cles primaires
        ORM::configure('id_column_overrides', array(
                'administre'    => array('idevenement','idutilisateur'),
                'commente'      => array('idevenement','idutilisateur'),
                'evenement'     => 'idevenement',
                'histBureau'    => 'idbureau',
                'participe'     => array('idevenement','idutilisateur'),
                'statut'        => 'idstatut',
                'utilisateur'   => 'idutilisateur'
        ));
    }

    function updateUserInformations($id, $nom, $prenom, $datenaiss, $adresse, $codepostal, $ville, $idstatut){
        $user = ORM::for_table('utilisateur')->where('idutilisateur', $id)->find_one();
        $user->nom = $nom;
        $user->prenom = $prenom;
        $user->datenaiss = $datenaiss;
        $user->adresse = $adresse;
        $user->codepostal = $codepostal;
        $user->ville = $ville;
        $user->idstatut = $idstatut;
        $user->save();
    }

	function updateUser($mail){
		$user = ORM::for_table('utilisateur')->where('mail', $mail)->find_one();
        $user->datepaiement = date('Y-m-d');
		$user->save();
	}
	
	function getBureau(){
			$id = ORM::for_table('histbureau')->count('*');
            $bureau=    ORM::for_table('histbureau')->where('idbureau', $id)->find_one();
        return array($bureau['id1'],$bureau['id2'],$bureau['id3'],$bureau['id4'],$bureau['id5'],$bureau['id6'],$bureau['id7']);
	}
    
    function getAllStatut(){
        return ORM::for_table('statut')->findArray();
    }
    
    function getStatut($id){
        return ORM::for_table('statut')->where('idstatut', $id)->findArray()[0]["nomstatut"];
    }

    function canDeleteComment($idUser){
        $statut = $this->getStatut($idUser);
        $idCanDelete = array(1,2,3,4,5,6,7);
        return in_array($statut, $idCanDelete);
    }
	
    function getLatestEvent(){
        return ORM::for_table('evenement')->find_one()->order_by_asc('datedebut')->where('valide', 'true');
    }

    function getPendingEvent(){
        return ORM::for_table('evenement')->where('valide', 'false')->findArray();
    }
    
    function getAllMembers(){
        return ORM::for_table('utilisateur')->order_by_asc('idutilisateur')->findArray();
    }
    
    function getAllMembersDate(){
        return ORM::for_table('utilisateur')->order_by_desc('dateinscr')->findArray();
    }
    
    function getEvent($id){
        return ORM::for_table('evenement')->where('idevenement', $id)->findArray()[0];
    }
    
    function getUser($id){
        return ORM::for_table('utilisateur')->where('idutilisateur', $id)->findArray()[0];
    }

    function getAllEvents(){
        return ORM::for_table('evenement')->findArray();
    }
    
    function getEventId(){
        return ORM::for_table('evenement')->max('idevenement') + 1;
    }
    
    function addEvent($id, $titre, $datedebut, $datefin, $nbplaces, $tarifmembre, $tarifbase, $idorga, $desc, $lieu){
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
    
    function getUserId($mail){
        return ORM::for_table('utilisateur')->where_like('mail', "$mail%")->findArray()[0]['idutilisateur'];
    }
    
    function isRoot($mail){
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
    
    function acceptAd($id){
        $user = ORM::for_table('utilisateur')->where('idutilisateur', $id)->find_one();
        $user->idstatut = 9;
        $user->datedelabdeadh = date('Y-m-d');
        $user->save();
    }
    
    function getPendingAd(){
        return ORM::for_table('utilisateur')->where('idstatut', 8)->where_not_null('datepaiement')->findArray();
    }
    
    function getAllParticipations(){
        return ORM::for_table('participe')->where('paye', 'true')->findArray();
    }

    function valideEvent($id){
        $event = ORM::for_table('evenement')->where('idevenement', $id)->find_one();
        $event->valide = true;
        $event->save();
    }
    
    function cancelEvent($id){
		ORM::for_table('commente')->where('idevenement',$id)->delete_many();
        ORM::for_table('administre')->where('idevenement', $id)->delete_many();
		ORM::for_table('participe')->where('idevenement',$id)->delete_many();
		ORM::for_table('evenement')->where('idevenement', $id)->delete_many();
    }
    
    function register($nom, $prenom, $dob, $adresse, $cp, $ville, $mail, $pwd){
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

    function connect($mail, $pwd){
        $count = ORM::for_table('utilisateur')->where(array(
            'mail' => $mail,
            'mdp'  => $pwd
        ))->count('*');
        return $count;
    }

    function getComments($id){
        $test =    ORM::for_table('commente')->where('idevenement', $id)->find_array();
        return $test;
    }

    function deleteComment($idEvent, $idUser){
        ORM::for_table('commente')->where(array(
            "idutilisateur" => $idUser,
            "idevenement" => $idEvent
        ))->delete_many();
    }

    function getNameFromComment($id){
        $user = ORM::for_table('utilisateur')->find_one($id);
        return $user->prenom;
    }

    function getTarifForUser($mail, $idevent){
        $count = ORM::for_table('utilisateur')->where(array(
            'mail' => $mail,
            'idstatut'  => '3'
        ))->count('*');
        if($count > 0){
            $event = ORM::for_table('evenement')->find_one($idevent);
            return $event['tarifmembre'];
        }else{
            $event = ORM::for_table('evenement')->find_one($idevent);
            return $event['tarifbase'];
        }
    }

    function createCommente($mail, $idevent,$comment){
        $user = ORM::for_table('utilisateur')->where('mail', $mail)->find_one();
       // return array($user['idUtilisateur'], $idevent, $comment);

        $commente = ORM::for_table('commente')->create();

        $commente['idutilisateur'] = $user['idutilisateur'];
        $commente->idevenement = $idevent;
        $commente->texte = $comment;

        $commente->save();
    }

    function getNbParticipants($id){
        return ORM::for_table('participe')->where('idevenement', $id)->count();
    }

    function addParticipe($idevent, $mail){
        $user = ORM::for_table('utilisateur')->where('mail', $mail)->find_one();
        $participe = ORM::for_table('participe')->create();

        $participe->idutilisateur = $user['idutilisateur'];
        $participe->idevenement = $idevent;
        $particip['paye'] = 'true';

        $participe->save();
    }
}