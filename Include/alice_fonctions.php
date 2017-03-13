<?php

//Script : fonctions.php
//16/02/2017
//v.05
//JCC & Vince SCHLO
//
//
// Fonction pour afficher un nombre avec 2 chiffres après la virgule
function arrondi2virguleAff($unNombre) {
    $unNombre = number_format($unNombre, 2, ',', ' ');
    return $unNombre;
}

// Fonction pour formater un nombre avec 2 chiffres après un point pour l'enregistrer dans la BDD
function arrondi2virguleBDD($unNombre) {
    $unNombre = number_format($unNombre, 2, '.', ' ');
    return $unNombre;
}

// Fonction pour formater une chaine de caractère : enlever les espaces et la convertir en lowercase
function formatChaine($uneChaine) {
    $uneChaine = mb_strtolower(trim($uneChaine), 'UTF-8');

    return $uneChaine;
}

// Fonction pour détecter les tirets et mettre le caractère suivant en majuscule
function detecTiret($uneChaine) {

    $uneChaine = ucwords(formatChaine($uneChaine));

    for ($i = 0; $i < strlen($uneChaine); $i++) {
        if (($uneChaine[$i] == '-')) {
            $uneChaine[$i + 1] = ucfirst($uneChaine[$i + 1]);
        }
    }
    return $uneChaine;
}

// Fonction pour fabriquer un login avec les premières lettres du prénom + le nom,
// le tout en minuscule
function makeLogin($nom, $prenom) {

    $nom = formatChaine($nom);
    $prenom = formatChaine($prenom);
    $prenomLogin = substr($prenom, 0, 1);

    // Cas des prénoms multiples avec tiret ou espace dans le prénom
    for ($i = 0; $i < strlen($prenom); $i++) {
        if (($prenom[$i] == '-') || ($prenom[$i] == ' ')) {
            $prenomLogin = $prenomLogin . $prenom[$i + 1];
        }
    }
    $login = $prenomLogin . $nom;

    return $login;
}

// Fonction pour convertir une date FR en date US
function convertDateFrUs($uneDate) {

    $uneDate = implode('-', array_reverse(explode('/', $uneDate)));

    return $uneDate;
}

// Fonction pour convertir une date US en date FR
function convertDateUsFr($uneDate) {

    $uneDate = strftime('%d/%m/%Y', strtotime($uneDate));

    return $uneDate;
}

// Fonction qui retourne un tableau des dates des jours du dimanche au samedi en fonction de la semaine et de l'année
function datesJourSemaine($week, $year) {

    $tabDatesJoursSemaines = array();
    $firstDayInYear = date("N", mktime(0, 0, 0, 1, 1, $year));
    if ($firstDayInYear < 5)
        $shift = -($firstDayInYear - 1) * 86400;
    else
        $shift = (8 - $firstDayInYear) * 86400;
    if ($week > 1)
        $weekInSeconds = ($week - 1) * 604800;
    else
        $weekInSeconds = 0;
    for ($i = 0; $i <= 6; $i++) {
        $timestamp = mktime(0, 0, 0, 1, $i, $year) + $weekInSeconds + $shift;
        $tabDatesJoursSemaines[$i] = date("Y-m-d", $timestamp);
    }

    return $tabDatesJoursSemaines;
}

// Fonction pour retourner le jour de la semaine d'une date donnée. Ex : 01/01/2017 retourne 7 (dimanche)
function convertDateNumJour($uneDate) {
    $tabDate = explode('-', $uneDate);
    $timestamp = mktime(0, 0, 0, $tabDate[1], $tabDate[2], $tabDate[0]);
    $numJour = date('w', $timestamp);
    return $numJour;
}

// Fonction qui convertit un horaire time en float pour faire des calculs. Ex : 10:00:00 devient 10, 13:30:00 devient 13.5
function convertTimeStringToNumber($timeString) {

    $timeNumber = floatval(substr($timeString, 0, 5));
    if (substr($timeString, 3, 2) == "30") {
        $timeNumber += 0.5;
    }
    return $timeNumber;
}

// Fonction pour remplacer tous les accents d'une chaîne de caractère
function stripAccents($string){
	return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

// Fonction pour comparer 2 chaînes de caractères du prénom en enlevant les accents
function compareString($a, $b)
{
    return strcmp(stripAccents($a['prenom']), stripAccents($b['prenom']));
}

function lireDonnee($nomDonnee, $valDefaut = "") {
    if (isset($_GET[$nomDonnee])) {
        $val = $_GET[$nomDonnee];
    } elseif (isset($_POST[$nomDonnee])) {
        $val = $_POST[$nomDonnee];
        //echo "bonjour".$val;
    } else {
        $val = $valDefaut;
    }
    return $val;
}
