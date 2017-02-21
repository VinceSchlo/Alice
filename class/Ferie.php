<?php
/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 16:05
 */
class Ferie {

    private $nomFerie;
    private $dateDebFerie;
    private $dateFinFerie;

    /**
     * Ferie constructor.
     */
    public function __construct() {
 
    }

    // Génération des Getters
    function getNomFerie() {
        return $this->nomFerie;
    }

    function getDateDebFerie() {
        return $this->dateDebFerie;
    }

    function getDateFinFerie() {
        return $this->dateFinFerie;
    }

    // Génération des Setters
    function setNomFerie($nomFerie) {
        $this->nomFerie = $nomFerie;
    }

    function setDateDebFerie($dateDebFerie) {
        $this->dateDebFerie = $dateDebFerie;
    }

    function setDateFinFerie($dateFinFerie) {
        $this->dateFinFerie = $dateFinFerie;
    }

    
    function selectAllFerie() {
        // Connexion à la base de données
        $dao = new Dao();
        //Requête SQL 
        $sql = "SELECT * FROM ferie ORDER BY 2";
        $resu = $dao->executeRequete($sql);
        return $resu->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateFerie() {
        // Connexion à la base de données
        $dao = new Dao();
        $sql = "UPDATE ferie SET dateDebFerie='$this->dateDebFerie', dateFinFerie='$this->dateFinFerie' WHERE nomFerie='$this->nomFerie'";
        $resu = $dao->executeRequete($sql);
    }

    public function selectFerie($debutSemaine, $finSemaine){
        $dao = new Dao();

        $sql = "SELECT * FROM ferie WHERE dateDebFerie BETWEEN '$debutSemaine' AND '$finSemaine'";

        $resu = $dao->executeRequete($sql);

        return $resu->fetchAll(PDO::FETCH_ASSOC);
    }



}