<?php
require_once ('../include/alice_dao.inc.php');
/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 15:57
 */
class PlanStd
{
    private $idAgent;
    private $idJour;
    private $horaireDeb;
    private $horaireFin;
    private $idPoste;

    /**
     * PlanSTD constructor.
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
    public function getIdJour()
    {
        return $this->idJour;
    }

    /**
     * @param mixed $idJour
     */
    public function setIdJour($idJour)
    {
        $this->idJour = $idJour;
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

    public function selectPlanStd()
    {
        $dao = new Dao();

        $sql = "SELECT a.idAgent, prenom, idJour, libPoste, poste.idPoste, g.coulGroupe, plan.horaireDeb, plan.horaireFin
                FROM agent as a
                JOIN planstd as plan
                JOIN poste as poste
                JOIN groupe as g
                ON a.idAgent = plan.idAgent
                AND plan.idPoste = poste.idPoste
                AND g.idGroupe = poste.idGroupe
                ORDER BY a.idAgent, plan.idJour, plan.horaireDeb";

        $resu = $dao->executeRequete($sql);

        $ligne = $resu->fetchall(PDO::FETCH_ASSOC);

        return $ligne;
    }

    public function selectUser(){
        $dao = new Dao();

        $sql = "SELECT prenom 
                FROM agent as a
                ORDER BY idAgent ASC";

        $resu = $dao->executeRequete($sql);

        $ligne = $resu->fetchall(PDO::FETCH_ASSOC);

        return $ligne;

    }
}