<?php
  session_start();    
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  $data = array('res' => false, 'msg' => 'Error general.');

  try {
    if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
    if(empty($_POST['Id'])){throw new Exception("La Información esta incompleta.");}

    $USUARIO = date('Ymd-His (').$_SESSION['UserName'].')';
    $informe = new stdClass();
    $informe->id = $_POST['Id'];
    $informe->equnombre = empty($_POST['EquNombre']) ? null : $_POST['EquNombre'];
    $informe->equmarca = empty($_POST['EquMarca']) ? null : $_POST['EquMarca'];
    $informe->equmodelo = empty($_POST['EquModelo']) ? null : $_POST['EquModelo'];
    $informe->equserie = empty($_POST['EquSerie']) ? null :$_POST['EquSerie'];
    $informe->equdatos = empty($_POST['EquDatos']) ? null : $_POST['EquDatos'];
    $informe->equkm = empty($_POST['EquKm']) ? 0 : $_POST['EquKm'];
    $informe->equhm = empty($_POST['EquHm']) ? 0 : $_POST['EquHm'];
    $informe->actualizacion = $USUARIO;

    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (FnModificarInformeEquipo($conmy, $informe)) {
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

