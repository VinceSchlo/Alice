<?php

/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 16:04
 */
class Vacances
{
    private $nomVac;
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
    public function getNomVac()
    {
        return $this->nomVac;
    }

    /**
     * @param mixed $nomVac
     */
    public function setNomVac($nomVac)
    {
        $this->nomVac = $nomVac;
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