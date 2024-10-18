<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  $data = array('res' => false, 'msg' => 'Error general.');

  try {
    if (empty($_SESSION['CliId']) && empty($_SESSION['UserName'])) { throw new Exception("Usuario no tiene Autorización."); }
    if (empty($_POST['id'])) { throw new Exception("La información está incompleta."); }

    $USUARIO = date('Ymd-His ('.$_SESSION['UserName'].')');
    // OBTENER ARCHIVO EXISTENTE
    $archivoExistente = FnBuscarArchivoTituloDescripcion($conmy, $_POST['id']);
    if (!$archivoExistente) {
      throw new Exception("El archivo no existe.");
    }
    // INICIALIZAR NOMBRE EXISTENTE
    $FileName = $archivoExistente->nombre;

    // VERIFICAR SI SE PASA NUEVO ARCHIVO
    if (!empty($_FILES['archivo']['name'])) { 
      $FileName ='INFD'.'_'.$_POST['id'].'_'.uniqid().'.jpeg'; 
      move_uploaded_file($_FILES['archivo']['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/files/".$FileName);
    }

    $archivo = new stdClass();
    $archivo->Id = $_POST['id'];
    $archivo->Titulo = $_POST['titulo'];
    $archivo->Descripcion = empty($_POST['descripcion']) ? null : $_POST['descripcion'];
    $archivo->nombre = $FileName; 
    $archivo->Usuario = $USUARIO; 

    if (FnModificarArchivoImagenTituloDescripcion($conmy, $archivo)) {
      $data['msg'] = "Modificación realizada con éxito.";
      $data['res'] = true;
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
