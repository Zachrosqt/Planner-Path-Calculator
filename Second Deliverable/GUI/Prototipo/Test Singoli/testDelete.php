<?php

require "db.php";

$startTime = time();

$database = new Db();

$nomeAlbero = "Test";

$connection = $database->connect();
        
        if (!$connection){
            echo "errore connessione";

        }

$albero = $database -> select("SELECT * FROM Albero WHERE Nome = '$nomeAlbero'");
        
        if (!$albero || $albero[0]['usabile'] == 0){
            return 2;
        }
        
        $result = $database->query("DELETE FROM nodes WHERE AlberoID = " . $albero[0]['AlberoID'] . "");
        $result1 = $database->query("DELETE FROM attrNodes WHERE AlberoID = " . $albero[0]['AlberoID'] . "");
        $result2 = $database->query("DELETE FROM attrEdges WHERE AlberoID = " . $albero[0]['AlberoID'] . "");
        $result3 = $database->query("DELETE FROM Albero WHERE AlberoID = " . $albero[0]['AlberoID'] . "");
        
        if($result==1 && $result1==1 && $result2==1 && $result3==1){
            echo "albero eliminato";
        } else {

            return "problema in fase di eliminazione";
        }

?>