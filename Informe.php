<?php 
  session_start();
  if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
    header("location:/gesman");
    exit();
  }
  $ID2=0;
  $Nombre='UNKNOWN';
  $ID = empty($_GET['id'])?0:$_GET['id'];
  $CLI_ID = $_SESSION['CliId'];
?>
<?php
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  $isAuthorized = false;
  $errorMessage = ''; 
  $Estado=0;
  $NUMERO=1;
	$tablaHTML ='';
  $actividades = array();
  $conclusiones = array();
  $recomendaciones = array();
  $antecedentes = array();
  $arbol=array();
  $imagenes = array();

  $imagenInformes = array();
  $imagenAnexos = array();

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
  
	function FnGenerarInformeHtmlAcordeon($arbol, $imagenes,$numero, $nivel = 0, $indice ='1') {
		$html='';
		$contador=1;		

		foreach ($arbol as $key=>$nodo) {
			$indiceActual = $nivel==0?$contador++:$indice.'.'.($key+1);
			$html.='
        <div class=" col-12 mb-0 border-bottom bg-light">
          <p class="mt-2 mb-2 fw-bold text-secondary" style="padding-left: 10px;">'.$numero.'.'.$indiceActual.' - '.$nodo['actividad'].'</p>
        </div>
        <div class="row p-1 m-0 ">
          <div class="col-12">
            <p class="m-0 text-secondary fw-bold">Diagnóstico :</p>
            <p class="mb-0 diagnostico text-secondary" style="font-size=15px; text-align:justify;" id="diagnostico-'.$nodo['id'].'">'.$nodo['diagnostico'].'</p>
          </div>
          <div class="col-12">
            <p class="m-0 text-secondary fw-bold">Trabajos :</p>
            <p class="mb-0 trabajo text-secondary" style="font-size=15px; text-align:justify;" id="trabajo-'.$nodo['id'].'">'.$nodo['trabajos'].'</p>
          </div>
          <div class="col-12">
            <p class="m-0 text-secondary fw-bold">Observaciones :</p>
            <p class="mb-0 observacion text-secondary" style="font-size=15px; text-align:justify;" id="observacion-'.$nodo['id'].'">'.$nodo['observaciones'].'</p>
          </div>
          <div class="row m-0 mt-2 mb-2 p-0 d-flex justify-content-center" id="'.$nodo['id'].'">';
            if(isset($imagenes[$nodo['id']])){
              $html.='        
              <div id="carouselImages" class="carousel slide d-md-none" data-bs-interval="false">
                <div class="carousel-inner">';
                  foreach($imagenes[$nodo['id']] as $key => $elemento){
                    $html .= 
                    '<div class="carousel-item '.($key === 0 ? 'active' : '').'">
                      <div class="card text-center p-0" id="archivo-'.$elemento['id'].'">
                        <div class="card-header text-secondary" style="text-align:justify;padding-left:5px;">'.$elemento['titulo'].'</div>
                          <div class="card-body p-0">
                            <img src="/mycloud/gesman/files/'.$elemento['nombre'].'" class="imagen-ajustada" alt="">
                          </div>
                        <div class="card-footer text-secondary" style="text-align:justify;padding-left:5px;">'.$elemento['descripcion'].'</div>
                      </div>
                    </div>';
                  }
                  $html.='
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>

              <!-- Diseño de dos columnas para pantallas grandes -->
              <div class="row d-none d-md-flex">';               
              foreach($imagenes[$nodo['id']] as $elemento){
                $html .= '<div class="col-md-6 mb-2">
                  <div class="card text-center p-0" id="archivo-'.$elemento['id'].'">
                    <div class="card-header text-secondary" style="text-align:justify;padding-left:5px;">'.$elemento['titulo'].'</div>
                    <div class="card-body p-0">
                      <img src="/mycloud/gesman/files/'.$elemento['nombre'].'" class="imagen-ajustada" alt="">
                    </div>
                    <div class="card-footer text-secondary" style="text-align:justify;padding-left:5px;">'.$elemento['descripcion'].'</div>
                  </div>
                </div>';
              }
              $html.='
              </div>';
            }
			    $html.='
          </div>';
			if (!empty($nodo['hijos'])) {
				$html.='<div class=" p-0 hijos">';
				$html.=FnGenerarInformeHtmlAcordeon($nodo['hijos'], $imagenes,$numero, $nivel+1, $indiceActual);
				$html.='</div>';
			}
			$html.='</div>';
		}
		return $html;
	}
    
  try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (is_numeric($ID) && $ID > 0) {
      $informe = FnBuscarInforme($conmy, $ID, $CLI_ID);
      if ($informe && $informe->Estado !=3) {
        $ID2=$informe->Id;
        $isAuthorized = true;
        $Nombre = $informe->Nombre;
        $Estado = $informe->Estado;
        $archivos = FnBuscarArchivos($conmy, $ID);
        $datos = FnBuscarActividades($conmy, $ID);
        if (!empty($datos)) {        
          foreach ($datos as $dato) {
            if ($dato['tipo'] == 'act') {
              $actividades[] = array(
                'id' => $dato['id'],
                'ownid' => $dato['ownid'],
                'tipo' => $dato['tipo'],
                'actividad' => $dato['actividad'],
                'diagnostico' => $dato['diagnostico'],
                'trabajos' => $dato['trabajos'],
                'observaciones' => $dato['observaciones'],
              );
            } else if ($dato['tipo'] == 'con') {
              $conclusiones[] = array('actividad' => $dato['actividad']);
            } else if ($dato['tipo'] == 'rec') {
              $recomendaciones[] = array('actividad' => $dato['actividad']);
            } else if ($dato['tipo'] == 'ant') {
              $antecedentes[] = array('actividad' => $dato['actividad']);
            }    
          };
          foreach ($archivos as $archivo) {
            if ($archivo['tabla'] == "INFE") {
              $imagenInformes[] = array(
                'titulo' => $archivo['titulo'],
                'nombre' => $archivo['nombre'],
                'descripcion' => $archivo['descripcion'],
              ); 
            } else if ($archivo['tabla'] == "INFA") {
              $imagenAnexos[] = array(
                'titulo' => $archivo['titulo'],
                'nombre' => $archivo['nombre'],
                'descripcion' => $archivo['descripcion'],
              );
            }
          };
          $arbol = construirArbol($actividades);
          $ids = array_map(function($elemento) {
            return $elemento['id'];
          }, $actividades);
          
          if (count($ids) > 0) {
            $placeholders = implode(',', $ids);
            $stmt3 = $conmy->prepare("SELECT id, refid, nombre, descripcion, titulo FROM tblarchivos WHERE refid IN ($placeholders) AND tabla=:Tabla AND tipo=:Tipo");
            $stmt3->execute(array(':Tabla'=>'INFD', ':Tipo'=>'IMG'));
            
            while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
              $imagenes[$row3['refid']][] = array(
                'id' => (int)$row3['id'],
                'nombre' => $row3['nombre'],
                'descripcion' => $row3['descripcion'],
                'titulo' => $row3['titulo'],
              );
            }
          }
        }
      }
    }
    $conmy = null;
  } catch (PDOException $ex) {
      $errorMessage = $ex->getMessage();
      $conmy = null;
  } catch (Exception $e) {
      $errorMessage = $e->getMessage();
      $conmy = null;
  }

  $claseHabilitado = "btn-outline-secondary";
  $atributoHabilitado = " disabled";
  if($Estado == 1 || $Estado == 2){
      $claseHabilitado = "btn-outline-primary";
      $atributoHabilitado = "";
  }
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informe | GPEM SAC</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mycloud/library/select-gpem-1.0/css/select-gpem-1.0.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css"> 
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
    <style>
      .hijos p:first-child{ padding-top: 10px;}
      .imagen-ajustada { width: 100%; height: 200px; object-fit: contain;}
      /* .contenedor-imagen{display:grid;grid-template-columns:1fr 1fr !important;column-gap:5px; row-gap: 15px;} */
      @media(min-width:768px){.contenedor-imagen{display:grid;grid-template-columns:1fr 1fr !important; gap:30px !important;} .contenedor-imagenes{gap:30px !important;}}
      @media(min-width:992px){.contenedor-imagen, .contenedor-imagenes{padding:0 50px;}}
      @media(min-width:1200px){.contenedor-imagen, .contenedor-imagenes{padding:0 200px;}}
      .contenedor-imagenes{display: grid; grid-template-columns:1fr 1fr; ;gap:5px;}
      .accordion .accordion-item { border: none; }
      .accordion .accordion-header { border: none; }
      .accordion .accordion-body { border: none; padding:0}
    </style>
  </head>
  <body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>

    <div class="container section-top">
      <div class="row mb-3">
        <div class="col-12 btn-group" role="group" aria-label="Basic example">
          <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnListarInformes(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Informes</span></button>
          <button type="button" class="btn btn-outline-primary fw-bold <?php echo $claseHabilitado;?> <?php echo $atributoHabilitado;?>" onclick="FnEditarInforme(); return false;"><i class="fas fa-edit"></i><span class="d-none d-sm-block"> Editar</span></button>
          <button type="button" class="btn btn-outline-primary fw-bold <?php echo $claseHabilitado;?> <?php echo $atributoHabilitado;?>" onclick="FnModalFinalizarInforme(); return false;"><i class="fas fa-check-square"></i><span class="d-none d-sm-block"> Finalizar</span></button>
          <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnDescargarInforme(); return false;"><i class="fas fa-download"></i><span class="d-none d-sm-block"> Descargar</span></button>
        </div>
      </div>
  
      <div class="row border-bottom mb-2 fs-5">
        <div class="col-12 fw-bold d-flex justify-content-between">
          <p class="m-0 text-secondary"><?php echo $isAuthorized ? $_SESSION['CliNombre'] : 'UNKNOWN'; ?></p>
          <input type="text" class="d-none" id="idInforme" value="<?php echo $ID;?>">
          <p class="m-0 text-secondary"><?php echo $isAuthorized ? $Nombre :'UNKNOWN' ; ?></p>
        </div>
      </div>
      <?php if ($isAuthorized): ?>
        <!-- DATOS GENERALES -->
        <div class="row p-1 mb-2 mt-2">
          <div class="col-12 m-0 border-bottom bg-light" >
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?>- DATOS GENERALES</p>
          </div>
          <div class="row p-1 m-0">
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Nombre</p> 
              <p class="m-0 text-secondary fw-bold"><?php echo  $informe->Nombre  ; ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Fecha</p> 
              <p class="m-0 text-secondary fw-bold"><?php echo   $informe->Fecha  ; ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">OT N°</p> 
              <p class="m-0 text-secondary fw-bold"><?php echo  $informe->OrdNombre  ; ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Cliente:</p> 
              <p class="m-0 text-secondary fw-bold"><?php echo  $informe->CliNombre  ; ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Contacto</p> 
              <p class="m-0 text-secondary fw-bold"><?php echo  $informe->CliContacto  ; ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Dirección</p> 
              <p class="m-0 text-secondary fw-bold"><?php echo  $informe->CliDireccion  ; ?></p>
            </div>
            <div class="col-6 col-sm-8 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Supervisor</p> 
              <p class="m-0 text-secondary fw-bold"><?php echo  $informe->Supervisor  ; ?></p>
            </div>
            <div class="col-6 col-sm-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Estado</p>
              <?php
                switch ($informe->Estado){
                  case 1:
                    echo "<span class='badge bg-danger'>Anulado</span>";
                    break;
                  case 2:
                    echo "<span class='badge bg-primary'>Abierto</span>";
                    break;
                  case 3:
                    echo "<span class='badge bg-success'>Cerrado</span>";
                    break;
                  default:
                    echo "<span class='badge bg-secondary'>Unknown</span>";
                }
              ?>
            </div>
          </div>
        </div>
        <?php $NUMERO+=1; ?>
        <!-- DATOS DEL EQUIPO -->
        <div class="row p-1 mb-2 mt-2">
          <div class="col-12 mb-0 border-bottom bg-light">
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?>- DATOS DEL EQUIPO</p>
          </div>
        </div>
        <div class="row p-1 m-0">
          <div class="col-6 col-sm-4 col-lg-4 mb-1">
            <p class="m-0 text-secondary" style="font-size: 15px;">Activo</p>
            <p class="m-0 text-secondary fw-bold"><?php echo  $informe->EquCodigo;?></p>              
          </div>
          <div class="col-6 col-sm-4 col-lg-4 mb-1">
            <p class="m-0 text-secondary" style="font-size: 15px;">Nombre Equipo</p>
            <p class="m-0 text-secondary fw-bold"><?php echo  $informe->EquNombre  ; ?></p>              
          </div>
          <div class="col-6 col-sm-4 col-lg-4 mb-1">
            <p class="m-0 text-secondary" style="font-size: 15px;">Modelo Equipo</p> 
            <p class="m-0 text-secondary fw-bold"><?php echo  $informe->EquModelo   ?></p>
          </div>
          <div class="col-6 col-sm-4 col-lg-4 mb-1">
            <p class="m-0 text-secondary" style="font-size: 15px;">Serie Equipo</p> 
            <p class="m-0 text-secondary fw-bold"><?php echo  $informe->EquSerie  ; ?></p>
          </div>
          <div class="col-6 col-sm-4 col-lg-4 mb-1">
            <p class="m-0 text-secondary " style="font-size: 15px;">Marca Equipo</p> 
            <p class="m-0 text-secondary fw-bold"><?php echo  $informe->EquMarca  ; ?></p>
          </div>
          <div class="col-6 col-sm-4 col-lg-4 mb-1">
            <p class="m-0 text-secondary" style="font-size: 15px;">Kilometraje</p> 
            <p class="m-0 text-secondary fw-bold"><?php echo  $informe->EquKm  ; ?></p>
          </div>
          <div class="col-6 col-sm-4 col-lg-4 mb-1">
            <p class="m-0 text-secondary" style="font-size: 15px;">Horas Motor</p> 
            <p class="m-0 text-secondary fw-bold"><?php echo  $informe->EquHm ; ?></p>
          </div>
          <div class="col-12 col-lg-6 mb-1">
            <p class="m-0 text-secondary" style="font-size: 15px;">Caraterísticas</p> 
            <p class="m-0 text-secondary fw-bold"><?php echo  $informe->EquDatos  ; ?></p>
          </div>
        </div>
        <!-- Carrusel para pantallas pequeñas -->
        <div id="carouselExample" class="carousel slide d-md-none" data-bs-ride="carousel" data-bs-interval="false">
          <div class="carousel-inner">
            <?php foreach($imagenInformes as $key => $imagenInforme): ?>
              <div class="carousel-item <?php echo $key === 0 ? 'active' : ''; ?>">
                <div class="card text-center p-0">
                  <div class="card-header text-secondary" style="text-align:justify;padding-left:5px; line-height: 1.2"><?php echo ($imagenInforme['titulo']); ?></div>
                  <div class="card-body p-0">
                    <img src="/mycloud/gesman/files/<?php echo empty($imagenInforme['nombre']) ? '0.jpg' : $imagenInforme['nombre'] ?>" class="imagen-ajustada" alt="">
                  </div>
                  <div class="card-footer text-secondary" style="text-align:justify;padding-left:5px;"><?php echo ($imagenInforme['descripcion']); ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
        <!-- Diseño de dos columnas para pantallas grandes -->
        <div class="row d-none d-md-flex">
          <?php foreach($imagenInformes as $imagenInforme): ?>
            <div class="col-md-6 mb-2">
              <div class="card text-center p-0">
                <div class="card-header text-secondary" style="text-align:justify;padding-left:5px; line-height: 1.2"><?php echo ($imagenInforme['titulo']); ?></div>
                <div class="card-body p-0">
                  <img src="/mycloud/gesman/files/<?php echo empty($imagenInforme['nombre']) ? '0.jpg' : $imagenInforme['nombre'] ?>" class="imagen-ajustada" alt="">
                </div>
                <div class="card-footer text-secondary" style="text-align:justify;padding-left:5px;"><?php echo ($imagenInforme['descripcion']); ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php $NUMERO+=1; ?>
        <!-- SOLICITUD DEL CLIENTE -->
        <div class="row p-1 mb-2 mt-2">
          <div class="col-12 mb-0 border-bottom bg-light">
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?>- SOLICITUD DEL CLIENTE</p>
          </div>
          <div class="row p-1 m-0">
            <div class="col-12 mb-2 mt-2">
              <p class="m-0" style="text-align: justify;"><?php echo  $informe->Actividad  ; ?></p>          
            </div>
          </div>
        </div>
        <?php $NUMERO+=1; ?>

        <!-- ANTECEDENTES-->
        <div class="row p-1 mb-2 mt-2">
          <div class="col-12 mb-0 border-bottom bg-light">
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?>- ANTECEDENTES</p>
          </div>
          <div class="row p-1 m-0">
            <?php foreach($antecedentes as $antecedente) :?>
                <div class="d-flex">
                  <span class="text-secondary" style="margin-right:10px;">&#x2713</span>
                  <p class="m-0 mb-2 p-0 text-secondary" style="text-align: justify;"><?php echo $antecedente['actividad'];?></p>
                </div>
            <?php endforeach ;?>
          </div>
        </div>
        <?php $NUMERO+=1; ?>
    
        <!-- ACTIVIDADES -->
        <div class="row p-1 mb-2 mt-2">
          <div class="col-12 mb-0 border-bottom bg-light">
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?>- ACTIVIDADES</p>
          </div>
            <?php
              $html = FnGenerarInformeHtmlAcordeon($arbol, $imagenes,$NUMERO);
              echo $html;
            ?>
        </div>
        <?php $NUMERO+=1; ?>

        <!-- CONCLUSIONES -->
        <div class="row p-1 mb-2 mt-2">
          <div class="col-12 mb-0 border-bottom bg-light">
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?>- CONCLUSIONES</p>
          </div>
          <div class="row p-1 m-0">
            <?php foreach($conclusiones as $conclusion) :?>
              <div class="d-flex">
                <span class="text-secondary" style="margin-right:10px;">&#x2713</span>
                <p class="m-0 mb-2 p-0" style="text-align: justify;"><?php echo $conclusion['actividad'];?></p>
              </div>
            <?php endforeach ;?>
          </div>
        </div>
        <?php $NUMERO+=1; ?>

        <!-- RECOMENDACIONES -->
        <div class="row p-1 mb-2 mt-2">
          <div class="col-12 mb-0 border-bottom bg-light">
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?>- RECOMENDACIONES</p>
          </div>
          <div class="row p-1 m-0">
            <?php foreach($recomendaciones as $recomendacion) :?>
              <div class="d-flex">
                <span class="text-secondary" style="margin-right:10px;">&#x2713</span> 
                <p class="m-0 mb-2 p-0" style="text-align: justify;"><?php echo $recomendacion['actividad'];?></p>
              </div>
            <?php endforeach ;?>
          </div>
        </div>
        <?php $NUMERO+=1; ?>

        <!-- ANEXOS -->
        <div class="row p-1 mb-2 mt-2">
          <div class="col-12 mb-0 border-bottom bg-light">
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?>- ANEXOS</p>
          </div>
          <div class="row p-1 m-0">
            <div class="mt-2 mb-2 p-1">
              <?php foreach($imagenAnexos as $imagenAnexo): ?>
                <div class="text-center border border-1 p-0">
                  <p class="m-0 bg-light text-secondary pt-1 pb-2" style="text-align:justify;padding-left:5px;"><?php echo ($imagenAnexo['titulo']); ?></p>
                  <img src="/mycloud/gesman/files/<?php echo empty($imagenAnexo['nombre']) ? '0.jpg' : $imagenAnexo['nombre'] ?>" class="" alt="">
                  <p class="m-0 bg-light text-secondary pt-1 pb-2" style="text-align:justify;padding-left:5px;"><?php echo ($imagenAnexo['descripcion']); ?></p>
                </div>
              <?php endforeach; ?>
            </div>            
          </div>
        </div>

        <div class="modal fade" id="modalFinalizarInforme" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Finalizar Informe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>                
              <div class="modal-body pb-1">
                <div class="row text-center fw-bold pt-3">                        
                  <p class="text-center">Para finalizar el Informe <?php echo $Nombre;?> haga clic en el botón CONFIRMAR.</p>                    
                </div>
              </div>
              <div class="modal-body pt-1" id="msjFinalizarInforme"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="FnFinalizarInforme(); return false;">CONFIRMAR</button>
              </div>              
            </div>
          </div>
        </div>
      <?php endif ?>
    </div>

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>
    
    <script src="/informes/js/Informe.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
  </body>
    <?php if ($errorMessage): ?>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo addslashes($errorMessage); ?>',
            timer: 1000,
          });
        });
      </script>
    <?php endif; ?>
</html>