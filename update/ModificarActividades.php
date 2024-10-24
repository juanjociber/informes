<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";

if (isset($_POST['update'])) {
  foreach($_POST['positions'] as $position) {
    $index = $position[0];
    $newPosition = $position[1];

    $UpdatePosition = ("UPDATE tbldetalleinforme SET posicion = '$newPosition' WHERE id='$index' ");
    $result = mysqli_query($con, $UpdatePosition);
    print_r($UpdatePosition);
  }
}
?>

