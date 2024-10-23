<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";

$data = array('res' => false, 'msg' => 'Error general.');

try {
    if (empty($_SESSION['CliId']) && empty($_SESSION['UserName'])) {
        throw new Exception("Usuario no tiene Autorización.");
    }

    // Leer datos del JSON
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input)) {
        echo json_encode(array('res' => false, 'msg' => 'Datos incompletos para enviar al servidor.'));
        exit;
    }

    $USUARIO = date('Ymd-His (') . $_SESSION['UserName'] . ')';
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Iniciar transacción
    $conmy->beginTransaction();

    // Recorrer las actividades y actualizar la base de datos
    foreach ($input as $actividad) {
        $id = $actividad['id'];
        $infid = !empty($actividad['infid']) ? $actividad['infid'] : null; // Obtener el padre_id
        $tipo = 'actividad';
        $estado = 'activo';
        $actualizacion = date('Y-m-d H:i:s');

        // Actualizar tabla de actividades
        $stmt = $conmy->prepare("UPDATE tbldetalleinforme 
                                  SET infid = :infid, tipo = :tipo, actividad = :actividad, 
                                      diagnostico = :diagnostico, trabajos = :trabajos, 
                                      observaciones = :observaciones, estado = :estado, 
                                      actualizacion = :actualizacion
                                  WHERE id = :id");

        $stmt->execute([
            ':id' => $id,
            ':infid' => $infid,
            ':tipo' => $tipo,
            ':actividad' => $actividad['actividad'],
            ':diagnostico' => $actividad['diagnostico'],
            ':trabajos' => $actividad['trabajos'],
            ':observaciones' => $actividad['observaciones'],
            ':estado' => $estado,
            ':actualizacion' => $actualizacion
        ]);

        // Manejar archivos (si hay)
        if (!empty($actividad['archivos'])) {
            foreach ($actividad['archivos'] as $archivo) {
                $stmtArchivo = $conmy->prepare("UPDATE tblarchivos 
                                                 SET actualizacion = :actualizacion 
                                                 WHERE id = :id AND refid = :refid");
                $stmtArchivo->execute([
                    ':id' => $archivo['id'],
                    ':refid' => $id,
                    ':actualizacion' => $actualizacion
                ]);
            }
        }
    }

    $conmy->commit();
    $data['msg'] = "Datos actualizados exitosamente.";
    $data['res'] = true;
} catch (PDOException $ex) {
    if ($conmy->inTransaction()) {
        $conmy->rollBack();
    }
    $data['msg'] = $ex->getMessage();
} catch (Exception $ex) {
    if ($conmy->inTransaction()) {
        $conmy->rollBack();
    }
    $data['msg'] = $ex->getMessage();
}

echo json_encode($data);
?>
