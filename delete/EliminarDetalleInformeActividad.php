<?php 
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";

  $data = array('res' => false, 'msg' => 'Error general.');

  try {
      if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
      if (empty($_POST['id'])) {throw new Exception("La información está incompleta.");}
      $id = $_POST['id']; 

      $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      if (FnEliminarActividad($conmy, $id)) {
          $data['msg'] = "Se eliminó la Actividad.";
          $data['res'] = true;
      } else {
          $data['msg'] = "Error eliminando la Actividad.";
      }
  } catch (PDOException $ex) {
      $data['msg'] = $ex->getMessage();
  } catch (Exception $ex) {
      $data['msg'] = $ex->getMessage();
  } 
  $conmy = null;
  echo json_encode($data);
?>