<?php
// On démarre une session
session_start();

// Inclusion de la connexion à la base
require_once('connect.php');

$sql = 'SELECT * FROM utilisateurs';

// Préparation de la requête
$query = $db->prepare($sql);

// Exécution de la requête
$query->execute();

// Stockage du résultat dans un tableau associatif
$result = $query->fetchAll(PDO::FETCH_ASSOC);

require_once('close.php');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des classes</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <?php
                    if(!empty($_SESSION['erreur'])) {
                        echo '<div class="alert alert-danger" role="alert">
                                '. $_SESSION['erreur'].'
                            </div>';
                        $_SESSION['erreur'] = '';
                    }
                ?>
                <h1>Gestion des classes</h1>
                <table class="table">
                    <thead>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Détails</th>
                    </thead>
                    <tbody>
                        <?php
                        // On boucle sur la variable result
                        foreach ($result as $login) {
                            ?>
                            <tr>
                                <td><?= $login['id'] ?></td>
                                <td><?= $login['login'] ?></td>
                                <td><a href="details.php?id=<?= $login['id'] ?>">Voir</a></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <a href="create.php" class="btn btn-primary">Créer un nouvel utilisateur</a>
            </section>
        </div>
    </main>
</body>

</html>