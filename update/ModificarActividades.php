<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/gesman/data/SesionData.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/gesman/connection/ConnGesmanDb.php";

if (!FnValidarSesion()) { responseError('Se ha perdido la conexión.'); }

try {
  $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // Leer y decodificar JSON
  $data = json_decode(file_get_contents("php://input"), true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    responseError(json_last_error_msg());
  }
  // Validar estructura del JSON
  if (!isset($data['orden']) || !is_array($data['orden'])) {
    responseError('La estructura de datos es incorrecta.');
  }
  // Procesar el orden
  $orden = $data['orden'];
  $conmy->beginTransaction();

  $stmt = $conmy->prepare("UPDATE tbldetalleinforme SET orden = ? WHERE id = ?");
  foreach ($orden as $pos => $id) {
    if (is_numeric($id)) {
      $stmt->execute(array($pos + 1, $id));
    }
  }
  $conmy->commit();
  responseSuccess();

} catch (Exception $e) {
  if ($conmy->inTransaction()) {
    $conmy->rollBack();
  }
  responseError($e->getMessage());
}
// Funciones auxiliares para manejar respuestas
function responseSuccess($data = array())
{
  header('Content-Type: application/json');
  echo json_encode(array_merge(array('success' => true), $data));
  exit;
}
function responseError($message)
{
  header('Content-Type: application/json');
  echo json_encode(array('success' => false, 'error' => $message));
  exit;
}
?>
