<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
</head>

<body>
    <h1>Gestion des utilisateurs</h1>

    <?php
    class User
    {
        private $id;
        public $login;
        public $email;
        public $firstname;
        public $lastname;
        private $mysqli;

        public function __construct()
        {
            // Connexion à la base de données
            $this->mysqli = new mysqli('localhost', 'root', '', 'classes');
            if ($this->mysqli->connect_error) {
                die('Erreur de connexion (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
            }
        }

        public function register($login, $password, $email, $firstname, $lastname)
        {
            // Utilisation d'une variable temporaire pour le mot de passe haché
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($hashedPassword === false) {
                die("Password hashing failed.");
            }

            $stmt = $this->mysqli->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
            if ($stmt === false) {
                return null; // Retourner null en cas d'échec de la préparation
            }

            $stmt->bind_param("sssss", $login, $hashedPassword, $email, $firstname, $lastname);
            if (!$stmt->execute()) {
                $stmt->close();
                return null; // Retourner null en cas d'échec de l'exécution
            }

            // Fermer la requête
            $stmt->close();

            // Récupérer l'id inséré
            $this->id = $this->mysqli->insert_id;
            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;

            return $this->getAllInfos();
        }

        public function connect($login, $password)
        {
            $stmt = $this->mysqli->prepare("SELECT * FROM utilisateurs WHERE login = ?");
            if ($stmt === false) {
                die('Erreur de préparation de la requête');
            }

            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                $this->id = $user['id'];
                $this->login = $user['login'];
                $this->email = $user['email'];
                $this->firstname = $user['firstname'];
                $this->lastname = $user['lastname'];
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                return false;
            }
        }

        public function disconnect()
        {
            $this->id = null;
            $this->login = null;
            $this->email = null;
            $this->firstname = null;
            $this->lastname = null;
        }

        public function delete()
        {
            if ($this->id !== null) {
                $stmt = $this->mysqli->prepare("DELETE FROM utilisateurs WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $this->id);
                    $stmt->execute();
                    $stmt->close();
                }
                $this->disconnect();
            }
        }

        public function update($login, $password, $email, $firstname, $lastname)
        {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->mysqli->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("sssssi", $login, $hashedPassword, $email, $firstname, $lastname, $this->id);
                $stmt->execute();
                $stmt->close();
            }

            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
        }

        public function isConnected()
        {
            return $this->id !== null;
        }

        public function getAllInfos()
        {
            return [
                'id' => $this->id,
                'login' => $this->login,
                'email' => $this->email,
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
            ];
        }

        public function getLogin()
        {
            return $this->login;
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function getFirstname()
        {
            return $this->firstname;
        }

        public function getLastname()
        {
            return $this->lastname;
        }
    }

    $user = new User();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'];

        switch ($action) {
            case 'register':
                $userInfo = $user->register($_POST['login'], $_POST['password'], $_POST['email'], $_POST['firstname'], $_POST['lastname']);
                if ($userInfo !== null) {
                    echo "<h2>Utilisateur créé :</h2>";
                    echo "<pre>";
                    print_r($userInfo);
                    echo "</pre>";
                } else {
                    echo "<h2> L'enregistrement a échoué.</h2>";
                }
                break;

            case 'login':
                if ($user->connect($_POST['login'], $_POST['password'])) {
                    echo "<h2>Connexion réussie ! </h2>";
                    echo "<pre>";
                    print_r($user->getAllInfos());
                    echo "</pre>";
                } else {
                    echo "<h2>Échec de la connexion.</h2>";
                }
                break;

            case 'disconnect':
                $user->disconnect();
                echo "<h2>Déconnexion réussie.</h2>";
                break;

            case 'update':
                $user->update($_POST['login'], $_POST['password'], $_POST['email'], $_POST['firstname'], $_POST['lastname']);
                echo "<h2>Informations mises à jour :</h2>";
                echo "<pre>";
                print_r($user->getAllInfos());
                echo "</pre>";
                break;

            case 'delete':
                $user->delete();
                echo "<h2>Utilisateur supprimé.</h2>";
                break;
        }
    }
    ?>

    <h2>Inscription</h2>
    <form method="post">
        <input type="hidden" name="action" value="register">
        <label>Login: <input type="text" name="login" required></label><br>
        <label>Mot de passe: <input type="password" name="password" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Prénom: <input type="text" name="firstname" required></label><br>
        <label>Nom: <input type="text" name="lastname" required></label><br>
        <button type="submit">S'inscrire</button>
    </form>

    <h2>Connexion</h2>
    <form method="post">
        <input type="hidden" name="action" value="login">
        <label>Login: <input type="text" name="login" required></label><br>
        <label>Mot de passe: <input type="password" name="password" required></label><br>
        <button type="submit">Se connecter</button>
    </form>

    <h2>Déconnexion</h2>
    <form method="post">
        <input type="hidden" name="action" value="disconnect">
        <button type="submit">Se déconnecter</button>
    </form>

    <h2>Mettre à jour les informations</h2>
    <form method="post">
        <input type="hidden" name="action" value="update">
        <label>Nouveau login: <input type="text" name="login" required></label><br>
        <label>Nouveau mot de passe: <input type="password" name="password" required></label><br>
        <label>Nouvel email: <input type="email" name="email" required></label><br>
        <label>Nouveau prénom: <input type="text" name="firstname" required></label><br>
        <label>Nouveau nom: <input type="text" name="lastname" required></label><br>
        <button type="submit">Mettre à jour</button>
    </form>

    <h2>Supprimer l'utilisateur</h2>
    <form method="post">
        <input type="hidden" name="action" value="delete">
        <button type="submit">Supprimer</button>
    </form>

</body>

</html>