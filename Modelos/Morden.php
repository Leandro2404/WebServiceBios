<?php
    class Morden extends Conectar{

      public function get_orden(){
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT * FROM cliente";
        $sql=$conectar->prepare($sql);
        $sql->execute();
        return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
      }

      public function get_orden_x_dni($dni){
        $conectar= parent::conexion();
        parent::set_names();
        $sql="CALL sp_ListarOrden(?)";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$dni);
        $sql->execute();
        $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
      }

      public function update_parte_orden($FechaInicio, $FechaFin ,$TareaDesarrollada ,$IdParte,$Completada ,$IdOrden){
        $conectar= parent::conexion();
        parent::set_names();
        $sql="UPDATE parteorden
              SET FechaInicio=?, FechaFin= ?,
              TareaDesarrollada=?, Completa=1
              WHERE IdParte= ?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$FechaInicio);
        $sql->bindValue(2,$FechaFin);
        $sql->bindValue(3,$TareaDesarrollada);
        $sql->bindValue(4,$IdParte);
        $sql->execute();
        $result=$this->update_orden($Completada ,$IdOrden);
        return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
      }

      public function update_orden($Completada ,$IdOrden){
        $conectar= parent::conexion();
        parent::set_names();
        $sql="UPDATE orden
              SET Completada=?
              WHERE IdOrden=?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$Completada);
        $sql->bindValue(2,$IdOrden);
        $sql->execute();


        return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
      }

      public function get_tecnico($dni){
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT IFNULL( (SELECT Nombre FROM tecnico WHERE Dni = ?) , 'false') Nombre";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$dni,PDO::PARAM_INT);
        $sql->execute();

        $resultado=$sql->fetch(PDO::FETCH_ASSOC);
        if($resultado != null ){
          return $resultado;
        }else{
          return false;
        }

      }



      public function actualizaParteOrden($idParte ,$tareaDesarrollada,$fechaInicio,$fechaFin,$completa, $estado){
        $conectar= parent::conexion();
        parent::set_names();
        $sql="UPDATE `parteorden` SET
        `TareaDesarrollada`=?,
        `FechaInicio`=?,
        `FechaFin`=?,
        `Completa`=?,
        `Estado`=?
         WHERE `IdParte`=?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$tareaDesarrollada);

        if($fechaInicio == null)
          $sql->bindValue(2,null);
        else
          $sql->bindValue(2,$fechaInicio);
          
        if($fechaFin == null)
          $sql->bindValue(3,null);
        else
          $sql->bindValue(3,$fechaFin);

        $sql->bindValue(4,$completa);
        $sql->bindValue(5,$estado);
        $sql->bindValue(6,$idParte);
        $sql->execute();

      }

      public function insertaMateriales($cantidad ,$idParte,$idOrden,$descripcion, $precio, $idCelular){

          $conectar= parent::conexion();
        parent::set_names();
          $sql="INSERT INTO `material`
        (`Cantidad`, `IdParte`, `IdOrden`,`Descripcion`, `Precio`, `IdCelular`)
        VALUES (?,?,?,?,?,?)";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$cantidad);
        $sql->bindValue(2,$idParte);
        $sql->bindValue(3,$idOrden);
        $sql->bindValue(4,$descripcion);
        $sql->bindValue(5,$precio);
        $sql->bindValue(6,$idCelular);
        $sql->execute();
      
      }


      public function get_material($idCelular, $descripcion, $precio, $idParte, $idOrden){
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT IdMat FROM material WHERE IdCelular = ? and Descripcion = ? and Precio = ? and IdParte = ? and IdOrden = ?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$idCelular,PDO::PARAM_INT);
        $sql->bindValue(2,$descripcion);
        $sql->bindValue(3,$precio);
        $sql->bindValue(4,$idParte);
        $sql->bindValue(5,$idOrden);
        $sql->execute();

        $resultado=$sql->fetch(PDO::FETCH_ASSOC);
        if($resultado != null ){
          return false;
        }else{
          return true;
        }

      }

      public function json($i){
        $conectar= parent::conexion();
        parent::set_names();
          $sql="INSERT INTO `demo`
        (`json` )
        VALUES (?)";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$i);
        $sql->execute();

      }

     public function verificaParte($id){
        $conectar= parent::conexion();
        parent::set_names();
          $sql="SELECT IdParte FROM `parteorden` WHERE IdParte=? AND Completa =1;";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$id);
        $sql->execute();
        $resultado=$sql->fetch(PDO::FETCH_ASSOC);

        return $resultado;

        //asdas
      }

      public function update_token($dni ,$token){
        $conectar= parent::conexion();
        parent::set_names();
        $sql="UPDATE tecnico
              SET TokenMobile=?
              WHERE Dni=?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$token);
        $sql->bindValue(2,$dni);
        $sql->execute();


        return "Ok";
      }

      public function sendNoti($dni){

        $conectar= parent::conexion();
        parent::set_names();
          $sql="SELECT TokenMobile FROM tecnico WHERE Dni=? AND Activo=1;";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$dni);
        $sql->execute();
        $resultado=$sql->fetch(PDO::FETCH_ASSOC);

        $token = $resultado["TokenMobile"];

        $url = 'https://fcm.googleapis.com/fcm/send';

        // use key 'http' even if you send the request to https://...
        $fields = array(
            'to' => $token,
            'notification' => array("body" => "Tienes ordenes nuevas", 
            "title" => "Coolsoft")
        );
    
        $headers = array(
            'Authorization:key = AAAAlA3B7UA:APA91bFS06f1IiFI-lKapyGDDBD-lOxbNR4ercCwY2NerBnuhZbuXK0dHe0SWlIi13RgnljFyEbWM3peJlHHzUpb66G85UrdiDUD_5gDHh1L-HxOocBjwG36k9dSw8t_bGgGtMgz_3cM', //Change API KEY HERE
            'Content-Type: application/json'
        );
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        
        $result = curl_exec($ch);           
    
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result);


      }


      public function ejecutarQuery($query){
        $conectar= parent::conexion();
        parent::set_names();
        $sql=$conectar->prepare($query);
        $sql->execute();
        $resultado=$sql->fetch(PDO::FETCH_ASSOC);

        return $resultado;
      }







      /*
      UPDATE biosgastro.parteorden
SET FechaInicio={FechaInicio del body}, FechaFin={FechaFin del body}, TareaDesarrollada={TareaDesarrolladadel body}, Completa=1
WHERE IdParte= {IdParte del body};

UPDATE biosgastro.orden
SET Completada={Completada del body}
WHERE IdOrden={idOrden del body};
IdParte:
FechaInicio
FechaFin
TareaDesarrollada
IdOrden
Completada
*/


    }

 ?>
