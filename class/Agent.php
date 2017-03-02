<?php

/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 15:52
 */
class Agent {

    private $idAgent;
    private $nom;
    private $prenom;
    private $login;
    private $mdp;
    private $statut;
    private $idBiblio;

    /**
     * Agent constructor.
     */
    public function __construct() {
        
    }

    /**
     * @return mixed
     */
    public function getIdAgent() {
        return $this->idAgent;
    }

    /**
     * @param mixed $idAgent
     */
    public function setIdAgent($idAgent) {
        $this->idAgent = $idAgent;
    }

    /**
     * @return mixed
     */
    public function getNom() {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom) {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom() {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom) {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login) {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getMdp() {
        return $this->mdp;
    }

    /**
     * @param mixed $mdp
     */
    public function setMdp($mdp) {
        $this->mdp = $mdp;
    }

    /**
     * @return mixed
     */
    public function getStatut() {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut) {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getIdBiblio() {
        return $this->idBiblio;
    }

    /**
     * @param mixed $idBiblio
     */
    public function setIdBiblio($idBiblio) {
        $this->idBiblio = $idBiblio;
    }

    public function selectPrenomAgent() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL 
        $sql = "SELECT prenom FROM agent";
        $resu = $dao->executeRequete($sql);
        $ligne = $resu->fetchall(PDO::FETCH_ASSOC);
        return $ligne;
    }
    
    public function selectIdPrenomAgent() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL 
        $sql = "SELECT idAgent, prenom FROM agent";
        $resu = $dao->executeRequete($sql);
        $ligne = $resu->fetchall(PDO::FETCH_ASSOC);
        return $ligne;
    }

    function selectAgentByName() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL 
        $sql = "SELECT * FROM agent ORDER BY 2";
        $resu = $dao->executeRequete($sql);
        return $resu->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateAgent() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL
        $sql = "UPDATE agent SET nom='$this->nom', prenom='$this->prenom', login='$this->login', mdp='$this->mdp', statut='$this->statut' WHERE idAgent='$this->idAgent'";
        $resu = $dao->executeRequete($sql);
    }

    function deleteAgent() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL
        $sql = "DELETE FROM planstd where idAgent='$this->idAgent';
                DELETE FROM planreel where idAgent='$this->idAgent';
                DELETE FROM agent WHERE idAgent='$this->idAgent'";
        $dao->executeRequete($sql);
    }

    function getMaxIdAgent() { // Retourne la valeur MAX de l'idAgent
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL 
        $sql = "SELECT MAX(idAgent) FROM agent";
        $resu = $dao->executeRequete($sql);
        return $resu->fetchAll(PDO::FETCH_ASSOC);
    }

    function insertAgent() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL
        $sql = "INSERT INTO agent (idAgent, nom, prenom, login, mdp, statut, idBiblio) VALUES ('" . $this->idAgent . "', '" . $this->nom . "',  '" . $this->prenom . "',  '" . $this->login . "',  '" . $this->mdp . "',  '" . $this->statut . "',  '" . $this->idBiblio . "' );
                INSERT INTO planstd (idAgent, idJour, horaireDeb, horaireFin, idPoste) VALUES 
                ('" . $this->idAgent . "', 1, 2, 4, 21), ('" . $this->idAgent . "', 1, 4, 7, 21), 
                ('" . $this->idAgent . "', 2, 2, 4, 21), ('" . $this->idAgent . "', 2, 4, 7, 21),
                ('" . $this->idAgent . "', 3, 1, 3, 21), ('" . $this->idAgent . "', 3, 3, 4, 21), ('" . $this->idAgent . "', 3, 4, 7, 21),
                ('" . $this->idAgent . "', 4, 2, 4, 21), ('" . $this->idAgent . "', 4, 4, 7, 21),
                ('" . $this->idAgent . "', 5, 2, 4, 21), ('" . $this->idAgent . "', 5, 4, 7, 21),
                ('" . $this->idAgent . "', 6, 1, 3, 21), ('" . $this->idAgent . "', 6, 3, 5, 21); ";
        $resu = $dao->executeRequete($sql);
        return $resu; // retourne un string contenant la ligne de commande SQL
    }

    // Fonction pour vérifier si l'identifiant existe dans la bdd.
    public function connexionAgent() {

        $dao = new Dao();
        $user = null;
// Requête SQL
        $sql = "SELECT * FROM agent WHERE login='$this->login' AND mdp='$this->mdp'";
        $resu = $dao->executeRequete($sql);
        $ligne = $resu->fetch(PDO::FETCH_ASSOC);

        if ($resu->rowcount()) {
            $user = $ligne;
        }

        return $user;
    }

}
