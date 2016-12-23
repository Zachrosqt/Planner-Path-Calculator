<?php

require "include/prototipoTree.php";
require "include/db.php";

$startTime = time();

$database = new Db();

    $albero = new Tree;

$nomeAlbero = "AlberoTest";

    $ruleNode[0] = array(0, 0, "Pippo");
    $ruleNode[1] = array(1, 12, "Gino");
    $ruleArchi[0] = array(1, 12, "Paolo");
    $ruleArchi[1] = array(2, 0, "Testa di cazzo");


    $result = $albero -> createTree($nomeAlbero, 21, 2, $ruleNode, $ruleArchi, $database);

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
    echo "Tempo di Creazione: " . (time() - $startTime);
    $startTime = time();
    echo "<br>";

    $resultNodi;
    $resultArchi;

    $result = $albero -> calculatePath($nomeAlbero, 1999999, 2, $resultNodi, $resultArchi, $database);

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
    echo "Tempo di Calcolo: " . (time() - $startTime);
    $startTime = time();
    echo "<br>";

    $result = $albero -> deleteTree($nomeAlbero, $database);

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
    echo "Tempo di Eliminazione: " . (time() - $startTime);

?>