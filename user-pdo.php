<?php
class Userpdo {
    private $conn; // Database connection

    // Userpdo class constructor, initializes database connection with PDO
    public function __construct($servername, $username, $password, $dbname) {
        try {
            // Database connection with PDO
            $this->conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
            // Configure PDO to generate exceptions on error
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Handling connection errors
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    // How to register a new user in the database
    public function register($login, $password, $email, $firstname, $lastname) {
        try {
            // Preparing the SQL query to insert a new user
            $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (:login, :password, :email, :firstname, :lastname)");

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Linking parameters to the prepared query
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);

            // Executing the request
            return $stmt->execute();
        } catch (PDOException $e) {
            // Handling runtime errors
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }

    // How to check a user's login details
    public function login($login, $password) {
        try {
            // Prepare SQL query to select user by login
            $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE login = :login");
            $stmt->bindParam(':login', $login);
            $stmt->execute();

            // Retrieving query results
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // If a user is found and the password is correct
            if ($user && password_verify($password, $user['password'])) {
                return $user; // Returns user information
            }
            return null; // If the user does not exist or if the password is incorrect
        } catch (PDOException $e) {
            // Handling runtime errors
            die("Erreur lors de la connexion : " . $e->getMessage());
        }
    }

    // Method for obtaining user information by ID
    public function getUserById($id) {
        try {
            // Preparing an SQL query to select a user by ID
            $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Returns user information in associative array format
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handling runtime errors
            die("Erreur lors de la récupération des données : " . $e->getMessage());
        }
    }

    // How to update user information
    public function update($id, $login, $email, $firstname, $lastname) {
        try {
            // Preparing a SQL query to update a user
            $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = :login, email = :email, firstname = :firstname, lastname = :lastname WHERE id = :id");

            // Linking parameters to the prepared query
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':id', $id);

            // Executing the request
            return $stmt->execute();
        } catch (PDOException $e) {
            // Handling runtime errors
            die("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    // How to delete a user by ID
    public function delete($id) {
        try {
            // Preparing an SQL query to delete a user by ID
            $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = :id");
            $stmt->bindParam(':id', $id);

            // Executing the request
            return $stmt->execute();
        } catch (PDOException $e) {
            // Handling runtime errors
            die("Erreur lors de la suppression : " . $e->getMessage());
        }
    }

    // Class destructor, to free up resources
    public function __destruct() {
        $this->conn = null; // Closes database connection
    }
}
