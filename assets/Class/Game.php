<?php
    require_once('../../Database.php');

    class Game extends Database {
        //Définition du constructeur
        public function __construct() {
            // Appelle le constructeur de la classe parente pour initialiser la connexion
            parent::__construct();
            $this->conn = $this->connect();
        }

    }
?>