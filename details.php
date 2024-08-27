<?php
// On démarre une session
session_start();

// Est-ce que l'id existe et il n'est pas vide dans l'URL
if(isset($_GET["id"]) && !empty($_GET['id'])) {

} else {
    header('Location : index.php');
}