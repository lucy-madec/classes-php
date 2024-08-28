<?php
session_start(); // Démarre une session pour suivre les utilisateurs connectés

// Vérifie si un utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirige l'utilisateur vers la page de connexion s'il n'est pas connecté
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Déconnecte l'utilisateur en détruisant la session
function logout() {
    session_unset(); // Supprime toutes les variables de session
    session_destroy(); // Détruit la session en cours
    header("Location: login.php");
    exit();
}
