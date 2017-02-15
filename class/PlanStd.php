<?php

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


}