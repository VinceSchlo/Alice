<?php

require_once('dbConfig.inc.php');

class Dao {

    private $maConnexion; // Attribut (=variable) de connexion : pointer le serveur SGBD et la BDD

    public function __construct() {
        $database = new Database;
        $db = $database->connect();
        $this->maConnexion = $db;
    }

    public function executeRequete($sql) {
        try {// Si tt va bien, va se connecter au serveur
            $resu = $this->maConnexion->prepare($sql);
            $resu->execute();
            return $resu;
        } catch (PDOException $exception) {// si pb, gestion des erreurs
            echo "Erreur de connexion avec la BDD :" . $exception->getMessage();
        }
    }

    public function disconnect() {
        $this->maConnexion = null;
    }

}
