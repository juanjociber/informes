<?php 
session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";

// Inicializar la respuesta por defecto
$data = array('res' => false, 'msg' => 'Error general.');

try {
    // Verificar la sesión del usuario
    if (empty($_SESSION['CliId']) || empty($_SESSION['UserName'])) { 
        throw new Exception("Usuario no tiene Autorización."); 
    }

    // Validar la información recibida
    if (empty($_POST['infid']) || empty($_POST['actividad'])) { 
        throw new Exception("La información está incompleta.");
    }
    if (strlen($_POST['actividad']) > 500) {
        throw new Exception("El campo solo permite 500 caracteres.");
    }

    // Crear objeto actividad
    $USUARIO = date('Ymd-His ('.$_SESSION['UserName'].')');
    $actividad = new stdClass();
    $actividad->infid = $_POST['infid'];
    $actividad->ownid = $_POST['ownid'];
    $actividad->orden = empty($_POST['orden']) ? 0 : $_POST['orden']; 
    $actividad->tipo = $_POST['tipo']; 
    $actividad->actividad = $_POST['actividad'];
    $actividad->diagnostico = empty($_POST['diagnostico']) ? null : $_POST['diagnostico'];
    $actividad->trabajos = empty($_POST['trabajos']) ? null : $_POST['trabajos'];
    $actividad->observaciones = empty($_POST['observaciones']) ? null : $_POST['observaciones'];
    $actividad->usuario = $USUARIO;

    // Configurar el manejo de errores de PDO
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Llamar a la función para registrar la actividad
    if (FnRegistrarInformeActividades($conmy, $actividad)) {
        $data['msg'] = "Registro exitoso.";
        $data['res'] = true;
    } else {
        $data['msg'] = "Error al procesar la solicitud.";
    }
} catch (PDOException $ex) {
    $data['msg'] = "Error de base de datos: " . $ex->getMessage();
    $conmy = null;
} catch (Exception $ex) {
    $data['msg'] = $ex->getMessage();
    $conmy = null;
}


echo json_encode($data);
?>
