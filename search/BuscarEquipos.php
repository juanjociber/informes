<?php 
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  $data = array('res' => false, 'msg' => 'Error general.','data' => null);

  try {
    if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
    if (!empty($_POST['nombre'])) {throw new Exception("La información esta incompleta.");}
    $nombre = $_POST['nombre'];

    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $equipos = FnBuscarEquipos($conmy, $nombre, $_SESSION['CliId']);
    if ($equipos) {
      $data['res'] = true;
      $data['msg'] = 'Ok.';
      $data['data'] = $equipos;
    } else {
      $data['msg'] = 'No existen registros en la base de datos.';
    }
  } catch(PDOException $ex) {
      $data['msg'] = $ex->getMessage();
      $conmy = null;
  } catch (Exception $ex) {
      $data['msg'] = $ex->getMessage();
      $conmy = null;
  } 
  echo json_encode($data);
?>

