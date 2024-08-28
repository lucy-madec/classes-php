<?php
class User {
    private $conn; // Connexion à la base de données

    // Constructeur de la classe User, initialise la connexion à la base de données
    public function __construct($servername, $username, $password, $dbname) {
        // Création d'une nouvelle connexion mysqli
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        
        // Vérification de la connexion
        if ($this->conn->connect_error) {
            die("Connexion échouée: " . $this->conn->connect_error);
        }
    }

    // Méthode pour enregistrer un nouvel utilisateur dans la base de données
    public function register($login, $password, $email, $firstname, $lastname) {
        // Préparation de la requête SQL pour insérer un nouvel utilisateur
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Erreur de préparation : " . $this->conn->error);
        }

        // Hachage du mot de passe pour sécuriser le stockage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Liaison des paramètres à la requête préparée
        $stmt->bind_param("sssss", $login, $hashed_password, $email, $firstname, $lastname);

        // Exécution de la requête et retour du résultat
        return $stmt->execute();
    }

    // Méthode pour vérifier les informations de connexion d'un utilisateur
    public function login($login, $password) {
        // Préparation de la requête SQL pour sélectionner l'utilisateur par son login
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();

        // Récupération du résultat de la requête
        $result = $stmt->get_result();

        // Si un utilisatur est trouvé
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Vérification du mot de passe
            if (password_verify($password, $user['password'])) {
                return $user; // Retourne les informations de l'utilisateur
            }
        }
        return null; // Si l'utilisateur n'existe pas ou mot de passe incorrect
    }

    // Méthode pour obtenir les informations d'un utilisateur par son ID
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc(); // Retourne les informations de l'utilisateur
    }

    // Méthode pour mettre à jour les informations d'un utilisateur
    public function update($id, $login, $email, $firstname, $lastname) {
        $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $login, $email, $firstname, $lastname, $id);
        return $stmt->execute(); // Retourne vrai si la mise à jour a réussi
    }

    // Méthode pour supprimer un utilisateur par son ID
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute(); // Retourne vrai si la suppression a réussi
    }

    // Destructeur de la classe, ferme la connexion à la base de données
    public function __destruct() {
        $this->conn->close();
    }
}
