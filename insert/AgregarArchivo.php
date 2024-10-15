<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  $data = array('res' => false, 'msg' => 'Error general.');

  try {
    if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
    $tabla = $_POST['tabla'];

    if (!in_array($tabla, array('INFE', 'INFA', 'INFD'))) {throw new Exception('Tabla no válida.');}

    $USUARIO = date('Ymd-His (').$_SESSION['UserName'].')';
    $FileName = $tabla.'_'.$_POST['refid'].'_'.uniqid().'.jpeg';
    $FileEncoded = str_replace("data:image/jpeg;base64,", "", $_POST['archivo']);
    $FileDecoded = base64_decode($FileEncoded);
    file_put_contents($_SERVER['DOCUMENT_ROOT']."/mycloud/gesman/files/".$FileName, $FileDecoded);

    $imagen = new stdClass();
    $imagen->refid = $_POST['refid'];
    $imagen->tabla = $tabla;
    $imagen->nombre = $FileName;
    $imagen->titulo = $_POST['titulo'];
    $imagen->descripcion = $_POST['descripcion'];
    $imagen->usuario = $USUARIO;
    $imagen->tipo = 'IMG';

    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (FnRegistrarArchivo($conmy, $imagen)) {
        $data['msg'] = "Archivo cargado con éxito.";
        $data['res'] = true;        
    } else {
        $data['msg'] = "Error registrando el Archivo.";
    }
  } catch (PDOException $ex) {
    $msg = $ex->getMessage();
  } catch (Exception $ex) {
    $msg = $ex->getMessage();
  }
  $conmy = null;
  echo json_encode($data);
?>
