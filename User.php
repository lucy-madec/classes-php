<?php
class User {
    private $conn; // Database connection

    // User class constructor, initializes database connection
    public function __construct($servername, $username, $password, $dbname) {
        // Creating a new mysqli connection
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        
        // Connection check
        if ($this->conn->connect_error) {
            die("Connexion échouée: " . $this->conn->connect_error);
        }
    }

    // How to register a new user in the database
    public function register($login, $password, $email, $firstname, $lastname) {
        // Preparing the SQL query to insert a new user
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Erreur de préparation : " . $this->conn->error);
        }

        // Password hashing for secure storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Linking parameters to the prepared query
        $stmt->bind_param("sssss", $login, $hashed_password, $email, $firstname, $lastname);

        // Executing the query and returning the result
        return $stmt->execute();
    }

    // How to check a user's login details
    public function login($login, $password) {
        // Prepare SQL query to select user by login
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();

        // Retrieving query results
        $result = $stmt->get_result();

        // If a user is found
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Password verification
            if (password_verify($password, $user['password'])) {
                return $user; // Returns user information
            }
        }
        return null; // If user does not exist or incorrect password
    }

    // Method for obtaining user information by ID
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc(); // Returns user information
    }

    // How to update user information
    public function update($id, $login, $email, $firstname, $lastname) {
        $stmt = $this->conn->prepare("UPDATE utilisateurs SET login = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $login, $email, $firstname, $lastname, $id);
        return $stmt->execute(); // Returns true if the update was successful
    }

    // How to delete a user by ID
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute(); // Returns true if deletion was successful
    }

    // Class destructor, closes database connection
    public function __destruct() {
        $this->conn->close();
    }
}
