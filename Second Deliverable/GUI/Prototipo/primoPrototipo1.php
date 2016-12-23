<?php

require "include/prototipoTree.php";
require "include/db.php";


$database = new Db();

    $albero = new Tree;
	
	if(isset($_POST['createSubmit'])){
        
        $startTime = microtime(true);
        
        $test=false;
	   $valNodo;
	   $valArco;

		for($i=1;!$test;$i++){
			if(isset($_POST['nomeAttr'.$i]) && isset($_POST['valAttr'.$i]) && isset($_POST['ruleNode'.$i])){
				$nome=$_POST['nomeAttr'.$i];
				$value=$_POST['valAttr'.$i];
				$rule=$_POST['ruleNode'.$i];
				$valNodo[$i-1]= array($rule,$value,$nome);
			}
			else
				$test=true;
		}
		$test=false;
		for($i=1;!$test;$i++){
			if(isset($_POST['nomeArco'.$i]) && isset($_POST['valArco'.$i]) && isset($_POST['ruleEdge'.$i])){
				$nome=$_POST['nomeArco'.$i];
				$value=$_POST['valArco'.$i];
				$rule=$_POST['ruleEdge'.$i];
				$valArco[$i-1]= array($rule,$value,$nome);
			}
			else
				$test=true;
		}

		$result = $albero -> createTree($_POST['nomeAlbero'], $_POST['depthSize'], $_POST['splitSize'], $valNodo, $valArco, $database);

        switch($result){
            case 3:
                echo "Errore di connessione";
                break;
            case 2:
                echo "Albero Esistente";
                break;
            case 1:
                echo "Errore in fase di creazione dell'albero";
                break;
            case 0:
                echo "Albero creato con successo";
                break;
        }

        echo "<br>";
        echo "Tempo di Creazione: " . (microtime(true) - $startTime);
        
        
        
	}

    if(isset($_POST['deleteSubmit'])){
        
        $startTime = microtime(true);
        
        $result = $albero -> deleteTree($_POST['deleteTree'], $database);

        switch($result){
            case 3:
                echo "Errore di connessione";
                break;
            case 2:
                echo "Albero Inesistente";
                break;
            case 0:
                echo "Errore in fase di eliminazione";
                break;
            case 1:
                echo "Eliminazione avvenuta con successo";
                break;
        }
        
        echo "<br>";
        echo "Tempo di Eliminazione: " . (microtime(true) - $startTime);
        
        
        
	}

    if(isset($_POST['pathSubmit'])){
        
        $startTime = microtime(true);
        
        $resultNodi;
        $resultArchi;

        $result = $albero -> calculatePath($_POST['loadTree'], $_POST['node2'], $_POST['node1'], $resultNodi, $resultArchi, $database);

        switch($result){
            case 3:
                echo "Errore di connessione";
                break;
            case 2:
                echo "Albero Inesistente";
                break;
            case 1:
                echo "Nodo Inesistente";
                break;
            case 0:
                echo "Path Inesistente";
                break;
            default:
                for ($i=0; $i<count($resultNodi); $i++){
                     echo "Somma attributi Nodi con nome " . $resultNodi[$i][0] . ": " . $resultNodi[$i][1] . "<br>";
                 }
                for ($i=0; $i<count($resultArchi); $i++){
                     echo "Somma attributi Archi con nome " . $resultArchi[$i][0] . ": " . $resultArchi[$i][1] . "<br>";
                 }
        }
        
        echo "<br>";
        echo "Tempo di Calcolo: " . (microtime(true) - $startTime);
        
        
        
	}



    
?>