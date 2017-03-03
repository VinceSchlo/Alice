<?php

/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 15:57
 */
class PlanStd {

    private $idAgent;
    private $idJour;
    private $horaireDeb;
    private $horaireFin;
    private $idPoste;

    /**
     * PlanSTD constructor.
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
    public function getIdJour() {
        return $this->idJour;
    }

    /**
     * @param mixed $idJour
     */
    public function setIdJour($idJour) {
        $this->idJour = $idJour;
    }

    /**
     * @return mixed
     */
    public function getHoraireDeb() {
        return $this->horaireDeb;
    }

    /**
     * @param mixed $horaireDeb
     */
    public function setHoraireDeb($horaireDeb) {
        $this->horaireDeb = $horaireDeb;
    }

    /**
     * @return mixed
     */
    public function getHoraireFin() {
        return $this->horaireFin;
    }

    /**
     * @param mixed $horaireFin
     */
    public function setHoraireFin($horaireFin) {
        $this->horaireFin = $horaireFin;
    }

    /**
     * @return mixed
     */
    public function getIdPoste() {
        return $this->idPoste;
    }

    /**
     * @param mixed $idPoste
     */
    public function setIdPoste($idPoste) {
        $this->idPoste = $idPoste;
    }

    public function selectPlanStd() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL
        $sql = "SELECT a.idAgent, prenom, idJour, libPoste, poste.idPoste, g.coulGroupe, plan.horaireDeb, plan.horaireFin
                FROM agent as a
                JOIN planstd as plan
                JOIN poste as poste
                JOIN groupe as g
                ON a.idAgent = plan.idAgent
                AND plan.idPoste = poste.idPoste
                AND g.idGroupe = poste.idGroupe
                ORDER BY a.prenom, plan.idJour, plan.horaireDeb";

        $resu = $dao->executeRequete($sql);

        $ligne = $resu->fetchall(PDO::FETCH_ASSOC);

        return $ligne;
    }

    public function selectPlanStdInactif() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL
        $sql = "SELECT a.idAgent, prenom, idJour, libPoste, poste.idPoste, g.coulGroupe, plan.horaireDeb, plan.horaireFin
                FROM agent as a
                JOIN planstd as plan
                JOIN poste as poste
                JOIN groupe as g
                ON a.idAgent = plan.idAgent
                AND plan.idPoste = poste.idPoste
                AND g.idGroupe = poste.idGroupe
                WHERE a.statut != 'I'
                ORDER BY a.prenom, plan.idJour, plan.horaireDeb";

        $resu = $dao->executeRequete($sql);

        $ligne = $resu->fetchall(PDO::FETCH_ASSOC);

        return $ligne;
    }

    public function selectDecPlanStd() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL
        $sql = "SELECT a.idAgent, idJour, poste.idPoste, plan.horaireDeb, plan.horaireFin
                FROM agent as a
                JOIN planstd as plan
                JOIN poste as poste
                ON a.idAgent = plan.idAgent
                AND plan.idPoste = poste.idPoste
                WHERE a.statut != 'I' AND poste.idGroupe=1 OR poste.idGroupe=2
                ORDER BY plan.idJour, plan.horaireDeb";

        $resu = $dao->executeRequete($sql);

        $ligne = $resu->fetchall(PDO::FETCH_ASSOC);

        return $ligne;
    }

    public function selectSamedisPlanStd() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL
        $sql = "SELECT a.idAgent, COUNT(idJour)
                FROM planstd as plan
                JOIN agent as a
                ON plan.idAgent = a.idAgent
                WHERE a.statut != 'I' AND plan.idJour=6
                GROUP BY a.idAgent";

        $resu = $dao->executeRequete($sql);

        $ligne = $resu->fetchall(PDO::FETCH_ASSOC);

        return $ligne;
    }

    function updatePlanStd() {
        // Connexion à la base de données
        $dao = new Dao();
        // Requête SQL
        $sql = "UPDATE planstd SET idPoste='$this->idPoste' WHERE idAgent='$this->idAgent' AND idJour='$this->idJour' AND horaireDeb='$this->horaireDeb' AND horaireFin='$this->horaireFin'";

        $resu = $dao->executeRequete($sql);

        return $resu; // retourne un string contenant la ligne de commande SQL
    }

}
