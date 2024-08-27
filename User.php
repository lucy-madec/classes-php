<?php
class User {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $mysqli;

    public function __construct() {
        // Connexion à la base de données
        $this->mysqli = new mysqli('localhost', 'root', '', 'classes');
        if ($this->mysqli->connect_error) {
            die('Erreur de connexion (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        }
    }

    public function register($login, $password, $email, $firstname, $lastname) {
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

    public function connect($login, $password) {
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

    public function disconnect() {
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
    }

    public function delete() {
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

    public function update($login, $password, $email, $firstname, $lastname) {
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

    public function isConnected() {
        return $this->id !== null;
    }

    public function getAllInfos() {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
        ];
    }

    public function getLogin() {
        return $this->login;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function getLastname() {
        return $this->lastname;
    }
}

// Exemple d'utilisation pour tester
$user = new User();
$userInfo = $user->register("Tom13", "azerty", "thomas@gmail.com", "Thomas", "DUPONT");

if ($userInfo !== null) {
    echo "<pre>";
    print_r($userInfo);
    echo "</pre>";
} else {
    echo "L'enregistrement a échoué.";
}

if ($user->connect("Tom13", "azerty")) {
    echo "Connexion réussie !";
} else {
    echo "Échec de la connexion.";
}