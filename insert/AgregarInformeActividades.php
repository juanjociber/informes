<?php 
session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
require_once $_SERVER['DOCUMENT_ROOT']."/informes/data/InformesData.php";
$data = array('res' => false, 'msg' => 'Error general.');

try {
    // if (empty($_SESSION['CliId']) && empty($_SESSION['UserName'])) { 
    //     throw new Exception("Usuario no tiene Autorización."); 
    // }
    if(!FnValidarSesion()){throw new Exception("Usuario no tiene Autorización.");}
    if (empty($_POST['infid']) || empty($_POST['actividad']) || empty($_POST['tipo'])) { 
        throw new Exception("La información está incompleta.");
    }
    if (strlen($_POST['actividad']) > 500) {
        throw new Exception("El campo solo permite 500 caracteres.");
    }

    $USUARIO = date('Ymd-His').'('.$_SESSION['gesman']['Nombre'].')';
    $actividad = new stdClass();
    $actividad->infid = $_POST['infid'];
    $actividad->ownid = empty($_POST['ownid']) ? 0 : $_POST['ownid'];
    $actividad->orden = empty($_POST['orden']) ? 0 : $_POST['orden'];
    $actividad->actividad = $_POST['actividad'];
    $actividad->diagnostico = empty($_POST['diagnostico']) ? null : $_POST['diagnostico'];
    $actividad->trabajos = empty($_POST['trabajos']) ? null : $_POST['trabajos'];
    $actividad->observaciones = empty($_POST['observaciones']) ? null : $_POST['observaciones'];
    $actividad->tipo = $_POST['tipo']; 
    $actividad->usuario = $USUARIO;

    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (FnRegistrarInformeActividades($conmy, $actividad)) {
        $data['msg'] = "Registro exitoso.";
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


