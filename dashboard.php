<?php
require_once 'auth.php';
require_once 'User.php';

redirectIfNotLoggedIn();

$user = new User("localhost", "root", "", "classes");
$user_data = $user->getUserById($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        $login = $_POST['login'];
        $email = $_POST['email'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];

        if ($user->update($user_data['id'], $login, $email, $firstname, $lastname)) {
            $success = "Mise à jour réussie.";
            $user_data = $user->getUserById($user_data['id']);
        } else {
            $error = "Erreur lors de la mise à jour.";
        }
    } elseif (isset($_POST['delete'])) {
        if ($user->delete($user_data['id'])) {
            logout();
        } else {
            $error = "Erreur lors de la suppression.";
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