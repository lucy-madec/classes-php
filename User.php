<?php
class User
{
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
        }
    }

    // Méthode pour créer un nouvel utilisateur
    public function create($login, $password, $email, $firstname, $lastname)
    {
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Erreur de préparation : " . $this->conn->error);
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sssss", $login, $hashed_password, $email, $firstname, $lastname);

        if ($stmt->execute()) {
            return true;
        } else {
            die("Erreur d'exécution : " . $stmt->error);
        }
    }

    // Méthode pour récupérer l'ID du dernier enregistrement inséré
    public function getLastInsertedId()
    {
        return $this->conn->insert_id;
    }

    // Méthode pour lire un utilisateur
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

    // Méthode pour fermer la connexion
    public function __destruct()
    {
        $this->conn->close();
    }
}
