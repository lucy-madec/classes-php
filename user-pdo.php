<?php
class Userpdo {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $pdo;

    // Constructeur pour initialiser la connexion PDO
    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=classes';
        $username = 'username';
        $password = 'password';
        $this->pdo = new PDO($dsn, $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Méthode pour enregistrer un utilisateur
    public function register($login, $password, $email, $firstname, $lastname) {
        $stmt = $this->pdo->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$login, password_hash($password, PASSWORD_DEFAULT), $email, $firstname, $lastname]);

        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;

        return $this->getAllInfos();
    }

    // Méthode pour connecter un utilisateur
    public function connect($login, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

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
            $stmt = $this->pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([$this->id]);
            $this->disconnect();
        }
    }

    // Méthode pour mettre à jour les informations de l'utilisateur
    public function update($login, $password, $email, $firstname, $lastname) {
        if ($this->id) {
            $stmt = $this->pdo->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
            $stmt->execute([$login, password_hash($password, PASSWORD_DEFAULT), $email, $firstname, $lastname, $this->id]);

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
