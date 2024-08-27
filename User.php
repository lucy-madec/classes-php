<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class User {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $mysqli;

    // Constructeur pour initialiser la connexion MySQLi
    public function __construct() {
        // Remplacez 'root' et '' par vos identifiants MySQL si nécessaire
        $this->mysqli = new mysqli('localhost', 'root', '', 'classes'); 
        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    // Méthode pour enregistrer un utilisateur
    public function register($login, $password, $email, $firstname, $lastname) {
        // Utilisation d'une variable temporaire pour le mot de passe haché
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if ($hashedPassword === false) {
            die("Password hashing failed.");
        }

        $stmt = $this->mysqli->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . $this->mysqli->error);
        }

        $stmt->bind_param("sssss", $login, $hashedPassword, $email, $firstname, $lastname);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        $stmt->close();

        // Récupérer l'id inséré
        $this->id = $this->mysqli->insert_id;
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        return $this->getAllInfos();
    }

    // Méthode pour connecter un utilisateur
    public function connect($login, $password) {
        $stmt = $this->mysqli->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $this->mysqli->error);
        }
        $stmt->bind_param("s", $login);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->login = $user['login'];
            $this->email = $user['email'];
            $this->firstname = $user['firstname'];
            $this->lastname = $user['lastname'];
            return true;
        } else {
            return false;
        }
    }

    // Méthode pour déconnecter l'utilisateur
    public function disconnect() {
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
    }

    // Méthode pour supprimer l'utilisateur
    public function delete() {
        if ($this->id) {
            $stmt = $this->mysqli->prepare("DELETE FROM utilisateurs WHERE id = ?");
            if ($stmt === false) {
                die("Prepare failed: " . $this->mysqli->error);
            }
            $stmt->bind_param("i", $this->id);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
            $stmt->close();
            $this->disconnect();
        }
    }

    // Méthode pour mettre à jour les informations de l'utilisateur
    public function update($login, $password, $email, $firstname, $lastname) {
        if ($this->id) {
            // Utilisation d'une variable temporaire pour le mot de passe haché
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($hashedPassword === false) {
                die("Password hashing failed.");
            }

            $stmt = $this->mysqli->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
            if ($stmt === false) {
                die("Prepare failed: " . $this->mysqli->error);
            }
            $stmt->bind_param("sssssi", $login, $hashedPassword, $email, $firstname, $lastname, $this->id);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
            $stmt->close();

            $this->login = $login;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
        }
    }

    // Méthode pour vérifier si un utilisateur est connecté
    public function isConnected() {
        return !empty($this->id);
    }

    // Méthode pour obtenir toutes les informations de l'utilisateur
    public function getAllInfos() {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname
        ];
    }

    // Méthode pour obtenir le login
    public function getLogin() {
        return $this->login;
    }

    // Méthode pour obtenir l'email
    public function getEmail() {
        return $this->email;
    }

    // Méthode pour obtenir le prénom
    public function getFirstname() {
        return $this->firstname;
    }

    // Méthode pour obtenir le nom de famille
    public function getLastname() {
        return $this->lastname;
    }
}

// Exemple d'utilisation pour tester
$user = new User();
$userInfo = $user->register("Tom13", "azerty", "thomas@gmail.com", "Thomas", "DUPONT");

echo "<pre>";
print_r($userInfo);
echo "</pre>";

// Teste la connexion :
if ($user->connect("Tom13", "azerty")) {
    echo "Connexion réussie !<br>";
    print_r($user->getAllInfos());
} else {
    echo "Échec de la connexion.<br>";
}