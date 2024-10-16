<?php 
  session_start();
  if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
    header("location:/gesman");
    exit();
  }
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";

  $ID = empty($_GET['id'])?0:$_GET['id'];
  $CLI_ID = $_SESSION['CliId'];
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
			$html.='<div class="accordion-item" id="'.$nodo['id'].'">';
			$html.='
				<div class="accordion-header" id="accordion-header-'.$nodo['id'].'">
          <div class="contenedor-actividades mb-2" style="margin:0 auto; border: 0.5px solid #9b9b9b59">
            <div class="grid-actividad d-flex p-1">
              <span class="text-secondary" style="font-size:17px !important">'.$indiceActual. '</span><span>&nbsp;-</span>
              <div class="accordion-button p-0" data-bs-toggle="collapse" style="cursor:pointer; background-color:white !important; border: unset; box-shadow: none; font-size:17px !important;" data-bs-target="#collapse-accordion-'.$nodo['id'].'" aria-expanded="true" aria-controls="collapse-accordion-'.$contador.'"><span>&nbsp;</span>'.$nodo['actividad'].'</div>
            </div>
            <div class="grid-icono input-grop-icons d-flex p-0">
              <span class="input-group-text bg-white border border-0 text-secondary" style="cursor:pointer;"><i class="fas fa-plus" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar" onclick="FnModalAgregarDetalleInformeSubActividad('.$nodo['id'].')"></i></span>
              <span class="input-group-text bg-white border border-0 text-secondary" style="cursor:pointer;"><i class="fas fa-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" onclick="FnModalModificarDetalleInformeActividad('.$nodo['id'].')"></i></span>
              <span class="input-group-text bg-white border border-0 text-secondary" style="cursor:pointer;"><i class="fas fa-paperclip" data-bs-toggle="tooltip" data-bs-placement="top" title="Archivo" onclick="FnModalAgregarArchivo('.$nodo['id'].')"></i></span>
              <span class="input-group-text bg-white border border-0 text-secondary" style="cursor:pointer;"><i class="fas fa-trash-alt" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar" onclick="FnEliminarDetalleInformeActividad('.$nodo['id'].')"></i></span>
            </div>
          </div>
				</div>
				<div id="collapse-accordion-'.$nodo['id'].'" class="accordion-collapse collapse show" aria-labelledby="accordion-header-'.$nodo['id'].'">
					<div class="accordion-body" style="padding-left:10px !important; padding-right: 10px !important">
						<div class="row mb-3">
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

            <div class="contenedor-imagen mb-4" style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;" id="'.$nodo['id'].'">';
							if(isset($imagenes[$nodo['id']])){
								foreach($imagenes[$nodo['id']] as $elemento){
									$html.='
                <div class="card p-0" id="archivo-'.$elemento['id'].'">
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar" onclick="FnEliminarArchivo('.$elemento['id'].')"></button>
                  <div class="card-header bg-transparent border-success">'.$elemento['titulo'].'</div>
                    <img src="/mycloud/gesman/files/'.$elemento['nombre'].'">
                  <div class="card-footer bg-transparent border-success">'.$elemento['descripcion'].'</div>
                </div>';
              }
            }
        $html.='</div>';
			if (!empty($nodo['hijos'])) {
				$html.='<div class="accordion" id="accordion-container-'.$nodo['id'].'">';
				$html.=FnGenerarInformeHtmlAcordeon($nodo['hijos'], $imagenes, $nivel+1, $indiceActual );
				$html.='</div>';
			}
			$html.='</div>';
			$html.='</div>';
			$html.='</div>';
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
      $stmt2 = $conmy->prepare("select id, ownid, tipo, actividad, diagnostico, trabajos, observaciones from tbldetalleinforme where infid=:InfId and tipo='act';");
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

          $stmt3 = $conmy->prepare("select id, refid, nombre, descripcion, titulo from tblarchivos where refid IN(".$cadenaIds.") and tabla=:Tabla and tipo=:Tipo;");				
          $stmt3->execute(array(':Tabla'=>'INFD', ':Tipo'=>'IMG'));
          while($row3=$stmt3->fetch(PDO::FETCH_ASSOC)){
            $imagenes[$row3['refid']][]=array(
              'id'=>(int)$row3['id'],
              'nombre'=>$row3['nombre'],
              'descripcion'=>$row3['descripcion'],
              'titulo'=>$row3['titulo'],
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
      @media(min-width:992px){.contenedor-imagen{grid-template-columns:1fr 1fr 1fr !important;}}
      @media(min-width:1200px){.contenedor-imagen{grid-template-columns:1fr 1fr 1fr 1fr !important;}}
      .accordion .accordion-item { border: none; }
      .accordion .accordion-header { border: none; }
      .accordion .accordion-body { border: none; padding:0}
      .contenedor-actividades{ display:grid; }
      .grid-icono{ grid-row: 1 / 2; place-self: end }
      @media(min-width:768px){
        .contenedor-actividades{ grid-template-columns: 6fr 1fr;}
        .grid-icono{ grid-column: 2 / 3; place-self: center }
      } 
    </style>
</head>
<body>
  <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>
	
  <div class="container section-top p-0">
    <div class="row mb-3">
      <div class="col-12 btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnListarInformes(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Informes</span></button>
        <button type="button" class="btn btn-outline-primary fw-bold <?php echo $claseHabilitado;?> <?php echo $atributoHabilitado;?>" onclick="FnResumenInforme(); return false;"><i class="fas fa-desktop"></i><span class="d-none d-sm-block"> Resúmen</span></button>
      </div>
    </div>
    <div class="row border-bottom mb-3 fs-5">
      <div class="col-12 fw-bold d-flex justify-content-between">
        <p class="m-0 p-0 text-secondary"><?php echo $isAuthorized ? $_SESSION['CliNombre'] : 'UNKNOWN'; ?></p>
          <input type="hidden" id="txtInformeId" value="<?php echo $ID; ?>" readonly/>
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
      <div class="row mb-1 border-bottom">
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
                <button id="guardarActividad" class="btn btn-primary pt-2 pb-2 col-12 fw-bold w-100" onclick="FnAgregarDetalleInformeTipoActividad()" ><i class="fas fa-save"></i> GUARDAR</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div><!-- GUARDAR ACTIVIDAD - M O D A L -->

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
                <button id="guardarSubActividad" class="btn btn-primary fw-bold pt-2 pb-2 col-12" onclick="FnAgregarDetalleInformeSubActividad()" ><i class="fas fa-save"></i> GUARDAR</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div><!-- END GUARDAR ACTIVIDAD - M O D A L -->

    <!-- START EDITAR ACTIVIDAD - M O D A L -->
    <div class="modal fade" id="modalEditarActividad" tabindex="-1" aria-labelledby="modalEditarActividadLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="modalEditarActividadLabel">EDITAR ACTIVIDAD</h5>
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
                <button id="editarActividadBtn" class="btn btn-primary fw-bold pt-2 pb-2 col-12" onclick="FnModificarDetalleInformeActividad()"><i class="fas fa-save"></i> GUARDAR</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div><!-- END EDITAR ACTIVIDAD - M O D A L -->

    <!-- START IMAGENES - M O D A L -->
    <div class="modal fade" id="modalAgregarImagen" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-dialog-scrollable ">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="modalAgregarImagenLabel">AGREGAR ARCHIVO </h5>
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
            <button type="button" class="btn btn-primary pt-2 pb-2 col-12 fw-bold" onclick="FnAgregarArchivo(); return false;"><i class="fas fa-save"></i>  GUARDAR</button>
          </div>
        </div>
      </div>
    </div><!-- END IMAGENES - M O D A L -->
	</div>

  <div class="container-loader-full">
    <div class="loader-full"></div>
  </div>

</body>
  <script src="/informes/js/EditarInformeActividad.js"></script>
  <script src="/mycloud/library/SweetAlert2/js/sweetalert2.all.min.js"></script>
  <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
  <script src="/gesman/menu/sidebar.js"></script>
</html>