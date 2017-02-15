<?php

/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 16:04
 */
class Vacances
{
    private $idVac;
    private $typeVac;
    private $dateDebVac;
    private $dateFinVac;

    /**
     * Vacances constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getIdVac()
    {
        return $this->idVac;
    }

    /**
     * @param mixed $idVac
     */
    public function setIdVac($idVac)
    {
        $this->idVac = $idVac;
    }

    /**
     * @return mixed
     */
    public function getTypeVac()
    {
        return $this->typeVac;
    }

    /**
     * @param mixed $typeVac
     */
    public function setTypeVac($typeVac)
    {
        $this->typeVac = $typeVac;
    }

    /**
     * @return mixed
     */
    public function getDateDebVac()
    {
        return $this->dateDebVac;
    }

    /**
     * @param mixed $dateDebVac
     */
    public function setDateDebVac($dateDebVac)
    {
        $this->dateDebVac = $dateDebVac;
    }

    /**
     * @return mixed
     */
    public function getDateFinVac()
    {
        return $this->dateFinVac;
    }

    /**
     * @param mixed $dateFinVac
     */
    public function setDateFinVac($dateFinVac)
    {
        $this->dateFinVac = $dateFinVac;
    }


}