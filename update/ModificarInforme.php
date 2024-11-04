<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  $data = array('res' => false, 'msg' => 'Error general.', 'result' => null);

  try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
    if(!FnValidarSesionManNivel2()){throw new Exception("Usuario no autorizado.");}
    if (empty($_POST['id']) || empty($_POST['fecha']) || empty($_POST['clidireccion']) || empty($_POST['supervisor'])) {
      throw new Exception("La información está incompleta.");
    }

    $USUARIO = date('Ymd-His (').$_SESSION['gesman']['Nombre'].')';
    $informe = new stdClass();
    $informe->id = $_POST['id'];
    $informe->fecha = $_POST['fecha'];
    $informe->clicontacto = $_POST['clicontacto'];
    $informe->clidireccion = $_POST['clidireccion'] ;
    $informe->supervisor = $_POST['supervisor'];
    $informe->actualizacion = $USUARIO;

    $result = FnModificarInforme($conmy, $informe);
    if ($result) {
      $data['msg'] = "Modificación realizada con éxito.";
      $data['res'] = true;
      $data['result'] = $result;
    } else {
      $data['msg'] = "Error al procesar la solicitud.";
    }
    $conmy = null;
  } catch (PDOException $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy = null;
  } catch (Exception $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy = null;
  } 
  echo json_encode($data);
?>
