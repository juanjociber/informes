<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";

$data = array('res' => false, 'msg' => 'Error general.');

try {
    // Verificar si el usuario está autorizado
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

    // Recorrer las actividades y actualizar la base de datos
    foreach ($input as $actividad) {
        $id = $actividad['id'];
        $infid = !empty($actividad['infid']) ? $actividad['infid'] : null; // Obtener el padre_id
        $tipo = $actividad['tipo']; 
        $estado = 2;
        $creacion = $USUARIO;
        $actualizacion = $USUARIO;

        try {
            // Actualizar tabla de actividades
            $stmt = $conmy->prepare("UPDATE tbldetalleinforme 
                                      SET infid = :infid, tipo = :tipo, actividad = :actividad, 
                                          diagnostico = :diagnostico, trabajos = :trabajos, 
                                          observaciones = :observaciones, estado = :estado,
                                          creacion = :creacion, 
                                          actualizacion = :actualizacion
                                      WHERE id = :id");

            $stmt->execute(array(
                ':id' => $id,
                ':infid' => $infid,
                ':tipo' => $tipo,
                ':actividad' => $actividad['actividad'],
                ':diagnostico' => $actividad['diagnostico'],
                ':trabajos' => $actividad['trabajos'],
                ':observaciones' => $actividad['observaciones'],
                ':estado' => $estado,
                ':creacion' => $creacion,
                ':actualizacion' => $actualizacion
            ));

            if ($stmt->rowCount() == 0) {
                throw new Exception("No se afectaron filas en la actualización de la actividad con ID: $id.");
            }
        } catch (PDOException $ex) {
            // Si ocurre un error en una actividad, continuar con la siguiente
            $data['msg'] = "Error en actividad con ID $id: " . $ex->getMessage();
            continue;
        }

        // Actualizar archivos si existen
        if (!empty($actividad['archivos'])) {
            foreach ($actividad['archivos'] as $archivo) {
                try {
                    $stmtArchivo = $conmy->prepare("UPDATE tblarchivos 
                                                     SET refid = :refid, tabla = :tabla, nombre = :nombre, 
                                                         titulo = :titulo, descripcion = :descripcion, 
                                                         tipo = :tipo,
                                                         creacion = :creacion 
                                                     WHERE id = :id");
                    $stmtArchivo->execute([
                        ':id' => $archivo['id'],
                        ':refid' => $id,
                        ':tabla' => $archivo['tabla'],
                        ':nombre' => $archivo['nombre'],
                        ':titulo' => $archivo['titulo'],
                        ':descripcion' => $archivo['descripcion'],
                        ':tipo' => $archivo['tipo'],
                        ':creacion' => $creacion
                    ]);

                    if ($stmtArchivo->rowCount() == 0) {
                        throw new Exception("No se afectaron filas en la actualización del archivo con ID: " . $archivo['id']);
                    }
                } catch (PDOException $ex) {
                    // Continuar con la siguiente actividad si hay error en archivo
                    $data['msg'] = "Error en archivo con ID " . $archivo['id'] . ": " . $ex->getMessage();
                    continue;
                }
            }
        }

        // Actualizar hijos si existen
        if (!empty($actividad['hijos'])) {
            foreach ($actividad['hijos'] as $hijo) {
                $hijoId = $hijo['id'];
                $hijoInfid = !empty($hijo['infid']) ? $hijo['infid'] : null;

                try {
                    // Actualizar información del hijo
                    $stmtHijo = $conmy->prepare("UPDATE tbldetalleinforme 
                                                  SET infid = :infid, tipo = :tipo, actividad = :actividad, 
                                                      diagnostico = :diagnostico, trabajos = :trabajos, 
                                                      observaciones = :observaciones, estado = :estado, 
                                                      creacion = :creacion,
                                                      actualizacion = :actualizacion                                                  
                                                  WHERE id = :id");

                    $stmtHijo->execute([
                        ':id' => $hijoId,
                        ':infid' => $hijoInfid,
                        ':tipo' => 'hijo',
                        ':actividad' => $hijo['actividad'],
                        ':diagnostico' => $hijo['diagnostico'],
                        ':trabajos' => $hijo['trabajos'],
                        ':observaciones' => $hijo['observaciones'],
                        ':estado' => $estado,
                        ':creacion' => $creacion,
                        ':actualizacion' => $actualizacion
                    ]);

                    if ($stmtHijo->rowCount() == 0) {
                        throw new Exception("No se afectaron filas en la actualización del hijo con ID: $hijoId");
                    }
                } catch (PDOException $ex) {
                    // Continuar con el siguiente hijo si hay error
                    $data['msg'] = $ex->getMessage();
                    continue;
                }
            }
        }
    }

    $data['msg'] = "Modificación realizada con éxito.";
    $data['res'] = true;

} catch (Exception $ex) {
    $data['msg'] = $ex->getMessage();
}

echo json_encode($data);
?>

