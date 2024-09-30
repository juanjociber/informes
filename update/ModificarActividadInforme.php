<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";

  $data = array('res' => false,'msg' => 'Error general.');
  
  try {
    if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
    if (empty($_POST['id']) || empty($_POST['actividad'])) {
        throw new Exception("La información está incompleta.");
    }
    $USUARIO = date('Ymd-His (').$_SESSION['UserName'].')';
    $actividad = new stdClass();
    $actividad->id = $_POST['id'];
    $actividad->actividad = $_POST['actividad'];
    $actividad->usuario = $USUARIO;

    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (FnModificarActividadInforme($conmy, $actividad)) {
        $data['msg'] = 'Modificación exitosa.';
        $data['res'] = true;
    } else {
        $data['msg'] = 'Error modificando la actividad.';
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
