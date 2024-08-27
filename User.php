<?php
class User
{
    // Attributs de la classe
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $conn;

    // Constructeur pour initialiser la connexion à la base de données
    public function __construct($servername, $username, $password, $dbname)
    {
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connexion échouée: " . $this->conn->connect_error);
        } else {
            echo "Connexion réussie<br>";
        }
    }


    // Méthode pour créer un nouvel utilisateur (Create)
    public function create($login, $password, $email, $firstname, $lastname)
    {
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Erreur de préparation : " . $this->conn->error);
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sssss", $login, $hashed_password, $email, $firstname, $lastname);

        if ($stmt->execute()) {
            echo "Utilisateur créé avec succès<br>";
            return true;
        } else {
            die("Erreur d'exécution : " . $stmt->error);
        }
    }


    // Méthode pour lire un utilisateur (Read)
    public function read($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    // Méthode pour mettre à jour un utilisateur (Update)
    public function update($id, $login, $email, $firstname, $lastname)
    {
        $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $login, $email, $firstname, $lastname, $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Méthode pour supprimer un utilisateur (Delete)
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Méthode pour fermer la connexion à la base de données
    public function __destruct()
    {
        $this->conn->close();
    }
}

// Exemple d'utilisation de la classe User
require_once 'User.php';

// Connexion à la base de données
$user = new User("localhost", "root", "", "classes");

// Créer un nouvel utilisateur
if ($user->create("john_doe", "password123", "john@example.com", "John", "Doe")) {
    echo "<p style='color: green;'>Utilisateur ajouté à la base de données avec succès.</p>";

    // Lire l'utilisateur ajouté (en supposant que c'est le dernier ajouté)
    // Utilisez mysqli_insert_id pour obtenir l'ID du dernier enregistrement ajouté
    $last_id = $user->conn->insert_id;
    $user_info = $user->read($last_id);

    // Afficher les informations de l'utilisateur
    echo "Les informations de l'utilisateur ajouté : <br>";
    echo "ID: " . $user_info['id'] . "<br>";
    echo "Login: " . $user_info['login'] . "<br>";
    echo "Email: " . $user_info['email'] . "<br>";
    echo "Prénom: " . $user_info['firstname'] . "<br>";
    echo "Nom: " . $user_info['lastname'] . "<br>";
} else {
    echo "Échec de l'ajout de l'utilisateur<br>";
}