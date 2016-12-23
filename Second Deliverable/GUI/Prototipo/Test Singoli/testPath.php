<?php

/* Path Test 1

$trovato = false;

$childId = 4; 
$parentId = 0;

$nodi="";

$rows = $database -> select("SELECT PadreID, NodoID FROM Nodes WHERE NodoID = $childId");

if (!$rows){
    echo "Errore";
    die;
} else {
    $nodi = $nodi . $rows[0]['NodoID'] . "<br>"; 
    $parent = $rows[0]['PadreID'];
}

while(!$trovato){
    
    $rows = $database -> select("SELECT PadreID, NodoID FROM Nodes WHERE NodoID = $parent");
    
    if (!$rows){
        echo "Errore 1";
        die;
    } else {
        $nodi = $nodi . $rows[0]['NodoID'] . "<br>"; 
        $parent = $rows[0]['PadreID'];
        
        if($rows[0]['NodoID'] == $parentId){
            $trovato = true;
        }
    }
    
}

echo $nodi;*/


require "db.php";

$startTime = time();

$database = new Db();

$nomeAlbero = "Test";
$childId = 5;
$parentId = 1;

$connection = $database->connect();
        
        if (!$connection){
            echo "errore connessione";

        }
        
        $albero = $database -> select("SELECT * FROM Albero WHERE Nome = '$nomeAlbero'");
        
        if (!$albero || $albero[0]['usabile'] == 0){
            echo "errore albero inesistente";
        }
        
        $trovato = false;
        $sommaNodi=0;
        $sommaArchi=0;

        $rows = $database -> select("SELECT NodoID, attr FROM attrNodes WHERE NodoID = $childId AND AlberoID = " . $albero[0]['AlberoID'] . "");

        if (!$rows){
            echo "errore connessione";
        }
        
        echo "<br>Nodo: " . $rows[0]['NodoID'] . " Attributo:" . $rows[0]['attr'];
        $sommaNodi = $sommaNodi + $rows[0]['attr']; 
        
        if($rows[0]['NodoID'] == $parentId){
                $totale = array ($sommaNodi);
                echo "<br>Somma nodi: " . $totale[0];
                $trovato = true;
        } else {
            $rows1 = $database -> select("SELECT PadreID, attr FROM attrEdges WHERE NodoID = $childId AND AlberoID = " . $albero[0]['AlberoID'] . "");
            
            if (!$rows1){
                echo "errore connessione";
            }
            
            echo "<br>Arco: " . $rows[0]['NodoID'] . "-" . $rows1[0]['PadreID'] . " Attributo:" . $rows1[0]['attr'];
            
            $sommaArchi = $sommaArchi + $rows1[0]['attr'];
            $parent = $rows1[0]['PadreID'];
        }

        while(!$trovato){

            $rows = $database -> select("SELECT NodoID, attr FROM attrNodes WHERE NodoID = $parent AND AlberoID = " . $albero[0]['AlberoID'] . "");

            if (!$rows){
                echo "errore connessione";
            }
             
            echo "<br>Nodo: " . $rows[0]['NodoID'] . " Attributo:" . $rows[0]['attr'];
            $sommaNodi = $sommaNodi + $rows[0]['attr']; 
            
            if($rows[0]['NodoID'] == $parentId){
                $totale = array ($sommaNodi, $sommaArchi);
                echo "<br>Somma nodi: " . $totale[0] . "<br>Somma Archi: " . $totale[1];
                $trovato = true;
                
            } else if ($rows[0]['NodoID'] < $parentId){
                echo "<br>path inesistente";
                $trovato = true;
                
            } else if ($rows[0]['NodoID']==0){
                echo "<br>path inesistente";
                $trovato = true;
                
            } else {
                $rows1 = $database -> select("SELECT PadreID, attr FROM attrEdges WHERE NodoID = $parent AND AlberoID = " . $albero[0]['AlberoID'] . "");

                if (!$rows1){
                    echo "errore connessione";
                }
                
                echo "<br>Arco: " . $rows[0]['NodoID'] . "-" . $rows1[0]['PadreID'] . " Attributo:" . $rows1[0]['attr'];
                $sommaArchi = $sommaArchi + $rows1[0]['attr'];
                $parent = $rows1[0]['PadreID'];
            }
    
        }

echo "<br>" . (time() - $startTime) . " Secondi";

?>