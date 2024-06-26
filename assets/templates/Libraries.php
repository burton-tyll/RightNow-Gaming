<?php

    class Libraries{
        function secure($value){
            return htmlspecialchars(htmlentities(trim($value)));
        }
    }

?>

