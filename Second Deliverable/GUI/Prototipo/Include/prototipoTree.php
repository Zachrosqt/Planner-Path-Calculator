<?php

class Tree {
     
    /*
    
    ----------------------------------------------------------------
    Metodo di creazione dell'albero
    ----------------------------------------------------------------
    
    createTree($nomeAlbero, $profondita, $figli, $arrayRuleNode, $arrayRuleEdge, $database)
    
        $nomeAlbero = Nome che si vuole dare all'albero
        $profondita = Altezza da dare all'albero
        $figli = Numero di figli da destinare ad ogni nodo
        $arrayRuleNode = Array doppio con valori (rule, value, nome attributo). Attributi per i nodi (regole definite nel metodo attributeRule()) Es. $result ((0, 2, "attributo 1"), (1, 9, "attributo 2"))
        $arrayRuleEdge = Array doppio con valori (rule, value, nome attributo). Attributi per gli archi (regole definite nel metodo attributeRule()) Es. $result ((0, 2, "attributo 1"), (1, 9, "attributo 2"))
        $database = oggetto di tipo Db (oggetto definito nel file db.php nella cartella Include)
        
        
    Return del metodo -> 3 - Errore di connessione
                         2 - Albero Esistente
                         1 - Errore in fase di creazione dell'albero
                         0 - Albero creato con successo
    
    */
    
    public function createTree($nomeAlbero, $profondita, $figli, $arrayRuleNode, $arrayRuleEdge, $database){
        
        // Metodo connection() dichiarato nel file db.php
        // Controllo di connessione avvenuta con successo -----------------------------------------------
        
        $connection = $database->connect();
        
        if (!$connection){
            return 3;

        }
        
        //-----------------------------------------------------------------------------------------------
        
        // Metodo select() dichiarato nel file db.php
        // Query di controllo di esistenza dell'albero - Se l'albero esiste return 2; -------------------
        
        $rows = $database -> select("SELECT * FROM Albero WHERE Nome = '$nomeAlbero'");
        
        if ($rows!=false){
            return 2;
        }
        
        //----------------------------------------------------------------------------------------------
        
        // Query di insert dell'albero e select dell'id assegnatogli------------------------------------
        
        $database->query("INSERT INTO Albero (Nome, Split, High, usabile, dataCreazione) VALUES ('$nomeAlbero', $figli, $profondita, false, NOW())");
        
        $rows = $database -> select("SELECT * FROM Albero WHERE Nome = '$nomeAlbero'");

        if (!$rows){
            return 1;
        }
        
        //----------------------------------------------------------------------------------------------
        
        // Creazione dei file csv e riempimento di questi con nodi e attributi--------------------------

        $fileNode = fopen("csv/$nomeAlbero.csv", "w");
        $fileAttrNode = fopen("csv/" . $nomeAlbero . "Node.csv", "w");
        $fileAttrEdge = fopen("csv/" . $nomeAlbero . "Edge.csv", "w");
        
        // scorro gli attributi da inserire nel nodo
        for ($at=0; $at < count($arrayRuleNode); $at++){
            $attrNodo = $this->attributeRule($arrayRuleNode[$at][0], $arrayRuleNode[$at][1]); // chiamo la funzione attributeRule() con la regola e il valore inseriti

            $listAttr1 = array($rows[0]['AlberoID'], "0", $arrayRuleNode[$at][2], $attrNodo);
            fputcsv($fileAttrNode, $listAttr1, ';', ' ');            
        }
        
        $list = array($rows[0]['AlberoID'], "0", "null", "0");
        fputcsv($fileNode, $list, ';', ' ');


        $count = 1; // Id del nodo 
        $nodo = 0; // nodo padre corrente
        $esp = 1;
        $childLvl = pow($figli, $esp); // espressione per tener conto del numero di figli da creare nel livello corrente prima di aggiornarlo
        $childCur=0; // numero di nodi creati nel livello
        
        // For che mantiene il livello dell'albero e aggiorna il nodo padre
        for ($level = 1; $level < $profondita; $nodo++){
            //for che tiene conto del numero di figli creati sotto il nodo padre corrente
            for($i=0; $i<$figli; $i++){
                
                // scorro gli attributi da inserire nel nodo
                for ($at=0; $at < count($arrayRuleNode); $at++){
                    $attrNodo = $this->attributeRule($arrayRuleNode[$at][0], $arrayRuleNode[$at][1]); // attributo nodo

                    $listAttr1 = array($rows[0]['AlberoID'], $count, $arrayRuleNode[$at][2], $attrNodo);
                    fputcsv($fileAttrNode, $listAttr1, ';', ' ');            
                }
                
                 // scorro gli attributi da inserire negli archi
                for ($at=0; $at < count($arrayRuleEdge); $at++){
                    $attrArco = $this->attributeRule($arrayRuleEdge[$at][0], $arrayRuleEdge[$at][1]); // attributo arco

                    $listAttr2 = array($rows[0]['AlberoID'], $count, $nodo, $arrayRuleEdge[$at][2], $attrArco);
                    fputcsv($fileAttrEdge, $listAttr2, ';', ' ');          
                }
                
                $list = array($rows[0]['AlberoID'], $count, $nodo, $level);
                
                fputcsv($fileNode, $list, ';', ' ');
                
                $count++;
                $childCur++;
            }
            
            // entra se il numero di nodi creati nel livello è >= del numero di nodi da creare nel livello
            
            if($childCur>=$childLvl){
                $esp++;
                $childLvl = pow($figli, $esp);
                $level++;
                $childCur=0;
            }
        }
        
        //----------------------------------------------------------------------------------------------
        
        // Controllo lo split dell'albero se split=1 mantengo i file csv creati altrimenti li inserisco nel database e li elimino 
        
        if ( $rows[0]['Split'] == 1){
            
            //aggiorno la tabella albero  creata in precedenza immettendo tru alla colonna di controllo di utilizzo dell'albero
            $database->query("UPDATE Albero SET usabile = true WHERE AlberoID = " . $rows[0]['AlberoID'] . "");
            return 0;
            
        } else {
            
            // Query di inserimento dei file csv nelle proprie tabelle e controllo di esecuzione andata a buon fine -----

            $result = $database->query("LOAD DATA LOCAL INFILE 'csv/$nomeAlbero.csv' INTO TABLE nodes FIELDS TERMINATED BY ';' ");

            $result1 = $database->query("LOAD DATA LOCAL INFILE 'csv/" . $nomeAlbero . "Node.csv' INTO TABLE attrNodes FIELDS TERMINATED BY ';'");

            $result2 = $database->query("LOAD DATA LOCAL INFILE 'csv/" . $nomeAlbero . "Edge.csv' INTO TABLE attrEdges FIELDS TERMINATED BY ';'");

            // controllo di query andata a buon fine

            if($result==1 && $result1==1 && $result2==1 ){
                
                //aggiorno la tabella albero  creata in precedenza immettendo tru alla colonna di controllo di utilizzo dell'albero
                $database->query("UPDATE Albero SET usabile = true WHERE AlberoID = " . $rows[0]['AlberoID'] . "");
                
                fclose($fileNode);
                fclose($fileAttrNode);
                fclose($fileAttrEdge);
                
                // eliminazione dei file csv ----------------------------------------------------------------
        
                array_map("unlink", glob("csv/$nomeAlbero.csv"));
                array_map("unlink", glob("csv/" . $nomeAlbero . "Node.csv"));
                array_map("unlink", glob("csv/" . $nomeAlbero . "Edge.csv"));

                // -------------------------------------------------------------------------------------------
                
                return 0;
                
            } else {
                // nel caso di query non andate a buon fine elimino ciò che è stato creato
                $database->query("DELETE FROM Albero WHERE AlberoID = " . $rows[0]['AlberoID'] . "");
                $database->query("DELETE FROM nodes WHERE AlberoID = " . $rows[0]['AlberoID'] . "");
                $database->query("DELETE FROM attrNodes WHERE AlberoID = " . $rows[0]['AlberoID'] . "");
                $database->query("DELETE FROM attrEdges WHERE AlberoID = " . $rows[0]['AlberoID'] . "");
                
                fclose($fileNode);
                fclose($fileAttrNode);
                fclose($fileAttrEdge);

                array_map("unlink", glob("csv/$nomeAlbero.csv"));
                array_map("unlink", glob("csv/" . $nomeAlbero . "Node.csv"));
                array_map("unlink", glob("csv/" . $nomeAlbero . "Edge.csv"));
                
                return 1;
            }

            //---------------------------------------------------------------------------------------------
            
        }
        
    }
    
    
    /*
    
    ----------------------------------------------------------------
    Metodo di calcolo della somma degli attributi in un dato path
    ----------------------------------------------------------------
    
    calculatePath($nomeAlbero, $childId, $parentId, &$resultNode, &$resultEdge, $database)
    
        $nomeAlbero = Nome dell'albero da cui prendere il path
        $childId = nodo finale del path
        $parentId = nodo iniziale del path
        &$resultNode = variabile passata dove verra restituita la somma dei nodi come array doppio
        &$resultEdge = variabile passata dove verra restituita la somma degli archi come array doppio
        $database = oggetto di tipo Db (oggetto definito nel file db.php nella cartella Include)
        
        
    Return del metodo -> 3 - Errore di connessione
                         2 - Albero Inesistente
                         1 - Nodo Inesistente
                         0 - Path Inesistente
                         $resultNode - array doppio composto da n elementi in cui: array[0]= nome attributo
                                                                                    array[1]=Somma di tutti gli attributi dei nodi nel path
                                                                                    
                        $resultEdge - array doppio composto da n elementi in cui: array[0]= nome attributo
                                                                                    array[1]=Somma di tutti gli attributi degli archi nel path
    
    */
    
    
    public function calculatePath($nomeAlbero, $childId, $parentId, &$resultNode, &$resultEdge, $database){
        
        // Controllo connessione ------------------------------------------------------------------------------------
        
        $connection = $database->connect();
        
        if (!$connection){
            return 3;

        }
        
        //-----------------------------------------------------------------------------------------------------------
        
        // Controllo esistenza albero -------------------------------------------------------------------------------
        
        $albero = $database -> select("SELECT * FROM Albero WHERE Nome = '$nomeAlbero'");
        
        if (!$albero || $albero[0]['usabile'] == 0){
            return 2;
        }
        
        //-----------------------------------------------------------------------------------------------------------
        
        // Controllo lo split dell'albero se split=1 faccio un calcolo altrimenti ne faccio un altro 
        
        if ( $albero[0]['Split'] == 1){
            
            // controllo se il nodo di partenza o il nodo di arrivo è più grande dell'altezza dell'albero ----------- 
            
            if ($childId>=$albero[0]['High'] || $parentId>=$albero[0]['High']){
                
                return 1;
                
            }
            
            //-------------------------------------------------------------------------------------------------------
            
            $fileAttrNode = fopen("csv/" . $nomeAlbero . "Node.csv", "r"); // apro il file csv con gli attributi dei nodi
            $fileAttrEdge = fopen("csv/" . $nomeAlbero . "Edge.csv", "r"); // apro il file csv con gli attributi degli archi

            $trovato = false; 
            
            // Scorro tutte le righe del csv degli archi -------------------------------------------------------------
            
            for($i=0; !feof($fileAttrNode) && !$trovato; $i++){
                
                $result = fgetcsv($fileAttrNode, 0, ";");
                
                if ($result[1]==0){
                    $resultNode[$i] = array ($result[2], 0);
                } else {
                    $trovato = true;
                }

            }
            
            //-------------------------------------------------------------------------------------------------------

            $trovato = false;

            // Scorro tutte le righe del csv degli archi -------------------------------------------------------------
            
            for($i=0; !feof($fileAttrEdge) && !$trovato; $i++){
                
                $result = fgetcsv($fileAttrEdge, 0, ";");
                
                if ($result[1]==1){
                    $resultEdge[$i] = array ($result[3], 0);
                } else {
                    $trovato = true;
                }

            }
            
            //-------------------------------------------------------------------------------------------------------

            $trovato = false;
            
            // Scorro tutte le righe del csv  dei nodi ---------------------------------------------------------------
            
            while (!feof($fileAttrNode) && !$trovato){
                $result = fgetcsv($fileAttrNode, 0, ";"); // prendo la riga dal csv

                // Se il nodoID nel csv è compreso è compreso tra il nodo padre e il nodo figlio del path allora sommo gli attributi
                if ($result[1]>=$parentId && $result[1]<=$childId){
                    for ($i=0; $i<count($resultNode); $i++){
                        if ($resultNode[$i][0]==$result[2]){
                            $resultNode[$i][1] =  $resultNode[$i][1] + $result[3];
                        }    
                    }
                }
                // Se ho trovato il figlio esco dal ciclo
                if ($result[1]>$childId){
                     $trovato = true;
                }

            }
            
            //-------------------------------------------------------------------------------------------------------

            $trovato = false;

            // Scorro tutte le righe del csv degli archi -------------------------------------------------------------
            
            while (!feof($fileAttrEdge) && !$trovato){
                $result = fgetcsv($fileAttrEdge, 0, ";"); // prendo la riga dal csv

                // Se l'arco nel csv è compreso è compreso tra il nodo padre e il nodo figlio del path allora sommo gli attributi
                if ($result[2]>=$parentId && $result[1]<=$childId){
                    for ($i=0; $i<count($resultEdge); $i++){
                        if ($resultEdge[$i][0]==$result[3]){
                            $resultEdge[$i][1] =  $resultEdge[$i][1] + $result[4];
                        }    
                    }
                }
                // Se ho trovato l'arco entrante figlio esco dal ciclo
                if ($result[1]>$childId){
                    $trovato = true;
                }

            }
            
            //-------------------------------------------------------------------------------------------------------
            
            return 10; // restituisco array con somma attributi nodi e somma attributi archi
            
        } else {

            // Seleziono il nodo figlio inserito nel path e ne controllo l'esistenza --------------------------------
            
            $rows = $database -> select("SELECT * FROM attrNodes WHERE NodoID = $childId AND AlberoID = " . $albero[0]['AlberoID'] . "");

            if (!$rows){
                return 1;
            }
            
            for ($i=0; $i<count($rows); $i++){
                $resultNode[$i] = array ($rows[$i]['nome'], $rows[$i]['attr']);
            }
            
            //-------------------------------------------------------------------------------------------------------
            
            // controllo se già ho trovato il path 

            if($rows[0]['NodoID'] == $parentId){
                    return 10; // se trovato restituisco la somma degli attributi dei nodi
            } else {
                
                // cerco arco entrante al figlio del path e controllo la sua esistenza
                $rows1 = $database -> select("SELECT * FROM attrEdges WHERE NodoID = $childId AND AlberoID = " . $albero[0]['AlberoID'] . "");

                if (!$rows1){
                    return 1;
                }
                
                for ($i=0; $i<count($rows1); $i++){
                    $resultEdge[] = array ($rows1[$i]['nome'], $rows1[$i]['attr']);
                }
                $parent = $rows1[0]['PadreID']; // setto il nodo da cui nasce l'arco
            }

            // ciclo risalendo dal figlio verso il padre del path
            while(true){

                $rows = $database -> select("SELECT * FROM attrNodes WHERE NodoID = $parent AND AlberoID = " . $albero[0]['AlberoID'] . "");

                if (!$rows){
                    return 1;
                }
                
                for ($i=0; $i<count($rows); $i++){
                    for ($sum=0; $sum < count($resultNode); $sum++){
                        if ($resultNode[$sum][0] == $rows[$i]['nome']){
                            $resultNode[$sum][1] =  $resultNode[$sum][1] + $rows[$i]['attr'];
                        }
                    }
                }
                
                // Se ho trovato il nodo padre del path restituisco lasomma degli attributi di nodi e archi in un array
                if($rows[0]['NodoID'] == $parentId){
                    return 10;

                } else if ($rows[0]['NodoID'] < $parentId){ // se il nodo corrente è minore del nodo padre del path allora faccio il return di path insesistente
                    return 0;

                } else if ($rows[0]['NodoID']==0){ // se sono arrivato al nodo radice senza restituire la somma allora faccio il return di nodo inesistente
                    return 0;

                } else { // s non ho effettuato nessuna delle cose sopra descritte cerco l'arco entrante al nodo corrente
                    $rows1 = $database -> select("SELECT * FROM attrEdges WHERE NodoID = $parent AND AlberoID = " . $albero[0]['AlberoID'] . "");

                    if (!$rows1){
                        return 1;
                    }
                    for ($i=0; $i<count($rows1); $i++){
                        for ($sum=0; $sum < count($resultEdge); $sum++){
                            if ($resultEdge[$sum][0] == $rows1[$i]['nome']){
                                $resultEdge[$sum][1] =  $resultEdge[$sum][1] + $rows1[$i]['attr'];
                            }
                        }
                    }
                    $parent = $rows1[0]['PadreID'];
                }

            }
        }
        
    }
    
    /*
    ----------------------------------------------------------------
    Metodo di calcolo della somma degli attributi in un dato path
    ----------------------------------------------------------------
    
    deleteTree($nomeAlbero, $database)
    
        $nomeAlbero = Nome dell'albero da cui prendere il path
        $database = oggetto di tipo Db (oggetto definito nel file db.php nella cartella Include)
        
        
    Return del metodo -> 3 - Errore di connessione
                         2 - Albero Inesistente
                         1 - Eliminazione avvenuta con successo
                         0 - Errore in fase di eliminazione
                        
    */
    
    public function deleteTree($nomeAlbero, $database){
        
        // controllo connessione ------------------------------------------------------------------
        
        $connection = $database->connect();
        
        if (!$connection){
            return 3;

        }
        
        // ----------------------------------------------------------------------------------------
        
        // controllo esistenza albero -------------------------------------------------------------
        
        $albero = $database -> select("SELECT * FROM Albero WHERE Nome = '$nomeAlbero'");
        
        if (!$albero || $albero[0]['usabile'] == 0){
            return 2;
        }
        
        //------------------------------------------------------------------------------------------
        
        // setto il tag nella tabella albero uguale a "false" in maniera tale che l'albero è inutilizzabile da altri
        $database->query("UPDATE Albero SET usabile = false WHERE AlberoID = " . $albero[0]['AlberoID'] . "");
        
        
        // Controllo lo split dell'albero se split=1 elimino i file csv altrimenti elimino i dati nel database
        if ( $albero[0]['Split'] == 1){
            
            // eliminazione dei file csv ----------------------------------------------------------------

            array_map("unlink", glob("csv/$nomeAlbero.csv"));
            array_map("unlink", glob("csv/" . $nomeAlbero . "Node.csv"));
            array_map("unlink", glob("csv/" . $nomeAlbero . "Edge.csv"));

            // -------------------------------------------------------------------------------------------
            
            $result = $database->query("DELETE FROM Albero WHERE AlberoID = " . $albero[0]['AlberoID'] . "");
            
            if($result==1){
                return 1;
            } else {
                return 0;
            }
            
        } else {
            
            // query di eliminazione dal Database ------------------------------------------------------
        
            $result1 = $database->query("DELETE FROM attrNodes WHERE AlberoID = " . $albero[0]['AlberoID'] . "");
            $result2 = $database->query("DELETE FROM attrEdges WHERE AlberoID = " . $albero[0]['AlberoID'] . "");
            $result = $database->query("DELETE FROM nodes WHERE AlberoID = " . $albero[0]['AlberoID'] . "");
            $result3 = $database->query("DELETE FROM Albero WHERE AlberoID = " . $albero[0]['AlberoID'] . "");

            //-------------------------------------------------------------------------------------------

            // controllo se l'eliminazione è andata a buon fine
            if($result==1 && $result1==1 && $result2==1 && $result3==1){
                return 1;
            } else {
                return 0;
            }
            
        }
        
    }
    
    
    
    public function attributeRule($rule=0, $valAttr=0){
        
        switch($rule){
            case 4:
                $attr = 4;
                return $attr;
                break;
            case 3:
                $attr = 3;
                return $attr;
                break;
            case 2:
                $attr = 2;
                return $attr;
                break;
            case 1:
                $attr = $valAttr;
                return $attr;
                break;
            default:
                $attr = rand();
                return $attr;

        }

    }
}

?>