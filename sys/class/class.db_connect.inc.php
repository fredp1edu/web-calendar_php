<?php

    class DB_Connect {

        protected $db;

        protected function __construct($dbo=NULL) {
             
            $this->db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
            if (mysqli_connect_errno($this->db))
                echo "Failed to connect to database.";
        }
    }
?>
