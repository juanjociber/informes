<?php 
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/data/InformesData.php";
  $data = array('id' => 0, 'res' => false, 'msg' => 'Error general.');

  try {
    if(!FnValidarSesion()){throw new Exception("Usuario no tiene Autorización.");}
    if(!FnValidarSesionManNivel2()){throw new Exception("Usuario no autorizado.");}
    
    if (empty($_POST['actividad']) || empty($_POST['fecha']) || empty($_POST['equnombre']) || empty($_POST['id'])) {
        throw new Exception("Todos los campos obligatorios deben estar completos.");
    }
    $informe = array(
      'ordid' => 0, 
      'equid' => $_POST['id'],
      'cliid' => $_SESSION['gesman']['CliId'],
      'supid'=>$_SESSION['gesman']['PerId'],
      'fecha' => $_POST['fecha'],
      'ordnombre' => null, 
      'clinombre' => $_SESSION['gesman']['CliNombre'],
      'clicontacto' => null, 
      'clidireccion' => null, 
      'supnombre' => $_SESSION['gesman']['Alias'],
      'equnombre' => $_POST['equnombre'], 
      'equmarca' => null, 
      'equmodelo' => null, 
      'equserie' => null, 
      'equdatos' => null, 
      'equkm' => $_POST['equkm'],
      'equhm' => $_POST['equhm'],
      'actividad' => $_POST['actividad'],
      'usuario' => date('Ymd-His') . '(' . $_SESSION['gesman']['Nombre'] . ')'
    );
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $informeId = FnAgregarInforme($conmy, $informe);
    if ($informeId) {
      $data['msg'] = "Registro exitoso.";
      $data['res'] = true;
      $data['id'] = $informeId;
    } else {
       throw new Exception("Error al procesar la solicitud.");
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


