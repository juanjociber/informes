<?php 
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";

  $data =array('res' => false,'msg' => 'Error general.');

  try {
    if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
    if (empty($_POST['id'])) { throw new Exception("La información está incompleta."); }

    $id = (int)$_POST['id'];
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (FnEliminarArchivo($conmy, $id)) {
      $data['msg'] = "Se eliminó el Archivo.";
      $data['res'] = true;
    } else {
      $data['msg'] = "Error eliminando el Archivo.";
    }
  } catch (PDOException $ex) {
      $data['msg'] = $ex->getMessage();
  } catch (Exception $ex) {
      $data['msg'] = $ex->getMessage();
  } 
  $conmy = null;
  echo json_encode($data);
?>
