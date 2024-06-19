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
        $query = "INSERT INTO user (username, email, password, name, firstname, country) VALUES (:username, :email, :password, :name, :firstname, :country)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":username" => $username, ":email" => $email, ":password" => $password, ":name" => $name, ":firstname" => $firstname, ":country" => $country]);
    }

    public function read(){
        $sql = 'SELECT * FROM user';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByName($username){
        $sql = 'SELECT * FROM user WHERE username = :username';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetchColumn();
    }
}

?>