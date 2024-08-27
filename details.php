<?php
// On démarre une session
session_start();

// Est-ce que l'id existe et il n'est pas vide dans l'URL
if(isset($_GET["id"]) && !empty($_GET['id'])) {
    require_once('connect.php');

    // On nettoie l'id envoyé
    $id = strip_tags($_GET['id']);

    $sql = 'SELECT * FROM `utilisateurs` WHERE `id` = :id;';

    // On prépare la requête
    $query = $db->prepare( $sql);

    // On "accroche" le paramètre (id)
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On récupère le login
    $login = $query->fetch();

    // On vérifie si le login existe
    if(!$login) {
        $_SESSION['erreur'] = "Cet id n'existe pas";
        header('Location: index.php');
    }
} else {
    $_SESSION['erreur'] = "URL invalide";
    header('Location : index.php');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails utilisateur</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <h1>Détails de l'utilisateur <?= $login['login'] ?></h1>
                <p>ID : <?= $login['id'] ?></p>
                <p>Login : <?= $login['login'] ?></p>
                <p>Email : <?= $login['email'] ?></p>
                <p>Prénom : <?= $login['firstname'] ?></p>
                <p>Nom : <?= $login['lastname'] ?></p>
                <p><a href="index.php">Retour</a> <a href="edit.php?id=<?= $login['id'] ?>">Modifier</a></p>

            </section>
        </div>
    </main>
</body>
</html>