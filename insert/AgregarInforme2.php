<?php 
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  $data = array('id'=>0,'res' => false, 'msg' => 'Error general.');

  try {
    if (empty($_SESSION['CliId']) || empty($_SESSION['UserName'])) {throw new Exception("Usuario no autorizado.");}
    
    if (empty($_POST['actividad']) || empty($_POST['fecha']) || empty($_POST['supervisor']) || empty($_POST['equ_codigo']) || empty($_POST['id'])) {
      throw new Exception("Todos los campos obligatorios deben estar completos.");
    }
    $usuario = date('Ymd-His').'('.$_SESSION['UserName'].')';
    $id = $_POST['id'];
    $equkm = $_POST['equkm'];
    $equhm = $_POST['equhm'];
    // CREANDO OBJETOS
    $orden = new stdClass();
    $orden->id = 0;
    $orden->nombre = '';
    $orden->contacto = null;
    $orden->km = $equkm;
    $orden->hm = $equhm;
    $orden->supervisor = $_POST['supervisor'];

    $equipo = new stdClass();
    $equipo->id = $id;
    $equipo->codigo = $_POST['equ_codigo'];
    $equipo->nombre = null;
    $equipo->marca = null;
    $equipo->modelo = null;
    $equipo->serie = null;
    $equipo->caracteristicas = null;
    $equipo->cli_direccion = null;

    $cliente = new stdClass();
    $cliente->id = $_SESSION['CliId']; 
    $cliente->nombre = $_SESSION['CliNombre'] ;

    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $informeId = FnRegistrarInforme($conmy, $orden, $cliente, $equipo, $_POST['fecha'], $_POST['actividad'], $usuario);
    if ($informeId) {
      $data['msg'] = "Registro existoso.";
      $data['res'] = true;
      $data['id'] = $informeId;
    } else {
      throw new Exception("Error al procesar la solicitud.");
    }
  } catch(PDOException $ex){
      $data['msg']=$ex->getMessage();
      $conmy=null;
  } catch (Exception $ex) {
      $data['msg']=$ex->getMessage();
      $conmy=null;
  }
  echo json_encode($data);
?>

