<?php

/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 16:05
 */
class Ferie
{

    private $nomFerie;
    private $dateDebFerie;
    private $dateFinFerie;

    /**
     * Ferie constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getNomFerie()
    {
        return $this->nomFerie;
    }

    /**
     * @param mixed $nomFerie
     */
    public function setNomFerie($nomFerie)
    {
        $this->nomFerie = $nomFerie;
    }

    /**
     * @return mixed
     */
    public function getDateDebutFerie()
    {
        return $this->dateDebFerie;
    }

    /**
     * @param mixed $dateDebutFerie
     */
    public function setDateDebutFerie($dateDebutFerie)
    {
        $this->dateDebFerie = $dateDebutFerie;
    }

    /**
     * @return mixed
     */
    public function getDateFinFerie()
    {
        return $this->dateFinFerie;
    }

    /**
     * @param mixed $dateFinFerie
     */
    public function setDateFinFerie($dateFinFerie)
    {
        $this->dateFinFerie = $dateFinFerie;
    }



}