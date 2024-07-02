<?php

    require_once(__DIR__ . '/../../Database.php');

    class Game_platform extends Database{
                //Définition du constructeur
                public function __construct() {
                    // Appelle le constructeur de la classe parente pour initialiser la connexion
                    parent::__construct();
                    $this->conn = $this->connect();
                }

                public function getAllPlatforms() {
                    $query = 'SELECT * FROM platform';
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute();
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                public function addGameToPlatform($id_game, $id_platform) {
                    $query = 'INSERT INTO game_platform (id_game, id_platform) VALUES (:id_game, :id_platform)';
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute([
                        ':id_game' => $id_game,
                        ':id_platform' => $id_platform
                    ]);

                    // return(true);
                }
                
                public function getAllGamesByPlateform($id_platform){
                    $query = 'SELECT id_game FROM game_platform WHERE id_platform = :id_platform';
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute(['id_platform' => $id_platform]);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
        
                public function getAllGamesByPlatformOrderedByDate($id_platform) {
                    $query = 'SELECT * FROM game JOIN game_platform ON game.id = game_platform.id_game WHERE game_platform.id_platform = :id_platform ORDER BY game.release_date DESC';
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute(['id_platform' => $id_platform]);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
        
                public function getAllGamesByPlatformOrderedByRate($id_platform) {
                    $query = 'SELECT * FROM game JOIN game_platform ON game.id = game_platform.id_game WHERE game_platform.id_platform = :id_platform ORDER BY game.rate DESC';
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute(['id_platform' => $id_platform]);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
        
                public function getTheBestGameByPlateform($id_platform){
                    $query = 'SELECT * FROM game JOIN game_platform ON game.id = game_platform.id_game WHERE game_platform.id_platform = :id_platform ORDER BY game.rate DESC LIMIT 1';
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute(['id_platform' => $id_platform]);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                public function getGamePlateform($id_game){
                    $query = 'SELECT name FROM platform JOIN game_platform ON platform.id = game_platform.id_platform WHERE game_platform.id_game = :id_game';
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute(['id_game' => $id_game]);
                    $result = $stmt->fetchAll(PDO::FETCH_COLUMN); // Utiliser fetchAll pour obtenir un tableau de noms
                    return $result;
                }
                
                
    }

?>