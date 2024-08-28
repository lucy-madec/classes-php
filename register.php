<?php
require_once 'User.php'; // Inclusion of the User class

$user = new User("localhost", "root", "", "classes"); // Creating a User object

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Checks if the form has been submitted
    $login = $_POST['login']; // Retrieving form data
    $password = $_POST['password'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    // Call register method to register user
    if ($user->register($login, $password, $email, $firstname, $lastname)) {
        header("Location: login.php");
        exit(); // Redirect to login page on success
    } else {
        $error = "Erreur lors de l'inscription."; // Error message displayed in case of failure
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="my-4">Inscription</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" class="form-control" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="firstname">Prénom</label>
                <input type="text" class="form-control" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="lastname">Nom</label>
                <input type="text" class="form-control" id="lastname" name="lastname" required>
            </div>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
            <p class="mt-3">Déjà inscrit ? <a href="login.php">Connexion</a></p>
        </form>
    </div>
</body>
</html>
