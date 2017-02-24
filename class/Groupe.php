<?php

/**
 * Created by PhpStorm.
 * User: svinc
 * Date: 15/02/2017
 * Time: 16:03
 */
class Groupe {

    private $idGroupe;
    private $libGroupe;
    private $coulGroupe;

    /**
     * Groupe constructor.
     */
    public function __construct() {
        
    }

    /**
     * @return mixed
     */
    public function getIdGroupe() {
        return $this->idGroupe;
    }

    /**
     * @param mixed $idGroupe
     */
    public function setIdGroupe($idGroupe) {
        $this->idGroupe = $idGroupe;
    }

    /**
     * @return mixed
     */
    public function getLibGroupe() {
        return $this->libGroupe;
    }

    /**
     * @param mixed $libGroupe
     */
    public function setLibGroupe($libGroupe) {
        $this->libGroupe = $libGroupe;
    }

    /**
     * @return mixed
     */
    public function getCoulGroupe() {
        return $this->coulGroupe;
    }

    /**
     * @param mixed $coulGroupe
     */
    public function setCoulGroupe($coulGroupe) {
        $this->coulGroupe = $coulGroupe;
    }

}
