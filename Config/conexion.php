<?php

    session_start();

    class Conectar{
      protected $dbh;

      protected function Conexion(){
            try {
              $conectar = $this->dbh = new PDO("mysql:local=localhost;dbname=u198557509_biosgastro", "u198557509_biosgastro","Galtech2022");
              return $conectar;

            } catch (Exception $e) {
              print "Error Base de Datos!". $e->getMessage(). "<br/>";
              die();
            }

      }

      public function set_names(){
        return $this->dbh->query("SET NAMES 'URF8'");
      }

      public function ruta(){
        return "https://biosgastro.online/default.php";
      }
    }
 ?>
