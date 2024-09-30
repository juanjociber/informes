<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";

    $data = array('res' => false, 'msg' => 'Error general.');

    try {
      if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
      if (empty($_POST['infid']) || empty($_POST['actividad']) || empty($_POST['tipo'])) {throw new Exception("La información está incompleta.");}

      $USUARIO = date('Ymd-His (').$_SESSION['UserName'].')';

      $actividad = new stdClass();
      $actividad->infid = $_POST['infid'];
      $actividad->ownid = empty($_POST['ownid']) ? 0 : $_POST['ownid'];
      $actividad->actividad = $_POST['actividad'];
      $actividad->diagnostico = empty($_POST['diagnostico']) ? null : $_POST['diagnostico'];
      $actividad->trabajos = empty($_POST['trabajos']) ? null : $_POST['trabajos'];
      $actividad->observaciones = empty($_POST['observaciones']) ? null : $_POST['observaciones'];
      $actividad->tipo = $_POST['tipo']; 
      $actividad->usuario = $USUARIO;

      $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      if (FnRegistrarActividad($conmy, $actividad)) {
        $data['msg'] = "Se registró la Actividad.";
        $data['res'] = true;
      } else {
        $data['msg'] = "Error registrando la Actividad.";
      }
    } catch (PDOException $ex) {
        $data['msg'] = $ex->getMessage();
    } catch (Exception $ex) {
        $data['msg'] = $ex->getMessage();
    } 
    $conmy = null;
    echo json_encode($data);
?>
