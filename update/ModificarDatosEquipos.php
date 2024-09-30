<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/connection/ConnGesmanDb.php';
  require_once $_SERVER['DOCUMENT_ROOT'].'/informes/datos/InformesData.php';

  $data = array('res' => false, 'msg' => 'Error general.');

  try {
    if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
      $informe->id = (int)$_POST['id'];
    } else {
      throw new Exception('ID no válido o no proporcionado.');
    }
    $USUARIO = date('Ymd-His (').$_SESSION['UserName'].')';
    $informe = new stdClass();
    $informe->equnombre = !empty($_POST['equnombre']) ? trim($_POST['equnombre']) : null;
    $informe->equmarca = !empty($_POST['equmarca']) ? trim($_POST['equmarca']) : null;
    $informe->equmodelo = !empty($_POST['equmodelo']) ? trim($_POST['equmodelo']) : null;
    $informe->equserie = !empty($_POST['equserie']) ? trim($_POST['equserie']) : null;
    $informe->equkm = isset($_POST['equkm']) && is_numeric($_POST['equkm']) ? (int)$_POST['equkm'] : 0;
    $informe->equhm = isset($_POST['equhm']) && is_numeric($_POST['equhm']) ? (int)$_POST['equhm'] : 0;
    $informe->actualizacion = $USUARIO;

    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (FnModificarInformeDatosEquipos($conmy, $informe)) {
      $data['msg'] = "Cambios guardados con éxito.";
      $data['res'] = true;
    } else {
      $data['msg'] = "No se realizaron cambios en el informe.";
    }
  } catch (PDOException $ex) {
      $data['msg'] = $ex->getMessage();
  } catch (Exception $ex) {
      $data['msg'] = $ex->getMessage();
  } 
  $conmy = null;
  echo json_encode($data);
?>


