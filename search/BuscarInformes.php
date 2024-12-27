<?php
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/data/InformesData.php";
  $data = array('res' => false, 'pag' => 0, 'msg' => 'Error general.', 'data'=>array());

  try {  
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(!FnValidarSesion()){throw new Exception("Usuario no tiene Autorización.");}
    if (empty($_POST['fechainicial']) || empty($_POST['fechafinal'])) {
      throw new Exception("Las fechas de búsqueda están incompletas.");
    }
    $informe = new stdClass();
    $informe->CliId = $_SESSION['gesman']['CliId'];
    $informe->Nombre = !empty($_POST['nombre']) ? $_POST['nombre'] : null;
    $informe->Equipo = !empty($_POST['equipo']) ? $_POST['equipo'] : 0;
    $informe->FechaInicial = $_POST['fechainicial'];
    $informe->FechaFinal = $_POST['fechafinal'];
    $informe->Pagina = !empty($_POST['pagina']) ? (int)$_POST['pagina'] : 0;

    $informes = FnBuscarInformes($conmy, $informe);
    if ($informes['pag'] > 0) {
      $data['res'] = true;
      $data['msg'] = 'Ok.';
      $data['pag'] = $informes['pag'];
      $data['data'] = $informes['data'];
    } else {  
      $data['msg'] = 'No se encontraron resultados.';
    }
  } catch(PDOException $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy=null;
  } catch (Exception $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy=null;
  }
  echo json_encode($data);
?>
