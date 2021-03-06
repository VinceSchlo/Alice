<?php

/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 16:05
 */
class Ferie {

    private $idFerie;
    private $nomFerie;
    private $dateDebFerie;
    private $dateFinFerie;

    /**
     * Ferie constructor.
     */
    public function __construct() {
        
    }

    // Génération des Getters

    function getIdFerie() {
        return $this->idFerie;
    }

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
    function setIdFerie($idFerie) {
        $this->idFerie = $idFerie;
    }

    function setNomFerie($nomFerie) {
        $this->nomFerie = $nomFerie;
    }

    function setDateDebFerie($dateDebFerie) {
        $this->dateDebFerie = $dateDebFerie;
    }

    function setDateFinFerie($dateFinFerie) {
        $this->dateFinFerie = $dateFinFerie;
    }

    public function selectFerie($debutSemaine, $finSemaine) {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL
        $sql = "SELECT * FROM ferie WHERE dateDebFerie BETWEEN '$debutSemaine' AND '$finSemaine'";
        $resu = $dao->executeRequete($sql);
        return $resu->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function selectAllFerie() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL 
        $sql = "SELECT * FROM ferie";
        $resu = $dao->executeRequete($sql);
        return $resu->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateFerie() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL
        $sql = "UPDATE ferie SET dateDebFerie='$this->dateDebFerie', dateFinFerie='$this->dateFinFerie' WHERE idFerie='$this->idFerie'";
        $resu = $dao->executeRequete($sql);
    }

}
