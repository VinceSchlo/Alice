<?php

class Database {

    //attributs private
    private $serveur = "localhost";
    private $nomBdd = "alice";
    /*private $username = " ";
    private $password = " ";*/
    private $dbh = null;

    //Méthode connect 
    //Connection à la base de données bdd_pdo via la classe PDO
    public function connect() {
        $this->dbh = null;
        try {
            $dsn = "mysql:host=" . $this->serveur . ";dbname=" . $this->nomBdd;

            $this->dbh = new PDO($dsn, $this->username, $this->password);

            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $this->dbh->exec("SET NAMES 'UTF8'");
        } catch (PDOException $exception) {

            echo "Connection error: " . $exception->getMessage();
        }
        return $this->dbh;
    }

}
