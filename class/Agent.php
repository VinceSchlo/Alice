<?php
/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 15:52
 */
class Agent
{
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
    public function __construct()
    {
    }


    /**
     * @return mixed
     */
    public function getIdAgent()
    {
        return $this->idAgent;
    }

    /**
     * @param mixed $idAgent
     */
    public function setIdAgent($idAgent)
    {
        $this->idAgent = $idAgent;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * @param mixed $mdp
     */
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getIdBiblio()
    {
        return $this->idBiblio;
    }

    /**
     * @param mixed $idBiblio
     */
    public function setIdBiblio($idBiblio)
    {
        $this->idBiblio = $idBiblio;
    }
    
    function selectAllAgent() {
        // Connexion à la base de données
        $dao = new Dao();
        //Requête SQL 
        $sql = "SELECT * FROM agent ORDER BY 2";
        $resu = $dao->executeRequete($sql);
        return $resu->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateAgent() {
        // Connexion à la base de données
        $dao = new Dao();
        //Requête SQL
        $sql = "UPDATE agent SET login='$this->login', mdp='$this->mdp', statut='$this->statut' WHERE idAgent='$this->idAgent'";
        $resu = $dao->executeRequete($sql);
    }
    
    function deleteAgent() {
         // Connexion à la base de données
        $dao = new Dao();
        //Requête SQL
        $sql = "delete from agent WHERE idAgent='$this->idAgent'";
        $dao->executeRequete($sql);
    }
}