<?php
require_once ('../include/alice_dao.inc.php');
/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 16:02
 */
class Poste
{
    private $idPoste;
    private $libPoste;
    private $typePoste;
    private $idGroupe;

    /**
     * Poste constructor.
     */
    public function __construct()
    {
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

    /**
     * @return mixed
     */
    public function getLibPoste()
    {
        return $this->libPoste;
    }

    /**
     * @param mixed $libPoste
     */
    public function setLibPoste($libPoste)
    {
        $this->libPoste = $libPoste;
    }

    /**
     * @return mixed
     */
    public function getTypePoste()
    {
        return $this->typePoste;
    }

    /**
     * @param mixed $typePoste
     */
    public function setTypePoste($typePoste)
    {
        $this->typePoste = $typePoste;
    }

    /**
     * @return mixed
     */
    public function getIdGroupe()
    {
        return $this->idGroupe;
    }

    /**
     * @param mixed $idGroupe
     */
    public function setIdGroupe($idGroupe)
    {
        $this->idGroupe = $idGroupe;
    }


}