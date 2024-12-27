<?php 
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/data/InformesData.php";
  $data = array('res' => false,'msg' => 'Error general.', 'data'=>null);
  
  try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(!FnValidarSesion()){throw new Exception("Usuario no tiene Autorización.");}
    // if(empty($_SESSION['CliId']) && empty($_SESSION['UserName'])){throw new Exception("Usuario no tiene Autorización.");}
    if(empty($_POST['id'])){ throw new Exception("La informacion esta incompleta."); }
  
    $actividad = FnBuscarInformeActividad($conmy, $_POST['id']);    
    if ($actividad) {
      $data['res'] = true;
      $data['msg'] = 'Ok.';
      $data['data'] = $actividad;
    } else {
      $data['msg'] = 'No existen registros en la base de datos.';
    }
    $conmy = null;
  } catch(PDOException $ex){
      $data['msg'] = $ex->getMessage();
      $conmy = null;
  } catch (Exception $ex) {
      $data['msg'] = $ex->getMessage();
      $conmy = null;
  } 
  echo json_encode($data);
?>


