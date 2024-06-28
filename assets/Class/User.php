<?php

require_once "../../Database.php";
require_once "Libraries.php";

class User extends Database {
    //Définition du constructeur
    private $libraries;
    public function __construct() {
        // Appelle le constructeur de la classe parente pour initialiser la connexion
        parent::__construct();
        $this->conn = $this->connect();
        $this->libraries = new Libraries();
    }

    /*---------*/
    /*----------CREATE--------*/
    /*---------*/

    public function addUser($username, $email, $password, $name, $firstname, $country){
        $username = $this->libraries->secure($username);
        $email = $this->libraries->secure($email);
        $password = $this->libraries->secure($password);
        $name = $this->libraries->secure($name);
        $firstname = $this->libraries->secure($firstname);
        $country = $this->libraries->secure($country);
        $admin = 0; //Equivaut à un utilisateur lambda

        // Préparer la requête d'insertion avec des paramètres de substitution
        $query = "INSERT INTO user (username, email, password, name, first_name, country, admin) VALUES (:username, :email, :password, :name, :firstname, :country, :admin)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':admin', $admin);

        $stmt->execute();
    }

    /*---------*/
    /*----------READ--------*/
    /*---------*/

    public function getUser($by, $byvalue) {
        $query = "SELECT * FROM user WHERE $by = :byvalue";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':byvalue', $byvalue);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result; 
    }

    public function read(){
        $sql = 'SELECT * FROM user';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*---------*/
    /*----------UPDATE--------*/
    /*---------*/

    public function upgradeToAdmin($username){
        $query = "UPDATE user SET admin = 1 WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':username' => $username]);
    }

    /*---------*/
    /*----------DELETE--------*/
    /*---------*/

    public function deleteUser($username){
        $query = "DELETE FROM user WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':username' => $username]);
    }
    
}

?>