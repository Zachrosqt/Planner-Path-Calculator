<?php


/* tabella SQL

CREATE TABLE Albero (

    AlberoID int PRIMARY KEY AUTO_INCREMENT,
    Nome varchar(50) NOT NULL UNIQUE,
    Split int NOT NULL,
    High int NOT NULL,
    usabile BOOLEAN NOT NULL

)

CREATE TABLE Nodes (

    AlberoID int NOT NULL,
    NodoID int NOT NULL,
    PadreID int,
    Livello int NOT NULL,
    Nome varchar(20) NOT NULL,
    UNIQUE (AlberoID, NodoID),
    Foreign Key (AlberoID) references Albero(AlberoID)
)

CREATE TABLE attrNodes (

    AlberoID int NOT NULL,
    NodoID int NOT NULL,
    nome varchar(50) NOT NULL,
    attr int NOT NULL,
    Foreign Key (AlberoID) references Nodes(AlberoID),
    Foreign Key (NodoID) references Nodes(NodoID)
    
)

CREATE TABLE attrEdges (

    AlberoID int NOT NULL,
    NodoID int NOT NULL,
    PadreID int NOT NULL,
    nome varchar(50) NOT NULL,
    attr int NOT NULL,
    Foreign Key (AlberoID) references Nodes(AlberoID),
    Foreign Key (NodoID) references Nodes(NodoID),
    Foreign Key (PadreID) references Nodes(PadreID)
)

*/



/* Test 1

$connection = $database->connect();

$file = fopen("tree.csv", "w");

$attr = rand();

$list = array("0", "", "0", "nodo 0", $attr);
fputcsv($file, $list, ';', ' ');

$count = 1;
$nodo = 0;
$esp = 1;
$childLvl = pow(2, $esp);
$childCur=0;

for ($level = 1; $level < 3; $nodo++){
    for($i=0; $i<2; $i++){
        $attr = rand();
        $list = array($count, $nodo, $level, "nodo $count", $attr);
        fputcsv($file, $list, ';', ' ');
        $count++;
        $childCur++;
    }
    if($childCur>=$childLvl){
        $esp++;
        $childLvl = pow(2, $esp);
        $level++;
        $childCur=0;
    }
}

$database->query("LOAD DATA LOCAL INFILE 'tree.csv' INTO TABLE nodes FIELDS TERMINATED BY ';'");*/

$startTime = time();

require "db.php";
$database = new Db();

$nomeAlbero = "Test";
$profondità = 3;
$figli = 2;


$connection = $database->connect();
        
        if (!$connection){
            
            $message = "Errore di connessione al database";
            echo $message;

        }
        
        $database->query("INSERT INTO Albero (Nome, usabile) VALUES ('$nomeAlbero', false)");
        
        $rows = $database -> select("SELECT AlberoID FROM Albero WHERE Nome = '$nomeAlbero'");

        if (!$rows){
            $message = "Errore!! Albero non creato";
            echo $message;
        }


        $fileNode = fopen("$nomeAlbero.csv", "w");
        $fileAttrNode = fopen($nomeAlbero . "Node.csv", "w");
        $fileAttrEdge = fopen($nomeAlbero . "Edge.csv", "w");
        
        $attr = rand();

        $list = array($rows[0]['AlberoID'], "0", "null", "0", "nodo 0");
        $listAttr1 = array($rows[0]['AlberoID'], "0", $attr);
        fputcsv($fileNode, $list, ';', ' ');
        fputcsv($fileAttrNode, $listAttr1, ';', ' ');


        $count = 1;
        $nodo = 0;
        $esp = 1;
        $childLvl = pow($figli, $esp);
        $childCur=0;

        for ($level = 1; $level < $profondità; $nodo++){
            for($i=0; $i<$figli; $i++){
                $attr = rand();
                
                $list = array($rows[0]['AlberoID'], $count, $nodo, $level, "nodo $count");
                $listAttr1 = array($rows[0]['AlberoID'], $count, $attr);
                $listAttr2 = array($rows[0]['AlberoID'], $count, $nodo, $attr);
                fputcsv($fileNode, $list, ';', ' ');
                fputcsv($fileAttrNode, $listAttr1, ';', ' ');
                fputcsv($fileAttrEdge, $listAttr2, ';', ' ');
                $count++;
                $childCur++;
            }
            if($childCur>=$childLvl){
                $esp++;
                $childLvl = pow($figli, $esp);
                $level++;
                $childCur=0;
            }
        }

        $result = $database->query("LOAD DATA LOCAL INFILE '$nomeAlbero.csv' INTO TABLE nodes FIELDS TERMINATED BY ';' ");

        $result1 = $database->query("LOAD DATA LOCAL INFILE '" . $nomeAlbero . "Node.csv' INTO TABLE attrNodes FIELDS TERMINATED BY ';'");

        $result2 = $database->query("LOAD DATA LOCAL INFILE '" . $nomeAlbero . "Edge.csv' INTO TABLE attrEdges FIELDS TERMINATED BY ';'");
        
        if($result==1 && $result1==1 && $result2==1 ){
            $database->query("UPDATE Albero SET usabile = true WHERE AlberoID = " . $rows[0]['AlberoID'] . "");
            $message = "Albero Creato";
        } else {
            $database->query("DELETE FROM Albero WHERE AlberoID = " . $rows[0]['AlberoID'] . "");
            $database->query("DELETE FROM nodes WHERE AlberoID = " . $rows[0]['AlberoID'] . "");
            $database->query("DELETE FROM attrNodes WHERE AlberoID = " . $rows[0]['AlberoID'] . "");
            $database->query("DELETE FROM attrEdges WHERE AlberoID = " . $rows[0]['AlberoID'] . "");
            $message = "Errore in fase di creazione dell'albero";
        }
        echo $message;

echo (time() - $startTime);

?>