<?php

/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 16:05
 */
class Ferie
{
    private $idFerie;
    private $typeFerie;
    private $dateFerie;

    /**
     * Ferie constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getIdFerie()
    {
        return $this->idFerie;
    }

    /**
     * @param mixed $idFerie
     */
    public function setIdFerie($idFerie)
    {
        $this->idFerie = $idFerie;
    }

    /**
     * @return mixed
     */
    public function getTypeFerie()
    {
        return $this->typeFerie;
    }

    /**
     * @param mixed $typeFerie
     */
    public function setTypeFerie($typeFerie)
    {
        $this->typeFerie = $typeFerie;
    }

    /**
     * @return mixed
     */
    public function getDateFerie()
    {
        return $this->dateFerie;
    }

    /**
     * @param mixed $dateFerie
     */
    public function setDateFerie($dateFerie)
    {
        $this->dateFerie = $dateFerie;
    }


}