<?php
  session_start();
  if (!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])) {
    header("location:/gesman");
    exit();
  }
  require_once $_SERVER['DOCUMENT_ROOT'] . "/gesman/connection/ConnGesmanDb.php";
  $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $data = json_decode(file_get_contents("php://input"), true);
  $orden = $data['orden'] ?? [];
  $response = array('success' => false); 
  try {
    $conmy->beginTransaction(); 
    foreach ($orden as $pos => $id) {
      if (is_numeric($id)) { 
        $stmt = $conmy->prepare("UPDATE tbldetalleinforme SET orden = ? WHERE id = ?");
        $stmt->execute(array($pos + 1, $id));
      }
    }
    $conmy->commit(); 
    $response['success'] = true; 
  } catch (Exception $e) {
    $conmy->rollBack(); 
    $response['error'] = $e->getMessage(); 
  }
  header('Content-Type: application/json');
  echo json_encode($response);
?>





