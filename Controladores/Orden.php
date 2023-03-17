<?php

    header('Content-Type: application/json');
    require_once("../Config/conexion.php");
    require_once("../Modelos/Morden.php");


    $orden = new Morden();



    $body =  json_decode(file_get_contents('php://input'),true);
    error_log($body, 3,  '../mi_log_file.log');


    switch ($_GET["op"]) {
      case 'GetAll':
        $datos=$orden->get_orden();
        echo json_encode($datos);
        break;

      case 'ListarOrden':
        $datos=$orden->get_orden_x_dni($body["DNICUIT"]);
        echo json_encode($datos);
      break;

      case 'UpdateOrden':
        $datos=$orden->update_parte_orden($body["FechaInicio"],$body["FechaFin"],$body["TareaDesarrollada"],$body["IdParte"],$body["Completada"],$body["IdOrden"]);
        echo "Correcto";
      break;

      case 'Login':
          $datos=$orden->get_tecnico($body["DNI"]);
          echo json_encode($datos);
      break;

      case 'UpdateToken':
        $datos=$orden->update_token($body["DNI"], $body["Token"]);
        echo json_encode($datos);
      break;

      case 'SendNotification':
        $datos=$orden->sendNoti($body["DNI"]);
        echo json_encode($datos);
      break;

      /*case 'Guardar':

        $json = json_encode($body["ParteOrden"]);
       $data =  json_decode($json,true);
       $orden->actualizaParteOrden($data["IdParte"],$data["TareaDesarrollada"],$data["FechaInicio"],$data["FechaFin"],$data["Completa"]);
       $cantMat= count($body["Materiales"]);
       for ($i=0; $i < $cantMat ; $i++) {
                 $json = json_encode($body["Materiales"][$i]);
                 $mat = json_decode($json);
                  $orden->insertaMateriales($mat->Cantidad,$mat->IdParte,$mat->IdOrden,$mat->Descripcion);

           }
        echo $json;
      break;*/
      case 'Guardar':

       $json = json_encode($body["ParteOrden"]);
       $data =  json_decode($json,true);
       $orden->actualizaParteOrden($data["IdParte"],$data["TareaDesarrollada"],$data["FechaInicio"],$data["FechaFin"],$data["Completa"], $data["Estado"]);
       $cantMat= count($body["Materiales"]);
         for ($i=0; $i < $cantMat ; $i++) {
                   $json = json_encode($body["Materiales"][$i]);
                   $mat = json_decode($json);
                   if($orden->get_material($mat->IdMat, $mat->Descripcion, $mat->Precio, $mat->IdParte, $mat->IdOrden)){
                    $orden->insertaMateriales($mat->Cantidad,$mat->IdParte,$mat->IdOrden, $mat->Descripcion, $mat->Precio, $mat->IdMat);
                    /*echo    $mat->cantidad."-";
                    echo    $mat->descripcion."-";
                    echo    $mat->idParte."-";
                    echo    $mat->idOrden."\n";*/
                    
                   }
             }
             $json = json_encode($body,true) ;
             echo $json;
           /*$json = json_encode($body,true) ;
           echo $json;
           $orden->json($json);*/
       break;

      case 'VerificarIdPartes':

        $bikes= new stdClass;
        $testArray = [];

        $idCompletadas =0;

        $cantMat= count($body["IdPartes"]);
        for ($i=0; $i < $cantMat ; $i++) {
                  $json = json_encode($body["IdPartes"][$i]);
                  $mat = json_decode($json);
                      $idParte=$orden->verificaParte($mat);
                 
              
                  if ($idParte==null){

                      }else{
                          array_push($testArray, (int)$mat);
                      }
            }
            $body["IdPartes"]=$testArray;

            echo json_encode($body)."\n";


            /*$json = json_encode($body,true) ;
            echo $json;
            $orden->json($json);*/
        break;

      case 'Query':
        $datos=$orden->ejecutarQuery($body["Query"]);
        echo json_encode($datos);
        break;

      default:
        // code...
        break;
    }
 ?>
