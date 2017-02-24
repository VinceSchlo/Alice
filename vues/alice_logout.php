<?php

session_start(); // Utilisation des variables $_SESSION
?>

<?php

if (!empty($_SESSION)) { // S'il existe des variables $_SESSION, on les détruit
    session_destroy(); // 
}
// On retourne à la page Index
header('Location: ../index.php');
?>