<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  $data = array('res' => false,'msg' => 'Error general.');
  
  try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
    if(!FnValidarSesionManNivel2()){throw new Exception("Usuario no autorizado.");}
    if (empty($_POST['id']) || empty($_POST['actividad'])) {throw new Exception("La información está incompleta.");}
    if (strlen($_POST['actividad']) > 500) {
      throw new Exception("El campo solo permite 500 caracteres.");
    }
    $USUARIO = date('Ymd-His (').$_SESSION['gesman']['Nombre'].')';
    $actividad = new stdClass();
    $actividad->id = $_POST['id'];
    $actividad->actividad = $_POST['actividad'];
    $actividad->usuario = $USUARIO;

    if (FnModificarInformeActividad($conmy, $actividad)) {
        $data['msg'] = 'Modificación realizada con éxito.';
        $data['res'] = true;
    } else {
        $data['msg'] = 'Error al procesar la solicitud.';
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
