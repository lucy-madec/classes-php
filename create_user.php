<?php
// Inclure la classe User
require_once 'User.php';

// Connexion à la base de données
$user = new User("localhost", "root", "", "classes");

// Créer un nouvel utilisateur
if ($user->create("john_doe", "password123", "john@example.com", "John", "Doe")) {
    // Afficher un message de succès
    echo "<p style='color: green;'>Utilisateur ajouté à la base de données avec succès.</p>";

    // Lire l'utilisateur ajouté
    $last_id = $user->getLastInsertedId();
    $user_info = $user->read($last_id);

    // Afficher les informations de l'utilisateur
    echo "<p>ID: " . $user_info['id'] . "</p>";
    echo "<p>Login: " . $user_info['login'] . "</p>";
    echo "<p>Email: " . $user_info['email'] . "</p>";
    echo "<p>Prénom: " . $user_info['firstname'] . "</p>";
    echo "<p>Nom: " . $user_info['lastname'] . "</p>";
} else {
    // Afficher un message d'échec
    echo "<p style='color: red;'>Échec de l'ajout de l'utilisateur.</p>";
}
