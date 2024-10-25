<?php
session_start();
if (!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])) {
  header("location:/gesman");
  exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/gesman/connection/ConnGesmanDb.php";
$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!empty($_POST['update'])) {
  foreach ($_POST['positions'] as $position) {
    $index = $position[0];
    $newPosition = $position[1];

    $UpdatePosition = $conmy->prepare("UPDATE tbldetalleinforme SET posicion = :newPosition WHERE id = :index");
    $UpdatePosition->bindParam(':newPosition', $newPosition, PDO::PARAM_INT);
    $UpdatePosition->bindParam(':index', $index, PDO::PARAM_INT);
    $UpdatePosition->execute();
  }
  echo "Posiciones actualizadas correctamente.";
} else {
  echo "No se recibieron datos de actualización.";
}
?>



