<?php

require_once "../../Database.php";

class User extends Database {
    //Définition du constructeur
    public function __construct() {
        // Appelle le constructeur de la classe parente pour initialiser la connexion
        parent::__construct();
        $this->conn = $this->connect();
    }

    public function addUser($username, $email, $password, $name, $firstname, $country){
        // Préparer la requête d'insertion avec des paramètres de substitution
        $query = "INSERT INTO user (username, email, password, name, first_name, country) VALUES (:username, :email, :password, :name, :firstname, :country)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":username" => $username, ":email" => $email, ":password" => $password, ":name" => $name, ":firstname" => $firstname, ":country" => $country]);
    }

    public function getUser($by, $byvalue) {
        // Prépare la requête avec le paramètre nommé :by
        $query = "SELECT * FROM user WHERE $by = :byvalue";
        $stmt = $this->conn->prepare($query);
        
        // Lie la valeur du paramètre nommé :byvalue
        $stmt->bindValue(':byvalue', $byvalue);
    
        // Exécute la requête préparée
        $stmt->execute();
    
        // Récupère la première ligne de résultat
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result; // Retourne le résultat sous forme de tableau associatif
    }

    public function read(){
        $sql = 'SELECT * FROM user';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUser($username){
        $query = "DELETE * FROM user WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }
}

?>