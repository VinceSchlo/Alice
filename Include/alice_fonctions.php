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

// Fonction pour retourner la date exacte des jours du dimanche au samedi en fonction de la semaine et de l'année
function datesJourSemaine($week, $year)
{
	$tabDatesJoursSemaines = array();
	$firstDayInYear=date("N",mktime(0,0,0,1,1,$year));
	if ($firstDayInYear<5)
		$shift=-($firstDayInYear-1)*86400;
	else
		$shift=(8-$firstDayInYear)*86400;
	if ($week>1) $weekInSeconds=($week-1)*604800; else $weekInSeconds=0;
	for ($i=0; $i<=6; $i++) {
		$timestamp = mktime(0,0,0,1,$i,$year)+$weekInSeconds+$shift;
		$tabDatesJoursSemaines[$i] = date("Y-m-d",$timestamp);
	}
	
    return $tabDatesJoursSemaines;
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