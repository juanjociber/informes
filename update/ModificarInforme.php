<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  $data = array('res' => false, 'msg' => 'Error general.', 'result' => null);

  try {
    if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
    if (empty($_POST['id']) || empty($_POST['fecha']) || empty($_POST['clidireccion']) || empty($_POST['supervisor'])) {
      throw new Exception("La información está incompleta.");
    }

    $USUARIO = date('Ymd-His (').$_SESSION['UserName'].')';
    $informe = new stdClass();
    $informe->id = $_POST['id'];
    $informe->fecha = $_POST['fecha'];
    $informe->clicontacto = $_POST['clicontacto'];
    $informe->clidireccion = $_POST['clidireccion'] ;
    $informe->supervisor = $_POST['supervisor'];
    $informe->actualizacion = $USUARIO;

    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $result = FnModificarInforme($conmy, $informe);
    if ($result) {
      $data['msg'] = "Modificación realizada con éxito.";
      $data['res'] = true;
      $data['result'] = $result;
    } else {
      $data['msg'] = "Error al procesar la solicitud.";
    }
  } catch (PDOException $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy = null;
  } catch (Exception $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy = null;
  } 
  echo json_encode($data);
?>
