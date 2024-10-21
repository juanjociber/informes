<?php 
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  $data = array('id' => 0, 'res' => false, 'msg' => 'Error general.');

  try {
    if (empty($_SESSION['CliId']) || empty($_SESSION['UserName'])) {throw new Exception("Usuario no autorizado.");}
    
    if (empty($_POST['actividad']) || empty($_POST['fecha']) || empty($_POST['supervisor']) || empty($_POST['equ_codigo']) || empty($_POST['id'])) {
        throw new Exception("Todos los campos obligatorios deben estar completos.");
    }
    $informe = array(
      'ordid' => 0, 
      'equid' => $_POST['id'],
      'cliid' => $_SESSION['CliId'],
      'fecha' => $_POST['fecha'],
      'ordnombre' => '', 
      'clinombre' => $_SESSION['CliNombre'],
      'clicontacto' => null, 
      'clidireccion' => null, 
      'supervisor' => $_POST['supervisor'],
      'equcodigo' => $_POST['equ_codigo'],
      'equnombre' => null, 
      'equmarca' => null, 
      'equmodelo' => null, 
      'equserie' => null, 
      'equdatos' => null, 
      'equkm' => $_POST['equkm'],
      'equhm' => $_POST['equhm'],
      'actividad' => $_POST['actividad'],
      'usuario' => date('Ymd-His') . '(' . $_SESSION['UserName'] . ')'
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
      
  } catch (PDOException $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy = null;
  } catch (Exception $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy = null;
  }
  echo json_encode($data);
?>


