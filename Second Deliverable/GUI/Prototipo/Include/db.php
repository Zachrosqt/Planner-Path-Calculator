<?php

    class Db {
        // La connessione al database
        protected static $connection;

        /**
         * Connetti al database
         * 
         * @return bool false per fallimento / mysqli MySQLi object instance per successo
         */
        public function connect() {    
            // Prova e connette al database
            if(!isset(self::$connection)) {
                // Carica le configurazioni come un array
                $config = parse_ini_file('include/config.ini'); 
                self::$connection = new mysqli('localhost',$config['username'],$config['password'],$config['dbname']);
            }

            // Controlla la connessione andata a buon fine
            if(self::$connection === false) {
                // return false in caso di connessione non andata a buon fine
                return false;
            }
            return self::$connection;
        }

        /**
         * Query per il database
         *
         * @param $query la stringa della query
         * @return il risultato della funzione mysqli::query() 
         */
        public function query($query) {
            // Connessione al database
            $connection = $this -> connect();

            // Query sul database
            $result = $connection -> query($query);

            return $result;
        }

        /**
         * Fetch rows dal database (SELECT query)
         *
         * @param $query la stringa della query
         * @return bool false per fallimento / righe del database in array per successo
         */
        public function select($query) {
            $rows = array();
            $result = $this -> query($query);
            if($result === false) {
                return false;
            }
            while ($row = $result -> fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }

        /**
         * Fetch l'ultimo errore del database
         * 
         * @return string messaggio di errore del database
         */
        public function error() {
            $connection = $this -> connect();
            return $connection -> error;
        }

        /**
         * Quote ed elimina caratteri non accettati per la query sul database
         *
         * @param string $value il valore da analizzare
         * @return string 
         */
        public function quote($value) {
            $connection = $this -> connect();
            return "'" . $connection -> real_escape_string($value) . "'";
        }
    }

?>