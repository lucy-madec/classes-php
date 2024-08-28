<?php
require_once 'auth.php'; // Inclusion des fonctions d'authentification
require_once 'User.php'; // Inclusion de la classe User

redirectIfNotLoggedIn(); // Redirection si l'utilisateur n'est pas connecté

$user = new User("localhost", "root", "", "classes"); // Création d'un objet User
$user_data = $user->getUserById($_SESSION['user_id']); // Récupération des informations de l'utilisateur connecté

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Vérifie si le formulaire a été soumis
    if (isset($_POST['update'])) { // Si le formulaire de mise à jour a été soumis
        $login = $_POST['login'];
        $email = $_POST['email'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];

        // Appel de la méthode update pour mettre à jour les informations de l'utilisateur
        if ($user->update($user_data['id'], $login, $email, $firstname, $lastname)) {
            $success = "Mise à jour réussie."; // Message de succès
            $user_data = $user->getUserById($user_data['id']); // Récupération des données mises à jour
        } else {
            $error = "Erreur lors de la mise à jour."; // Message d'erreur
        }
    } elseif (isset($_POST['delete'])) { // Si le formulaire de suppression a été soumis
        if ($user->delete($user_data['id'])) {
            logout(); // Déconnexion après suppression du compte
        } else {
            $error = "Erreur lors de la suppression."; // Message d'erreur
        }
    }
} ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <h2 class="my-4">Bienvenue, <?= htmlspecialchars($user_data['firstname']) ?></h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" class="form-control" id="login" name="login"
                    value="<?= htmlspecialchars($user_data['login']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="<?= htmlspecialchars($user_data['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="firstname">Prénom</label>
                <input type="text" class="form-control" id="firstname" name="firstname"
                    value="<?= htmlspecialchars($user_data['firstname']) ?>" required>
            </div>
            <div class="form-group">
                <label for="lastname">Nom</label>
                <input type="text" class="form-control" id="lastname" name="lastname"
                    value="<?= htmlspecialchars($user_data['lastname']) ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-primary">Mettre à jour</button>
            <button type="submit" name="delete" class="btn btn-danger"
                onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?');">Supprimer le
                compte</button>
        </form>
        <a href="logout.php" class="btn btn-secondary mt-3">Déconnexion</a>
    </div>
</body>

</html>