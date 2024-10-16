<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  $data = array('res' => false, 'msg' => 'Error general.');

  try {
    if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
    if (empty($_POST['id']) && empty($_POST['titulo']) ) {throw new Exception("La información está incompleta.");}
    
    $USUARIO = date('Ymd-His (').$_SESSION['UserName'].')';
    $archivo = new stdClass();
    $archivo->Id = $_POST['id'];
    $archivo->Titulo = empty($_POST['titulo']) ? null : $_POST['titulo'];
    $archivo->Descricion = empty($_POST['descripcion']) ? null : $_POST['descripcion'];
    $archivo->Usuario = $USUARIO;

    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (FnEditarArchivoTituloDescripcion($conmy, $archivo)) {
      $data['msg'] = "Modificación existosa.";
      $data['res'] = true;
    } else {
      $data['msg'] = "Error en la modificanción.";
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
