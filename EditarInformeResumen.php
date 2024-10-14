<?php
  session_start();
  if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
    header("location:/gesman");
    exit();
  }

  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";

  $CLI_ID = $_SESSION['CliId'];
  $ID = empty($_GET['id'])?0:$_GET['id'];
  $isAuthorized = false;
  $errorMessage = ''; 
  $datos = array();
  $Estado=0;
  $Nombre='';
  $claseHabilitado = "btn-outline-secondary";
  $atributoHabilitado = " disabled";

  try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (is_numeric($ID) && $ID > 0) {
      $informe = FnBuscarInforme($conmy, $ID, $CLI_ID);
      if ($informe && $informe->Estado !=3) {
        $isAuthorized = true;
        $claseHabilitado = "btn-outline-primary";
        $atributoHabilitado = ""; 
        $Nombre = $informe->Nombre;
        $datos = FnBuscarActividades($conmy, $ID);
        $conclusiones=array();
        $recomendaciones=array();
        $antecedentes=array();
        $analisis=array();

        foreach($datos as $dato){
          if($dato['tipo']=='con'){
            $conclusiones[]=array('actividad'=>$dato['actividad'],'id'=>$dato['id'],'tipo'=>$dato['tipo']);
          }else if($dato['tipo']=='rec'){
            $recomendaciones[]=array('actividad'=>$dato['actividad'],'id'=>$dato['id'],'tipo'=>$dato['tipo']);
          }else if($dato['tipo']=='ant'){
            $antecedentes[]=array('actividad'=>$dato['actividad'],'id'=>$dato['id'],'tipo'=>$dato['tipo']);
          }else if($dato['tipo']=='ana'){
            $analisis[]=array('actividad'=>$dato['actividad'],'id'=>$dato['id'],'tipo'=>$dato['tipo']);
          }	
        }
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

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resumen | GPEM SAC</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/SweetAlert2/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mycloud/library/select-gpem-1.0/css/select-gpem-1.0.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css"> 
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
    <style>
      .input-grop-icons{ display: flex; justify-content: flex-end;}
      .bi-plus-lg::before{ font-weight:bold!important; }
      @media(min-width:1400px){.small{ padding-left: 15px; padding-right: 15px;}}
      #btn-antecedentes:hover, #btn-analisis:hover,#btn-conclusiones:hover, #btn-recomendaciones:hover { color:white !important; }      
    </style>
  </head>
  <body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>
  
    <div class="container section-top mb-4">
      
      <div class="row mb-3">
        <div class="col-12 btn-group p-0" role="group" aria-label="Basic example">
          <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnListarInformes(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Informes</span></button>
          <button type="button" class="btn btn-outline-primary fw-bold <?php echo $claseHabilitado;?> <?php echo $atributoHabilitado;?>" onclick="FnResumenInforme(); return false;"><i class="fas fa-desktop"></i><span class="d-none d-sm-block"> Resúmen</span></button>
        </div>
      </div>

      <div class="row border-bottom mb-3 fs-5">
        <div class="col-12 fw-bold d-flex justify-content-between p-0">
          <p class="m-0 p-0 text-secondary"><?php echo $isAuthorized ? $_SESSION['CliNombre'] : 'UNKNOWN'; ?></p>
          <input type="text" class="d-none" id="txtIdInforme" value="<?php echo $ID; ?>" readonly/>
          <input type="text" class="d-none" id="txtIdtblDetalleInf">
          <p class="m-0 p-0 text-center text-secondary"><?php echo $isAuthorized ? $Nombre : 'UNKNOWN'; ?></p>
        </div>
      </div>

      <?php if ($isAuthorized): ?>
        <div class="row">
        <div class="col-12 p-0">
          <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInforme.php?id=<?php echo $ID ?>" class="text-decoration-none">INFORME</a></li>
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeEquipo.php?id=<?php echo $ID ?>" class="text-decoration-none">EQUIPO</a></li>
              <li class="breadcrumb-item active fw-bold" aria-current="page">RESUMEN</li>
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeActividad.php?id=<?php echo $ID ?>" class="text-decoration-none">ACTIVIDAD</a></li>
              <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeAnexo.php?id=<?php echo $ID ?>" class="text-decoration-none">ANEXOS</a></li>
            </ol>
          </nav>
        </div>
        </div>
        <!--RESUMEN-->
        <div class="row border border-bottom-1 p-2" id="containerActividad">
          <label class="text-secondary fw-bold p-0 d-flex justify-content-between">ACTIVIDAD: <small>EDITAR</small></label>
          <!-- ACTIVIDAD -->
          <div class="mt-1 p-2 d-flex justify-content-between align-items-center border border-1 bg-light">
            <p class="mb-0 text-secondary fw-bold" id="actividadId" style="text-align: justify;"><?php echo $informe->Actividad; ?></p>
            <i class="fas fa-edit text-secondary" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" onclick="fnEditarActividad(<?php echo $informe->Id; ?>)"></i>
          </div>
        </div>      
        <!-- ITEM ANTECEDENTES -->
        <div class="row mt-3">
          <div class="fw-bold d-flex justify-content-between col-8 col-md-10 p-0" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar" data-tipo="ant" onclick="abrirModalAgregar('ant')"><button class="btn btn-outline-primary text-primary p-2 mb-1" style="width:190px; text-align:left;" id="btn-antecedentes"><i class="fas fa-plus" style="margin-right: 5px"></i> ANTECEDENTES</button></div>
          <div class="fw-bold text-secondary d-flex justify-content-center align-items-center col-4 col-md-2 p-0"><small>EDITAR</small><small class="small" style="margin-left:5px !important; margin-right:5px; !important;" >|</small><small>ELIMINAR</small></div>
          <?php foreach ($antecedentes as $antecedente) : ?>
            <div class="row d-flex justify-content-between align-items-center border border-bottom m-0 mb-1" style="padding-left:0px; padding-right:0px;">
              <div class="d-flex col-8 col-md-10";>
                <span class="text-secondary" style="margin-right:10px;">&#x2713</span>
                <p class="mb-0 mb-2 text-secondary" style="margin-bottom: 0 !important;" data-tipo="<?php echo $antecedente['tipo']; ?>" id="antecedenteId" style="text-align: justify;"><?php echo $antecedente['actividad']; ?></p>
              </div>
              <div class="input-grop-icons col-4 col-md-2 d-flex justify-content-around align-items-center">
                <span class="input-group-text bg-white border border-0 text-secondary"><i class="fas fa-edit" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" data-tipo="<?php echo $antecedente['tipo']; ?>" onclick="abrirModalEditar(<?php echo $antecedente['id']; ?>, 'antecedente')"></i></span>
                <span class="input-group-text bg-white border border-0 text-secondary"><i class="fas fa-trash-alt" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar" onclick="abrirModalEliminar(<?php echo $antecedente['id']; ?>)"></i></span>
              </div>
            </div>
          <?php endforeach ?>
        </div>
        <!-- ITEM ANÁLISIS -->
        <div class="row mt-3">
          <div class="fw-bold d-flex justify-content-between col-8 col-md-10 p-0" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar" data-tipo="ana" onclick="abrirModalAgregar('ana')"><button class="btn btn-outline-primary text-primary p-2 mb-1 text-left" style="width:190px; text-align:left;" id="btn-analisis"><i class="fas fa-plus" style="margin-right: 5px"></i> ANÁLISIS</button></div>
          <div class="fw-bold text-secondary d-flex justify-content-center align-items-center col-4 col-md-2 p-0"><small>EDITAR</small><small class="small" style="margin-left:5px !important; margin-right:5px; !important;" >|</small><small>ELIMINAR</small></div>
          <?php foreach ($analisis as $analisi) : ?>
            <div class="row d-flex justify-content-between align-items-center border border-bottom m-0 mb-1" style="padding-left:0px; padding-right:0px;">
              <div class="d-flex col-8 col-md-10";>
                <span class="text-secondary" style="margin-right:10px;">&#x2713</span>
                <p class="mb-0 mb-2 text-secondary" style="margin-bottom: 0 !important;" data-tipo="<?php echo $analisi['tipo']; ?>" id="analisisId" style="text-align: justify;"><?php echo $analisi['actividad']; ?></p>
              </div>
              <div class="input-grop-icons col-4 col-md-2 d-flex justify-content-around align-items-center">
                <span class="input-group-text bg-white border border-0 text-secondary"><i class="fas fa-edit" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" data-tipo="<?php echo $analisi['tipo']; ?>" onclick="abrirModalEditar(<?php echo $analisi['id']; ?>, 'analisis')"></i></span>
                <span class="input-group-text bg-white border border-0 text-secondary"><i class="fas fa-trash-alt" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar" onclick="abrirModalEliminar(<?php echo $analisi['id']; ?>)"></i></span>
              </div>
            </div>
          <?php endforeach ?>
        </div>
        <!-- ITEM CONCLUSION -->
        <div class="row mt-3">
          <div class="fw-bold d-flex justify-content-between col-8 col-md-10 p-0" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar" data-tipo="con" onclick="abrirModalAgregar('con')"><button class="btn btn-outline-primary text-primary p-2 mb-1" style="width:190px; text-align:left;" id="btn-conclusiones"><i class="fas fa-plus" style="margin-right: 5px"></i> CONCLUSIONES</button></div>
          <div class="fw-bold text-secondary d-flex justify-content-center align-items-center col-4 col-md-2 p-0"><small>EDITAR</small><small class="small" style="margin-left:5px !important; margin-right:5px; !important;" >|</small><small>ELIMINAR</small></div>
            <?php foreach ($conclusiones as $conclusion) : ?>
            <div class="row d-flex justify-content-between align-items-center border border-bottom m-0 mb-1" style="padding-left:0px; padding-right:0px;">
              <div class="d-flex col-8 col-md-10">
                <span class="text-secondary" style="margin-right:10px;">&#x2713</span>
                <p class="mb-0 mb-2 text-secondary" style="margin-bottom: 0 !important;" data-tipo="<?php echo $conclusion['tipo']; ?>" id="conclusionId>" style="text-align: justify;"><?php echo $conclusion['actividad']; ?></p>
              </div>
              <div class="input-grop-icons col-4 col-md-2 d-flex justify-content-around align-items-center">
                <span class="input-group-text bg-white border border-0 text-secondary"><i class="fas fa-edit" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" data-tipo="<?php echo $conclusion['tipo']; ?>" onclick="abrirModalEditar(<?php echo $conclusion['id']; ?>, 'conclusion')"></i></span>
                <span class="input-group-text bg-white border border-0 text-secondary"><i class="fas fa-trash-alt" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar" onclick="abrirModalEliminar(<?php echo $conclusion['id']; ?>)"></i></span>
              </div>
            </div>
            <?php endforeach ?>
        </div>
        <!-- ITEM RECOMENDACIÓN -->
        <div class="row mt-3">
          <div class="fw-bold d-flex justify-content-between col-8 col-md-10 p-0" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar" data-tipo="rec" onclick="abrirModalAgregar('rec')"><button class="btn btn-outline-primary text-primary p-2 mb-1" style="width:190px; text-align:left;" id="btn-recomendaciones"><i class="fas fa-plus" style="margin-right: 5px"></i> RECOMENDACIONES</button></div>
          <div class="fw-bold text-secondary d-flex justify-content-center align-items-center col-4 col-md-2 p-0"><small>EDITAR</small><small class="small" style="margin-left:5px !important; margin-right:5px; !important;" >|</small><small>ELIMINAR</small></div>
            <?php foreach ($recomendaciones as $recomendacion) : ?>
              <div class="row d-flex justify-content-between align-items-center border border-bottom m-0 mb-1" style="padding-left:0px; padding-right:0px;">
              <div class="d-flex col-8 col-md-10">
                <span class="text-secondary" style="margin-right:10px;">&#x2713</span>
                <p class="mb-0 mb-2 text-secondary" style="margin-bottom: 0 !important;" data-tipo="<?php echo $recomendacion['tipo']; ?>" id="recomendacionId" style="text-align: justify;"><?php echo $recomendacion['actividad']; ?></p>
              </div>
              <div class="input-grop-icons col-4 col-md-2 d-flex justify-content-around align-items-center">
                <span class="input-group-text bg-white border border-0 text-secondary"><i class="fas fa-edit" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" data-tipo="<?php echo $recomendacion['tipo']; ?>" onclick="abrirModalEditar(<?php echo $recomendacion['id']; ?>, 'recomendacion')"></i></span>
                <span class="input-group-text bg-white border border-0 text-secondary"><i class="fas fa-trash-alt" style="cursor: pointer;" onclick="abrirModalEliminar(<?php echo $recomendacion['id']; ?>)"></i></span>
              </div>
            </div>
            <?php endforeach ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- MODAL EDITAR : ACTIVIDAD -->
    <div class="modal fade" id="modalActividad" tabindex="-1" aria-labelledby="modalGeneralLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fw-bold" id="modalGeneralLabel">MODIFICAR ACTIVIDAD</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formGeneral">
              <textarea type="text" class="form-control text-secondary" id="modalActividadInput" name="actividad" rows="3"><?php echo $informe->Actividad; ?></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="diagnosticoModalInput" name="diagnostico" rows="3"></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="trabajoModalInput" name="trabajos" rows="3"></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="observacionModalInput" name="observaciones" rows="3"></textarea>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary fw-bold" id="modalGuardarBtn" onclick="fnModificarActividadInforme()"><i class="fas fa-save"></i> GUARDAR</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL REGISTRAR : ANTECEDENTE-CONCLUSION-RECOMENDACIÓN -->
    <div class="modal fade" id="agregarActividadModal" tabindex="-1" aria-labelledby="cabeceraRegistrarModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title text-uppercase fw-bold" id="cabeceraRegistrarModal"></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formGeneral">
              <textarea type="text" class="form-control text-secondary " id="registroActividadInput" name="actividad" rows="3"></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="registroDiagnosticoInput" name="diagnostico" rows="3"></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="registroTrabajoInput" name="trabajos" rows="3"></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="registroObservacionInput" name="observaciones" rows="3"></textarea>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary fw-bold" id="modalGuardarBtn" onclick="fnRegistrarActividadDetalle()"><i class="fas fa-save"></i> GUARDAR</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL EDITAR : ANTECEDENTE-CONCLUSION-RECOMENDACIÓN- -->
    <div class="modal fade" id="modalGeneral" tabindex="-1" aria-labelledby="cabeceraModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title text-uppercase fw-bold" id="cabeceraModal"></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formGeneral">
              <textarea type="text" class="form-control text-secondary" id="actividadModalInput" name="actividad" rows="3" placeholder=""></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="diagnosticoModalInput" name="diagnostico" rows="3" placeholder=""></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="trabajoModalInput" name="trabajos" rows="3" placeholder=""></textarea>
              <textarea type="text" class="form-control text-secondary d-none" id="observacionModalInput" name="observaciones" rows="3" placeholder=""></textarea>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary fw-bold" id="modalGuardarBtn" onclick="FnModificarActividad()"><i class="fas fa-save"></i> GUARDAR</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>
    
    <script src="/informes/js/EditarInformeResumen.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/SweetAlert2/js/sweetalert2.all.min.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
  </body>
</html>
