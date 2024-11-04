<?php 
    session_start();
	$res=false;
    $id=0;
	$msg='Error general creando el Informe.';
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/OrdenesData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/EquiposData.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ClientesData.php";

    try {
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!FnValidarSesion()){throw new Exception("Usuario no tiene Autorización.");}

        $orden=FnBuscarOrden($conmy, $_POST['ordid']);
        if(empty($orden->id)){ throw new Exception("No se encontró la Orden."); }
        
        $equipo=FnBuscarEquipo($conmy, $orden->equid);
        if(empty($orden->id)){ throw new Exception("No se encontró el Equipo."); }
        
        $cliente=FnBuscarCliente($conmy, $orden->cliid);
        if(empty($orden->id)){ throw new Exception("No se encontró el Cliente."); }

        $usuario=date('Ymd-His (').$_SESSION['gesman']['Nombre'].')';

        $id=FnRegistrarInforme($conmy, $orden, $cliente, $equipo, $_POST['fecha'], $_POST['actividad'], $usuario);
        if($id>0){
            $res=true;
            $msg='Se generó el Informe';
        }else{
            throw new Exception("Error generando el Informe.");  
        }
        $conmy=null;
    } catch(PDOException $ex){
        $msg=$ex->getMessage();
        $conmy=null;
    } catch (Exception $ex) {
        $msg=$ex->getMessage();
        $conmy=null;
    }
    echo json_encode(array('res'=>$res, 'id'=>$id, 'msg'=>$msg));
?>