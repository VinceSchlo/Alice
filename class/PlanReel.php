<?php

/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 15:59
 */
class PlanReel
{

    private $idAgent;
    private $dateReel;
    private $horaireDeb;
    private $horaireFin;
    private $idPoste;

    /**
     * PlanReel constructor.
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
    public function getDateReel()
    {
        return $this->dateReel;
    }

    /**
     * @param mixed $dateReel
     */
    public function setDateReel($dateReel)
    {
        $this->dateReel = $dateReel;
    }

    /**
     * @return mixed
     */
    public function getHoraireDeb()
    {
        return $this->horaireDeb;
    }

    /**
     * @param mixed $horaireDeb
     */
    public function setHoraireDeb($horaireDeb)
    {
        $this->horaireDeb = $horaireDeb;
    }

    /**
     * @return mixed
     */
    public function getHoraireFin()
    {
        return $this->horaireFin;
    }

    /**
     * @param mixed $horaireFin
     */
    public function setHoraireFin($horaireFin)
    {
        $this->horaireFin = $horaireFin;
    }

    /**
     * @return mixed
     */
    public function getIdPoste()
    {
        return $this->idPoste;
    }

    /**
     * @param mixed $idPoste
     */
    public function setIdPoste($idPoste)
    {
        $this->idPoste = $idPoste;
    }

    public function selectPlanReel($dateDebut, $dateFin)
    {
        $dao = new Dao();

        $sql = "SELECT plan.idAgent , plan.dateReel, plan.horaireDeb, plan.horaireFin, plan.idPoste, pos.libPoste, grp.coulGroupe
                FROM planreel as plan
                JOIN poste as pos
                JOIN groupe as grp
                ON plan.idPoste = pos.idPoste AND pos.idGroupe = grp.idGroupe
                WHERE dateReel BETWEEN '$dateDebut' AND '$dateFin'";

        $resu = $dao->executeRequete($sql);

        $ligne = $resu->fetchall(PDO::FETCH_ASSOC);

        return $ligne;
    }
    public function selectDecPlanReel($dateDebut, $dateFin)
    {
        $dao = new Dao();

        $sql = "SELECT plan.idAgent , plan.dateReel, plan.idPoste, poste.idGroupe, plan.horaireDeb, plan.horaireFin
                FROM planreel as plan
                JOIN poste as poste
                ON plan.idPoste = poste.idPoste
                WHERE dateReel BETWEEN '$dateDebut' AND '$dateFin' AND poste.idGroupe=1 OR poste.idGroupe=2";

        $resu = $dao->executeRequete($sql);

        $ligne = $resu->fetchall(PDO::FETCH_ASSOC);

        return $ligne;
    }

    public function insertPlanReel()
    {
        $dao = new Dao();

        $sql = "INSERT INTO planreel (idAgent, dateReel, horaireDeb, horaireFin, idPoste) VALUES ('" . $this->idAgent . "',  '" . $this->dateReel . "',  '" . $this->horaireDeb . "',  '" . $this->horaireFin . "',  '" . $this->idPoste . "')";

        $dao->executeRequete($sql);
    }

    public function preUpdatePlanReel()
    {
        $dao = new Dao();

        $sql = "SELECT * FROM planreel WHERE idAgent = '$this->idAgent' AND horaireDeb = '$this->horaireDeb' AND horaireFin = '$this->horaireFin' AND dateReel = '$this->dateReel'";

        $resu = $dao->executeRequete($sql);

        $ligne = $resu->fetchall(PDO::FETCH_ASSOC);

        return $ligne;
    }

    public function updatePlanReel()
    {
        $dao = new Dao();

        $sql = "UPDATE planreel SET idPoste='$this->idPoste' WHERE idAgent='$this->idAgent' AND horaireDeb = '$this->horaireDeb' AND horaireFin = '$this->horaireFin' AND dateReel = '$this->dateReel'";

        $dao->executeRequete($sql);
    }

    public function deletePlanReel(){
        $dao = new Dao();

        $sql = "DELETE FROM planreel WHERE idAgent='$this->idAgent' AND horaireDeb = '$this->horaireDeb' AND horaireFin = '$this->horaireFin' AND dateReel = '$this->dateReel'";

        $dao->executeRequete($sql);
    }

}
