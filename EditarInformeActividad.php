<?php 
  session_start();
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/SesionData.php";
  
  if(!FnValidarSesion()){
    header("location:/gesman/Salir.php");
    exit();
  }

  if(!FnValidarSesionManNivel3()){
    header("HTTP/1.1 403 Forbidden");
    exit();
  }

  if(empty($_GET['id'])){
    header("HTTP/1.1 404 Not Found");
    exit();
  }

  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";

  $ID = empty($_GET['id'])?0:$_GET['id'];
  $CLI_ID = $_SESSION['gesman']['CliId'];
  $isAuthorized = false;
  $errorMessage = '';		
  $Nombre='';
	$ClienteNombre='';
  $Estado=0;
  $claseHabilitado = "btn-outline-secondary";
  $atributoHabilitado = " disabled";
  $tablaHTML ='';

	function construirArbol($registros, $padreId = 0) {
		$arbol = array();
		foreach ($registros as $registro) {
			if ($registro['ownid'] == $padreId) {
				$hijos = construirArbol($registros, $registro['id']);
				if (!empty($hijos)) {
					$registro['hijos'] = $hijos;
				}
				$arbol[] = $registro;
			}
		}			
		return $arbol;
	}

	function FnGenerarInformeHtmlAcordeon($arbol, $imagenes, $nivel = 0, $indice ='1') {
		$html='';
		$contador=1;		
		foreach ($arbol as $key=>$nodo) {
			$indiceActual = $nivel==0?$contador++:$indice.'.'.($key+1);
			$html.='
        <div class="accordion-item" id="'.$nodo['id'].'" data-id="'.$nodo['id'].'">
          <div class="accordion-header" id="accordion-header-'.$nodo['id'].'">
            <div class="contenedor-actividades bg-light mb-2 cabecera-actividad--mod" style="margin:0 auto;">
              <div class="d-flex p-1 cabecera-actividad bg-light">
                <label class="text-secondary bg-light" style="font-size:17px !important">'.$indiceActual. '<span>&nbsp;-</span></label>
                <p class="accordion-button p-0 bg-light" data-bs-toggle="collapse" style="cursor:pointer; border: unset; box-shadow: none; font-size:17px !important; text-align:justify;" data-bs-target="#collapse-accordion-'.$nodo['id'].'" aria-expanded="true" aria-controls="collapse-accordion-'.$contador.'"><span>&nbsp;</span>'.$nodo['actividad'].'</p>
              </div>
              <div class="grid-icono input-grop-icons d-flex p-0">
                <!--AGREGAR ACTIVIDAD-->
                <span class="input-group-text input-group--mod bg-light border border-0 text-muted" style="cursor:pointer;">            
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="28px" height="33px" viewBox="0 0 554.625 554.625" style="enable-background:new 0 0 554.625 554.625;" xml:space="preserve" onclick="FnModalAgregarInformeActividades('.$nodo['id'].')">
                    <title>Actividad</title>
                    <g fill="#6B6C6E" stroke="#6B6C6E" stroke-width="8">
                      <g>
                        <polygon points="392.062,439.875 392.062,382.5 372.938,382.5 372.938,439.875 315.562,439.875 315.562,459 372.938,459     372.938,516.375 392.062,516.375 392.062,459 449.438,459 449.438,439.875   "/>
                        <path d="M430.312,355.725V143.438v-9.562L315.562,0H306H105.188c-21.038,0-38.25,17.212-38.25,38.25v439.875    c0,21.037,17.212,38.25,38.25,38.25h196.987c19.125,22.95,47.812,38.25,80.325,38.25c57.375,0,105.188-47.812,105.188-105.188    C487.688,409.275,464.737,372.938,430.312,355.725z M315.562,28.688l89.888,105.188h-70.763c-9.562,0-19.125-9.562-19.125-19.125    V28.688z M105.188,497.25c-9.562,0-19.125-7.65-19.125-19.125V38.25c0-9.562,7.65-19.125,19.125-19.125h191.25v95.625    c0,21.038,17.213,38.25,38.25,38.25h76.5v195.075c-9.562-1.913-19.125-3.825-28.688-3.825    c-57.375,0-105.188,47.812-105.188,105.188c0,17.213,3.825,32.513,11.475,47.812H105.188z M382.5,535.5    c-47.812,0-86.062-38.25-86.062-86.062s38.25-86.062,86.062-86.062s86.062,38.25,86.062,86.062S430.312,535.5,382.5,535.5z"/>
                      </g>
                    </g>
                  </svg>
                </span>
                <!--EDITAR-->
                <span class="input-group-text input-group--mod bg-light border border-0 text-secondary" style="cursor:pointer;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 59 64" onclick="FnModalModificarInformeActividades('.$nodo['id'].')">
                    <title>Editar</title>
                    <g fill="none" stroke="#6B6C6E" stroke-width="3">
                      <path d="M47,45v15c0,1.1-0.9,2-2,2H2c-1.1,0-2-0.9-2-2V2c0-1.1,0.9-2,2-2h25.9L47,18.1V33"/>
                      <path d="M47,18.9H30c-1.1,0-2-0.9-2-2V1"/>
                      <path d="M9,17h13"/>
                      <path d="M9,27h31"/>
                      <path d="M9,34h31"/>
                      <path d="M9,43h24"/>
                      <path d="M9,49h17"/>
                      <g transform="translate(27, 29)">
                        <path stroke-linejoin="round" d="M0,30l3.9-9.4L24.2,0.3c0.4-0.4,1.1-0.4,1.6,0l3.9,3.9c0.4,0.4,0.4,1.1,0,1.6L9.4,26.1L0,30z"/>
                        <path d="M21.9,2.7l5.4,5.4"/>
                      </g>
                    </g>
                  </svg>
                </span>
                <!--AGREGAR ARCHIVO-->
                <span class="input-group-text input-group--mod bg-light border border-0 text-secondary" style="cursor:pointer;">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" width="23px" height="28px" viewBox="0 0 59 63" version="1.1" onclick="FnModalAgregarArchivo('.$nodo['id'].')">
                    <title>Archivo</title>
                    <desc>Created with Sketch.</desc>
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                      <path d="M36.3,46.5 L12.1,22.4 C9.4,19.7 9.1,15.6 11.8,12.9 L11.8,12.9 C14.5,10.2 19.1,10.1 21.8,12.8 L53.6,44.4 C57.8,48.6 58.2,54.9 54.5,58.5 L54.5,58.5 C50.8,62.1 44.5,61.7 40.3,57.6 L4.7,22.1 C-0.1,17.4 -0.2,9.8 4.3,5.3 L5.3,4.3 C9.8,-0.2 17.4,-0.1 22.2,4.7 L46,28.4" id="Archivo" stroke="#6B6C6E" stroke-width="2" sketch:type="MSShapeGroup"/>
                    </g>
                  </svg>
                </span>
                <!--ELIMINAR-->
                <span class="input-group-text input-group--mod bg-light border border-0 text-secondary" style="cursor:pointer;">      
                  <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 300 343" onclick="FnEliminarInformeActividades('.$nodo['id'].')">
                    <title>Eliminar</title>
                    <g fill="none" stroke="#6B6C6E" stroke-width="7">
                      <path d="M86 2.6c-6.2 2.2-11.7 6.4-15.7 12-4.2 6-5.3 10-5.3 19.5 0 4.9-.4 8-1.3 8.7-.7.6-9.3 1.2-21.2 1.4-20 .3-20 .3-25.3 3.2C10.7 51 6.5 55.6 3.1 63 .6 68.2.5 69.3.5 85.5c0 15.7.2 17.3 2.2 21.2 2.9 5.3 8.1 9.3 14.1 10.8l4.7 1.2.5 95.9c.5 104.8.2 98.4 6.3 108 3.2 5.2 11.4 12.7 16.7 15.4 9.5 4.9 11.5 5 104.6 5 58.8 0 89-.4 93-1.1 16.4-3 29.7-15.2 34-31.3 1.1-4.1 1.4-22.6 1.4-98.9V118h2.3c3.4-.1 10.4-3.6 13.3-6.7 4.7-5.1 5.4-8.3 5.4-25.3 0-20.4-1.1-24.2-9.5-32.6-8.5-8.6-11.3-9.3-35-9.7l-19-.2-.6-10c-.4-8.1-1-11-3.1-15-3.1-6-8.6-11.4-14.8-14.6l-4.5-2.4-61-.2c-53.4-.2-61.6 0-65.5 1.3zM211.6 25.4c3.4 3.4 3.5 3.6 3.2 10.8l-.3 7.3-63.7.2L86 44v-7.8c0-8.1 1.1-10.6 5.5-13 1.4-.8 19.4-1.1 58.8-1.2h56.9l3.4 3.4zM257 210.4c0 101 .2 97.6-5.8 103.9-1.5 1.6-4.5 3.9-6.7 5l-4 2.2H59.4l-4.9-2.6c-3.5-1.8-5.9-3.9-8-7.4l-3-4.8-.3-94.4L43 118h214v92.4z"/>
                      <path d="M67.9 142.9l-2.9 2.9v26.4c-.1 95 .1 121.1 1 122.9 2.3 4.2 6.3 5.1 21.1 4.7 13.6-.3 13.9-.3 16.6-3.1l2.8-2.7.3-73.6.2-73.6-3.4-3.4-3.4-3.4H70.8l-2.9 2.9zM96 220v70.1l-10.2-.3-10.3-.3-.3-69.8L75 150h21v70zm35.9-77.1l-2.9 2.9v73.9c.1 56.5.3 74.2 1.3 75.3 3.5 4.5 5.7 5 20 5h13.9l3.4-3.4 3.4-3.4v-72.6c0-78.9.1-76.8-5.5-79.4-1.6-.8-7.9-1.2-16.6-1.2h-14.1l-2.9 2.9zm28.6 77.1v69.5h-21l-.3-69.8-.2-69.8 10.7.3 10.8.3V220zm37.6-78.6c-5.2 2.9-5 .9-5.1 79v72.8l3.4 3.4 3.4 3.4h28.4l3.4-3.4 3.4-3.4v-73.3c0-68-.2-73.6-1.8-75.5-.9-1.2-2.7-2.7-3.9-3.3-3.2-1.6-28.1-1.4-31.2.3zm26.4 78.6v69.5h-21l-.3-68.5c-.1-37.7 0-69.1.3-69.8.3-.9 3.1-1.2 10.7-1l10.3.3V220z"/>
                    </g>
                  </svg>
                </span>
              </div>
            </div>
          </div>
          <div id="collapse-accordion-'.$nodo['id'].'" class="accordion-collapse collapse show" aria-labelledby="accordion-header-'.$nodo['id'].'" style="border:0.5px solid #e3dede; margin-bottom:30px; margin-top:-7px;">
            <div class="accordion-body" style="padding-left:10px !important; padding-right: 10px !important">
              <div class="row mb-2">
                <div class="col-12 mb-1">
                  <p class="m-0 text-secondary fw-bold">Diagnóstico</p>
                  <p class="mb-1 text-secondary diagnostico" style="font-size=15px; text-align:justify; line-height: 1.2;" id="diagnostico-'.$nodo['id'].'">'.$nodo['diagnostico'].'</p>
                </div>
                <div class="col-12 mb-1">
                  <p class="m-0 text-secondary fw-bold">Trabajos</p>
                  <p class="mb-1 text-secondary trabajo" style="font-size=15px; text-align:justify; line-height: 1.2;" id="trabajo-'.$nodo['id'].'">'.$nodo['trabajos'].'</p>
                </div>
                <div class="col-12">
                  <p class="m-0 text-secondary fw-bold">Observaciones</p>
                  <p class="mb-1 text-secondary observacion" style="font-size=15px; text-align:justify; line-height: 1.2;" id="observacion-'.$nodo['id'].'">'.$nodo['observaciones'].'</p>
                </div>
              </div>
              <div class="contenedor-imagen mb-3" id="'.$nodo['id'].'">';
              // <div class="archivo-container" id="archivo">;
                if(isset($imagenes[$nodo['id']])){
                  foreach($imagenes[$nodo['id']] as $elemento){
                    $html.='
                    <div class="d-flex flex-column" id="'.$elemento['id'].'">
                      <div class="d-flex justify-content-end align-items-center text-secondary">
                        <!--BOTON EDITAR-->  
                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" onclick="FnModalModificarArchivo('.$elemento['id'].')" style="font-size:25px; cursor:pointer; padding:10px">
                          <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 59 64">
                            <g fill="none" stroke="#6B6C6E" stroke-width="3">
                              <path d="M47,45v15c0,1.1-0.9,2-2,2H2c-1.1,0-2-0.9-2-2V2c0-1.1,0.9-2,2-2h25.9L47,18.1V33"/>
                              <path d="M47,18.9H30c-1.1,0-2-0.9-2-2V1"/>
                              <path d="M9,17h13"/>
                              <path d="M9,27h31"/>
                              <path d="M9,34h31"/>
                              <path d="M9,43h24"/>
                              <path d="M9,49h17"/>
                              <g transform="translate(27, 29)">
                                <path stroke-linejoin="round" d="M0,30l3.9-9.4L24.2,0.3c0.4-0.4,1.1-0.4,1.6,0l3.9,3.9c0.4,0.4,0.4,1.1,0,1.6L9.4,26.1L0,30z"/>
                                <path d="M21.9,2.7l5.4,5.4"/>
                              </g>
                            </g>
                          </svg>
                        </span>
                        <!--BOTON ELIMINAR-->
                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar" onclick="FnEliminarArchivo('.$elemento['id'].', '.$elemento['refid'].' )" style="font-size:25px; cursor:pointer; padding:10px">
                          <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 300 343">
                            <g fill="none" stroke="#6B6C6E" stroke-width="7">
                              <path d="M86 2.6c-6.2 2.2-11.7 6.4-15.7 12-4.2 6-5.3 10-5.3 19.5 0 4.9-.4 8-1.3 8.7-.7.6-9.3 1.2-21.2 1.4-20 .3-20 .3-25.3 3.2C10.7 51 6.5 55.6 3.1 63 .6 68.2.5 69.3.5 85.5c0 15.7.2 17.3 2.2 21.2 2.9 5.3 8.1 9.3 14.1 10.8l4.7 1.2.5 95.9c.5 104.8.2 98.4 6.3 108 3.2 5.2 11.4 12.7 16.7 15.4 9.5 4.9 11.5 5 104.6 5 58.8 0 89-.4 93-1.1 16.4-3 29.7-15.2 34-31.3 1.1-4.1 1.4-22.6 1.4-98.9V118h2.3c3.4-.1 10.4-3.6 13.3-6.7 4.7-5.1 5.4-8.3 5.4-25.3 0-20.4-1.1-24.2-9.5-32.6-8.5-8.6-11.3-9.3-35-9.7l-19-.2-.6-10c-.4-8.1-1-11-3.1-15-3.1-6-8.6-11.4-14.8-14.6l-4.5-2.4-61-.2c-53.4-.2-61.6 0-65.5 1.3zM211.6 25.4c3.4 3.4 3.5 3.6 3.2 10.8l-.3 7.3-63.7.2L86 44v-7.8c0-8.1 1.1-10.6 5.5-13 1.4-.8 19.4-1.1 58.8-1.2h56.9l3.4 3.4zM257 210.4c0 101 .2 97.6-5.8 103.9-1.5 1.6-4.5 3.9-6.7 5l-4 2.2H59.4l-4.9-2.6c-3.5-1.8-5.9-3.9-8-7.4l-3-4.8-.3-94.4L43 118h214v92.4z"/>
                              <path d="M67.9 142.9l-2.9 2.9v26.4c-.1 95 .1 121.1 1 122.9 2.3 4.2 6.3 5.1 21.1 4.7 13.6-.3 13.9-.3 16.6-3.1l2.8-2.7.3-73.6.2-73.6-3.4-3.4-3.4-3.4H70.8l-2.9 2.9zM96 220v70.1l-10.2-.3-10.3-.3-.3-69.8L75 150h21v70zm35.9-77.1l-2.9 2.9v73.9c.1 56.5.3 74.2 1.3 75.3 3.5 4.5 5.7 5 20 5h13.9l3.4-3.4 3.4-3.4v-72.6c0-78.9.1-76.8-5.5-79.4-1.6-.8-7.9-1.2-16.6-1.2h-14.1l-2.9 2.9zm28.6 77.1v69.5h-21l-.3-69.8-.2-69.8 10.7.3 10.8.3V220zm37.6-78.6c-5.2 2.9-5 .9-5.1 79v72.8l3.4 3.4 3.4 3.4h28.4l3.4-3.4 3.4-3.4v-73.3c0-68-.2-73.6-1.8-75.5-.9-1.2-2.7-2.7-3.9-3.3-3.2-1.6-28.1-1.4-31.2.3zm26.4 78.6v69.5h-21l-.3-68.5c-.1-37.7 0-69.1.3-69.8.3-.9 3.1-1.2 10.7-1l10.3.3V220z"/>
                            </g>
                          </svg>
                        </span>
                      </div>
                      <div class="card text-center p-0" id="archivo-'.$elemento['id'].'">
                        <input type="hidden" id="txtRefid" value="'.$elemento['refid'].'">
                        <input type="hidden" id="txtTabla" value="'.$elemento['tabla'].'">
                        <input type="hidden" id="txtTipoArchivo" value="'.$elemento['tipo'].'">
                        <div class="card-header text-secondary" style="text-align:justify;padding-left:5px;" id="tituloArchivo">'.$elemento['titulo'].'
                        </div>
                        <div class="card-body p-0">
                          <img src="/mycloud/gesman/files/'.$elemento['nombre'].'" class="imagen-ajustada" alt="" id="imagenArchivo">
                        </div>
                        <div class="card-footer text-secondary" style="text-align:justify;padding-left:5px;" id="descripcionArchivo">'.$elemento['descripcion'].'
                        </div>
                      </div>
                    </div>
                  ';
                }
              }
              $html.='
              </div>';
              // </div>;
          if (!empty($nodo['hijos'])) {
            $html.='
              <div class="accordion" id="accordion-container-'.$nodo['id'].'">';
                $html.=FnGenerarInformeHtmlAcordeon($nodo['hijos'], $imagenes, $nivel+1, $indiceActual );
            $html.='
              </div>
               ';  
          }
          $html.='
        </div>
      </div>
    </div>';
		}
		return $html;
	}
  try{
    $Id2 = 0;
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt=$conmy->prepare("select id, nombre, cli_nombre, estado from tblinforme where id=:Id AND cliid=:Cliid;");
    $stmt->execute(array(':Id'=>$ID, ':Cliid'=>$CLI_ID));
    $row=$stmt->fetch();

    if (is_numeric($ID) && $ID > 0){
      if($row && $row['estado'] !=3){
        $isAuthorized = true;
        $Id2 = $row['id'];
        $Nombre = $row['nombre'];
        $Estado = $row['estado'];
        $ClienteNombre = $row['cli_nombre'];
        $claseHabilitado = "btn-outline-primary";
        $atributoHabilitado = ""; 
      }
    }
    if($Id2 > 0){
      $stmt2 = $conmy->prepare("select id, infid, ownid, orden, tipo, actividad, diagnostico, trabajos, observaciones from tbldetalleinforme where infid=:InfId and tipo='act' order by orden");
      $stmt2->bindParam(':InfId', $ID, PDO::PARAM_INT);
      $stmt2->execute();
      $actividades = $stmt2->fetchAll(PDO::FETCH_ASSOC);

      if(count($actividades) > 0){
        $arbol = construirArbol($actividades);
        $ids = array_map(function($elemento) {
          return $elemento['id'];
          }, $actividades);
          $cadenaIds = implode(',', $ids);
          $imagenes=array();

          $stmt3 = $conmy->prepare("select id, refid, tabla, nombre, titulo, descripcion, tipo from tblarchivos where refid IN(".$cadenaIds.") and tabla=:Tabla and tipo=:Tipo;");				
          $stmt3->execute(array(':Tabla'=>'INFD', ':Tipo'=>'IMG'));
          while($row3=$stmt3->fetch(PDO::FETCH_ASSOC)){
            $imagenes[$row3['refid']][]=array(
              'id'=>(int)$row3['id'],
              'refid'=>$row3['refid'],
              'tabla'=>$row3['tabla'],
              'nombre'=>$row3['nombre'],
              'titulo'=>$row3['titulo'],
              'descripcion'=>$row3['descripcion'],
              'tipo'=>$row3['tipo']  
            );
          }
          $tablaHTML.='<div class="accordion" id="accordion-container">';
            $tabla=FnGenerarInformeHtmlAcordeon($arbol, $imagenes);
            $tablaHTML .=$tabla;
          $tablaHTML.='</div>';
        }
    }
  } catch (PDOException $ex) {
    $errorMessage = $ex->getMessage();
    $conmy = null;
  } catch (Exception $ex) {
    $errorMessage = $ex->getMessage();
    $conmy = null;
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Editar Actividades | GPEM S.A.C</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">

    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/SweetAlert2/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mycloud/library/select-gpem-1.0/css/select-gpem-1.0.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css"> 
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
    <style>
      /* .contenedor-imagen{ display: grid; grid-template-columns:1fr 1fr; gap:5px;} */
      @media(min-width:768px){.contenedor-imagen{display: grid; grid-template-columns:1fr 1fr; gap:15px;}}
      .accordion .accordion-item { border: none; }
      .accordion .accordion-header { border: none; }
      .accordion .accordion-body { border: none; padding:0}
      .contenedor-actividades{ display:grid; background-color:white !important; }
      @media(max-width:767px){.input-group--mod{background-color:white !important; }} .grid-icono{ grid-row: 1 / 2; place-self: end }
      @media(min-width:768px){ .contenedor-actividades{ grid-template-columns: 6fr 1fr;} .grid-icono{ grid-column: 2 / 3; place-self: center }} 
      .cabecera-actividad{ border: 0.5px solid #9b9b9b59; }
      @media(min-width:768px){ .cabecera-actividad--mod{ border: 0.5px solid #9b9b9b59;} .cabecera-actividad{ border: none; } } .imagen-ajustada { width: 100%; height: 200px; object-fit: contain; } .btn-desactivado { color: grey; pointer-events: none; opacity: 0.5; }
    </style>
</head>
<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>
	
  <div class="container section-top">
    <div class="row mb-3">
      <div class="col-12 btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnListarInformes(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Informes</span></button>
        <button type="button" class="btn btn-outline-primary fw-bold <?php echo $claseHabilitado;?> <?php echo $atributoHabilitado;?>" onclick="FnResumenInforme(); return false;"><i class="fas fa-desktop"></i><span class="d-none d-sm-block"> Resúmen</span></button>
      </div>
    </div>
    <div class="row border-bottom mb-3 fs-5">
      <div class="col-12 fw-bold d-flex justify-content-between">
        <p class="m-0 p-0 text-secondary"><?php echo $isAuthorized ? $_SESSION['gesman']['CliNombre'] : 'UNKNOWN'; ?></p>
        <input type="hidden" id="txtInformeId" value="<?php echo $ID; ?>"/>
        <p class="m-0 p-0 text-center text-secondary"><?php echo $isAuthorized ? $Nombre : 'UNKNOWN'; ?></p>
      </div>
    </div>
    <?php if ($isAuthorized): ?>  
      <div class="row">
        <div class="col-12">
          <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInforme.php?id=<?php echo ($ID); ?>" class="text-decoration-none">INFORME</a></li>
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeEquipo.php?id=<?php echo ($ID); ?>" class="text-decoration-none">EQUIPO</a></li>
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeResumen.php?id=<?php echo ($ID); ?>" class="text-decoration-none">RESUMEN</a></li>
              <li class="breadcrumb-item active fw-bold" aria-current="page">ACTIVIDAD</li>
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeAnexo.php?id=<?php echo ($ID); ?>" class="text-decoration-none">ANEXOS</a></li>
            </ol>
          </nav>
        </div>
      </div>
      <div class="row mb-3 border-bottom">
        <div class="col-12 col-md-3 mb-2">
            <button type="button" class="btn btn-primary form-control fw-bold col-12" data-bs-toggle="modal" data-bs-target="#modalNuevaActividad"><i class="fas fa-plus"></i> ACTIVIDAD</button>
        </div>
      </div>    
      <div class="row">
        <div class="col-12">
          <?php
            echo $tablaHTML;
          ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- AGREGAR ACTIVIDAD - M O D A L -->
    <div class="modal fade" id="modalNuevaActividad" tabindex="-1" aria-labelledby="modalNuevaActividadLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="modalNuevaActividadLabel">ACTIVIDAD</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="txtActividadInfid1" value="<?php echo $ID ?>">
            <div class="row">
              <div class="col-12">
                <label for="txtActividad" class="form-label mb-0">Nombre de la Actividad</label>
                <textarea type="text" name="actividad" class="form-control text-secondary" id="txtActividad1" row=3></textarea>
              </div>
              <div class="col-12 mt-2">
                <label for="txtDiagnostico" class="form-label mb-0">Diagnóstico</label>
                <textarea type="text" name="diagnostico" class="form-control text-secondary" ro=3 id="txtDiagnostico1"></textarea>
              </div>
              <div class="col-12 mt-2">
                <label for="txtTrabajo" class="form-label mb-0">Trabajos</label>
                <textarea type="text" name="trabajo" class="form-control text-secondary" id="txtTrabajo1" row=3></textarea>
              </div>
              <div class="col-12 mt-2">
                <label for="txtObservacion" class="form-label mb-0">Observación</label>
                <textarea type="text" name="observacion" class="form-control text-secondary" id="txtObservacion1" row=3></textarea>
              </div>
              <div class="col-12 mt-2">
                <button id="guardarActividad" class="btn btn-primary pt-2 pb-2 col-12 fw-bold w-100" onclick="FnAgregarInformeActividades()">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" widht="18px" height="23px" x="0px" y="0px" viewBox="0 0 611.923 611.923" xml:space="preserve" style='margin-right:10px'>
                    <g fill="none" stroke="#FFFFFF" stroke-width="20">
                      <path d="M606.157,120.824L489.908,4.575c-2.46-2.46-6.612-4.152-10.764-4.152H434.32H175.988H40.672   C18.222,0.423,0,18.721,0,41.095v529.734c0,22.45,18.298,40.672,40.672,40.672h86.341h368.661h75.577   c22.45,0,40.672-18.299,40.672-40.672V131.665C611.077,128.359,609.463,124.207,606.157,120.824z M419.328,31.177v136.162   c0,0.846-0.846,0.846-0.846,0.846h-42.363V31.177H419.328z M344.596,31.177v137.008H192.595c-0.846,0-0.846-0.846-0.846-0.846   V31.177H344.596z M141.929,580.9V390.688c0-35.674,29.062-64.737,64.737-64.737h208.434c35.674,0,64.737,29.062,64.737,64.737   v190.135H141.929V580.9z M580.401,570.905c0,4.997-4.152,9.995-9.995,9.995h-59.816V390.688c0-52.281-43.209-95.49-95.49-95.49   H207.511c-52.281,0-95.49,43.209-95.49,95.49v190.135H40.595c-4.997,0-9.995-4.152-9.995-9.995V41.095   c0-4.997,4.152-9.995,9.995-9.995h120.401v136.162c0,17.453,14.147,31.523,31.523,31.523h225.886   c17.453,0,31.523-14.147,31.523-31.523V31.177h23.219l107.1,107.1L580.401,570.905L580.401,570.905z M422.634,490.33   c0,8.304-6.612,14.916-14.916,14.916H217.506c-8.304,0-14.916-6.612-14.916-14.916c0-8.303,6.612-14.916,14.916-14.916h189.289   C415.945,475.415,422.634,482.027,422.634,490.33z M422.634,410.678c0,8.303-6.612,14.916-14.916,14.916H217.506   c-8.304,0-14.916-6.612-14.916-14.916s6.612-14.916,14.916-14.916h189.289C415.945,394.84,422.634,401.529,422.634,410.678z"/>
                    </g>
                  </svg> 
                  GUARDAR
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- AGREGAR SUBACTIVIDAD - M O D A L -->
    <div class="modal fade" id="modalNuevaSubActividad" tabindex="-1" aria-labelledby="modalNuevaSubActividadLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="modalNuevaSubActividadLabel">SUBACTIVIDAD</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="txtActividadInfid2" value="<?php echo $ID ?>">
            <div class="row">
              <div class="col-12">
                <label for="txtActividad2" class="form-label mb-0">Nombre de la Actividad</label>
                <textarea type="text" name="actividad" class="form-control text-secondary" id="txtActividad2" row=3></textarea>
              </div>
              <div class="col-12 mt-2">
                <label for="txtDiagnostico2" class="form-label mb-0">Diagnóstico</label>
                <textarea type="text" name="diagnostico" class="form-control text-secondary" ro=3 id="txtDiagnostico2"></textarea>
              </div>
              <div class="col-12 mt-2">
                <label for="txtTrabajo2" class="form-label mb-0">Trabajos</label>
                <textarea type="text" name="trabajo" class="form-control text-secondary" id="txtTrabajo2" row=3></textarea>
              </div>
              <div class="col-12 mt-2">
                <label for="txtObservacion2" class="form-label mb-0">Observación</label>
                <textarea type="text" name="observacion" class="form-control text-secondary" id="txtObservacion2"></textarea>
              </div>
              <div class="col-12 mt-2">
                <button id="guardarSubActividad" class="btn btn-primary fw-bold pt-2 pb-2 col-12" onclick="FnAgregarInformeActividades2()" >
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" widht="18px" height="23px" x="0px" y="0px" viewBox="0 0 611.923 611.923" xml:space="preserve" style='margin-right:10px'>
                    <g fill="none" stroke="#FFFFFF" stroke-width="20">
                      <path d="M606.157,120.824L489.908,4.575c-2.46-2.46-6.612-4.152-10.764-4.152H434.32H175.988H40.672   C18.222,0.423,0,18.721,0,41.095v529.734c0,22.45,18.298,40.672,40.672,40.672h86.341h368.661h75.577   c22.45,0,40.672-18.299,40.672-40.672V131.665C611.077,128.359,609.463,124.207,606.157,120.824z M419.328,31.177v136.162   c0,0.846-0.846,0.846-0.846,0.846h-42.363V31.177H419.328z M344.596,31.177v137.008H192.595c-0.846,0-0.846-0.846-0.846-0.846   V31.177H344.596z M141.929,580.9V390.688c0-35.674,29.062-64.737,64.737-64.737h208.434c35.674,0,64.737,29.062,64.737,64.737   v190.135H141.929V580.9z M580.401,570.905c0,4.997-4.152,9.995-9.995,9.995h-59.816V390.688c0-52.281-43.209-95.49-95.49-95.49   H207.511c-52.281,0-95.49,43.209-95.49,95.49v190.135H40.595c-4.997,0-9.995-4.152-9.995-9.995V41.095   c0-4.997,4.152-9.995,9.995-9.995h120.401v136.162c0,17.453,14.147,31.523,31.523,31.523h225.886   c17.453,0,31.523-14.147,31.523-31.523V31.177h23.219l107.1,107.1L580.401,570.905L580.401,570.905z M422.634,490.33   c0,8.304-6.612,14.916-14.916,14.916H217.506c-8.304,0-14.916-6.612-14.916-14.916c0-8.303,6.612-14.916,14.916-14.916h189.289   C415.945,475.415,422.634,482.027,422.634,490.33z M422.634,410.678c0,8.303-6.612,14.916-14.916,14.916H217.506   c-8.304,0-14.916-6.612-14.916-14.916s6.612-14.916,14.916-14.916h189.289C415.945,394.84,422.634,401.529,422.634,410.678z"/>
                    </g>
                  </svg>
                  GUARDAR
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- START EDITAR ACTIVIDAD - M O D A L -->
    <div class="modal fade" id="modalEditarActividad" tabindex="-1" aria-labelledby="modalEditarActividadLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="modalEditarActividadLabel">EDITAR</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="txtActividadId">
            <div class="row">
              <div class="col-12">
                <label for="txtactividad3" class="form-label mb-0">Nombre de la Actividad</label>
                <textarea type="text" name="actividad" class="form-control text-secondary" id="txtactividad3" row=3></textarea>
              </div>
              <div class="col-12 mt-2">
                <label for="txtDiagnostico3" class="form-label mb-0">Diagnóstico</label>
                <textarea type="text" name="diagnostico" class="form-control text-secondary" ro=3 id="txtDiagnostico3"></textarea>
              </div>
              <div class="col-12 mt-2">
                <label for="txtTrabajo3" class="form-label mb-0">Trabajos</label>
                <textarea type="text" name="trabajo" class="form-control text-secondary" id="txtTrabajo3" row=3></textarea>
              </div>
              <div class="col-12 mt-2">
                <label for="txtObservacion3" class="form-label mb-0">Observación</label>
                <textarea type="text" name="observacion" class="form-control text-secondary" id="txtObservacion3" row=3></textarea>
              </div>
              <div class="col-12 mt-2">
                <button id="editarActividadBtn" class="btn btn-primary fw-bold pt-2 pb-2 col-12" onclick="FnModificarInformeActividades()">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" widht="18px" height="23px" x="0px" y="0px" viewBox="0 0 611.923 611.923" xml:space="preserve" style='margin-right:10px'>
                    <g fill="none" stroke="#FFFFFF" stroke-width="20">
                      <path d="M606.157,120.824L489.908,4.575c-2.46-2.46-6.612-4.152-10.764-4.152H434.32H175.988H40.672   C18.222,0.423,0,18.721,0,41.095v529.734c0,22.45,18.298,40.672,40.672,40.672h86.341h368.661h75.577   c22.45,0,40.672-18.299,40.672-40.672V131.665C611.077,128.359,609.463,124.207,606.157,120.824z M419.328,31.177v136.162   c0,0.846-0.846,0.846-0.846,0.846h-42.363V31.177H419.328z M344.596,31.177v137.008H192.595c-0.846,0-0.846-0.846-0.846-0.846   V31.177H344.596z M141.929,580.9V390.688c0-35.674,29.062-64.737,64.737-64.737h208.434c35.674,0,64.737,29.062,64.737,64.737   v190.135H141.929V580.9z M580.401,570.905c0,4.997-4.152,9.995-9.995,9.995h-59.816V390.688c0-52.281-43.209-95.49-95.49-95.49   H207.511c-52.281,0-95.49,43.209-95.49,95.49v190.135H40.595c-4.997,0-9.995-4.152-9.995-9.995V41.095   c0-4.997,4.152-9.995,9.995-9.995h120.401v136.162c0,17.453,14.147,31.523,31.523,31.523h225.886   c17.453,0,31.523-14.147,31.523-31.523V31.177h23.219l107.1,107.1L580.401,570.905L580.401,570.905z M422.634,490.33   c0,8.304-6.612,14.916-14.916,14.916H217.506c-8.304,0-14.916-6.612-14.916-14.916c0-8.303,6.612-14.916,14.916-14.916h189.289   C415.945,475.415,422.634,482.027,422.634,490.33z M422.634,410.678c0,8.303-6.612,14.916-14.916,14.916H217.506   c-8.304,0-14.916-6.612-14.916-14.916s6.612-14.916,14.916-14.916h189.289C415.945,394.84,422.634,401.529,422.634,410.678z"/>
                    </g>
                  </svg> 
                  GUARDAR
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- START IMAGENES - M O D A L -->
    <div class="modal fade" id="modalAgregarImagen" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-dialog-scrollable ">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="modalAgregarImagenLabel">AGREGAR</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body pb-1">
          <input type="hidden" id="txtActividadOwnid"/>
            <div class="row">
              <div class="col-12 mb-2">
                <label class="form-label mb-0">Título</label>
                <input type="text" class="form-control text-secondary" id="txtTitulo">
              </div>
              <div class="col-12 mb-2">
                <label class="form-label mb-0">Descripción</label>
                <input type="text" class="form-control text-secondary" id="txtDescripcion">
              </div>                        
              <div class="col-12">
                <label for="adjuntarImagenInput" class="form-label mb-0">Imagen</label>
                <input id="fileImagen" type="file" accept="image/*,.pdf" class="form-control mb-2"/>
              </div>
              <div class="col-12 m-0">
                  <div class="col-md-12 text-center" id="divImagen"><i class="fas fa-images fs-2"></i></div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary pt-2 pb-2 col-12 fw-bold" onclick="FnAgregarArchivo(); return false;">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" widht="18px" height="23px" x="0px" y="0px" viewBox="0 0 611.923 611.923" xml:space="preserve" style='margin-right:10px'>
                <g fill="none" stroke="#FFFFFF" stroke-width="20">
                  <path d="M606.157,120.824L489.908,4.575c-2.46-2.46-6.612-4.152-10.764-4.152H434.32H175.988H40.672   C18.222,0.423,0,18.721,0,41.095v529.734c0,22.45,18.298,40.672,40.672,40.672h86.341h368.661h75.577   c22.45,0,40.672-18.299,40.672-40.672V131.665C611.077,128.359,609.463,124.207,606.157,120.824z M419.328,31.177v136.162   c0,0.846-0.846,0.846-0.846,0.846h-42.363V31.177H419.328z M344.596,31.177v137.008H192.595c-0.846,0-0.846-0.846-0.846-0.846   V31.177H344.596z M141.929,580.9V390.688c0-35.674,29.062-64.737,64.737-64.737h208.434c35.674,0,64.737,29.062,64.737,64.737   v190.135H141.929V580.9z M580.401,570.905c0,4.997-4.152,9.995-9.995,9.995h-59.816V390.688c0-52.281-43.209-95.49-95.49-95.49   H207.511c-52.281,0-95.49,43.209-95.49,95.49v190.135H40.595c-4.997,0-9.995-4.152-9.995-9.995V41.095   c0-4.997,4.152-9.995,9.995-9.995h120.401v136.162c0,17.453,14.147,31.523,31.523,31.523h225.886   c17.453,0,31.523-14.147,31.523-31.523V31.177h23.219l107.1,107.1L580.401,570.905L580.401,570.905z M422.634,490.33   c0,8.304-6.612,14.916-14.916,14.916H217.506c-8.304,0-14.916-6.612-14.916-14.916c0-8.303,6.612-14.916,14.916-14.916h189.289   C415.945,475.415,422.634,482.027,422.634,490.33z M422.634,410.678c0,8.303-6.612,14.916-14.916,14.916H217.506   c-8.304,0-14.916-6.612-14.916-14.916s6.612-14.916,14.916-14.916h189.289C415.945,394.84,422.634,401.529,422.634,410.678z"/>
                </g>
              </svg>  
              GUARDAR
            </button>
          </div>
        </div>
      </div>
    </div><!-- END IMAGENES - M O D A L -->

    <!-- MODAL MODIFICAR TITULO-DESCRIPCION -->
    <div class="modal fade" id="modalModificarArchivoTituloDescripcion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-dialog-scrollable ">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="modalModificarArchivoTituloDescripcionLabel">MODIFICAR </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body pb-1">
          <input type="hidden" id="txtArchivoId"/>
            <div class="row">
              <div class="col-12 mb-2">
                <label class="form-label mb-0">Título</label>
                <input type="text" class="form-control text-secondary" id="txtTitulo2">
              </div>
              <div class="col-12 mb-2">
                <label class="form-label mb-0">Descripción</label>
                <input type="text" class="form-control text-secondary" id="txtDescripcion2">
              </div>
              <div class="col-12">
                <label for="fileImagen2" class="form-label mb-0">Imagen</label>
                <input id="fileImagen2" type="file" accept="image/*,.pdf" class="form-control mb-2"/>
              </div>
              <div class="col-12 m-0">
                  <div class="col-md-12 text-center" id="divImagen2"><i class="fas fa-images fs-2"></i></div>
              </div>                        
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary pt-2 pb-2 col-12 fw-bold" onclick="FnModificarArchivo(); return false;">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" widht="18px" height="23px" x="0px" y="0px" viewBox="0 0 611.923 611.923" xml:space="preserve" style='margin-right:10px'>
                <g fill="none" stroke="#FFFFFF" stroke-width="20">
                  <path d="M606.157,120.824L489.908,4.575c-2.46-2.46-6.612-4.152-10.764-4.152H434.32H175.988H40.672   C18.222,0.423,0,18.721,0,41.095v529.734c0,22.45,18.298,40.672,40.672,40.672h86.341h368.661h75.577   c22.45,0,40.672-18.299,40.672-40.672V131.665C611.077,128.359,609.463,124.207,606.157,120.824z M419.328,31.177v136.162   c0,0.846-0.846,0.846-0.846,0.846h-42.363V31.177H419.328z M344.596,31.177v137.008H192.595c-0.846,0-0.846-0.846-0.846-0.846   V31.177H344.596z M141.929,580.9V390.688c0-35.674,29.062-64.737,64.737-64.737h208.434c35.674,0,64.737,29.062,64.737,64.737   v190.135H141.929V580.9z M580.401,570.905c0,4.997-4.152,9.995-9.995,9.995h-59.816V390.688c0-52.281-43.209-95.49-95.49-95.49   H207.511c-52.281,0-95.49,43.209-95.49,95.49v190.135H40.595c-4.997,0-9.995-4.152-9.995-9.995V41.095   c0-4.997,4.152-9.995,9.995-9.995h120.401v136.162c0,17.453,14.147,31.523,31.523,31.523h225.886   c17.453,0,31.523-14.147,31.523-31.523V31.177h23.219l107.1,107.1L580.401,570.905L580.401,570.905z M422.634,490.33   c0,8.304-6.612,14.916-14.916,14.916H217.506c-8.304,0-14.916-6.612-14.916-14.916c0-8.303,6.612-14.916,14.916-14.916h189.289   C415.945,475.415,422.634,482.027,422.634,490.33z M422.634,410.678c0,8.303-6.612,14.916-14.916,14.916H217.506   c-8.304,0-14.916-6.612-14.916-14.916s6.612-14.916,14.916-14.916h189.289C415.945,394.84,422.634,401.529,422.634,410.678z"/>
                </g>
              </svg>  
              GUARDAR
            </button>
          </div>
        </div>
      </div>
    </div><!-- END IMAGENES - M O D A L -->
	</div>

  <div class="container-loader-full">
    <div class="loader-full"></div>
  </div>

  <!-- <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script> -->
  <script src="/mycloud/library/Sortable-1.15.3/Sortable.min.js"></script>
  <script src="/informes/js/EditarInformeActividad.js"></script>
  <script src="/mycloud/library/SweetAlert2/js/sweetalert2.all.min.js"></script>
  <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="/gesman/menu/sidebar.js"></script>
  </body>
</html>