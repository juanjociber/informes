<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";    
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/data/InformesData.php";
  $data = array('res' => false, 'msg' => 'Error general.');

  try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(!FnValidarSesion()){throw new Exception("Se ha perdido la conexión.");}
    if(!FnValidarSesionManNivel2()){throw new Exception("Usuario no autorizado.");}
    if(empty($_POST['Id'])){throw new Exception("La Información esta incompleta.");}

    $USUARIO = date('Ymd-His (').$_SESSION['gesman']['Nombre'].')';
    $informe = new stdClass();
    $informe->id = $_POST['Id'];
    $informe->equid = $_POST['Equid'];
    $informe->equnombre = empty($_POST['EquNombre']) ? null : $_POST['EquNombre'];
    $informe->equmarca = empty($_POST['EquMarca']) ? null : $_POST['EquMarca'];
    $informe->equmodelo = empty($_POST['EquModelo']) ? null : $_POST['EquModelo'];
    $informe->equserie = empty($_POST['EquSerie']) ? null :$_POST['EquSerie'];
    $informe->equdatos = empty($_POST['EquDatos']) ? null : $_POST['EquDatos'];
    $informe->equreferencia = empty($_POST['EquReferencia']) ? null : $_POST['EquReferencia'];
    $informe->equkm = empty($_POST['EquKm']) ? 0 : $_POST['EquKm'];
    $informe->equhm = empty($_POST['EquHm']) ? 0 : $_POST['EquHm'];
    $informe->actualizacion = $USUARIO;

    if (FnModificarInformeEquipo($conmy, $informe)) {
        $data['msg'] = "Modificación realizada con éxito.";
        $data['res'] = true;
    } else {
        $data['msg'] = "Error al procesar la solicitud.";
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

