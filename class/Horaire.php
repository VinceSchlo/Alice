<?php
require_once ('../include/alice_dao.inc.php');
/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 16:01
 */
class Horaire
{
    private $idHoraire;
    private $LibHoraire;

    /**
     * Horaire constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getIdHoraire()
    {
        return $this->idHoraire;
    }

    /**
     * @param mixed $idHoraire
     */
    public function setIdHoraire($idHoraire)
    {
        $this->idHoraire = $idHoraire;
    }

    /**
     * @return mixed
     */
    public function getLibHoraire()
    {
        return $this->LibHoraire;
    }

    /**
     * @param mixed $LibHoraire
     */
    public function setLibHoraire($LibHoraire)
    {
        $this->LibHoraire = $LibHoraire;
    }


}