<?php 
    require_once(__DIR__ . '/../../Databe.php')
    class Cart extends Database {
        //Définition du constructeur
        public function __construct() {
            // Appelle le constructeur de la classe parente pour initialiser la connexion
            parent::__construct();
            $this->conn = $this->connect();
        }
    }

