<?php
  session_start();
  if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
    header("location:/gesman");
    exit();
  }
  $ID = empty($_GET['id'])?0:$_GET['id'];
  $CLI_ID = $_SESSION['CliId'];
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/informes/datos/InformesData.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/data/ArchivosData.php";

  $isAuthorized = false;
  $errorMessage = '';
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
        $Nombre = $informe->Nombre;
        $claseHabilitado = "btn-outline-primary";
        $atributoHabilitado = ""; 
        $archivos = FnBuscarArchivos2($conmy, $ID);
      } 
    } else {
      throw new Exception('El ID es inválido.');
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
    <title>Editar Anexos | GPEM S.A.C</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/SweetAlert2/css/sweetalert2.min.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mycloud/library/select-gpem-1.0/css/select-gpem-1.0.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css"> 
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
    <style>
      #canvas{ width:100% }
      .contenedor-anexos{ display:grid; }
      .grid-icono{ grid-row: 1 / 2; place-self: end }
      @media(min-width:768px){ .contenedor-anexos{ grid-template-columns: 6fr 1fr;} .grid-icono{ grid-column: 2 / 3; place-self: center } } 
      .cabecera-anexos{ border: 0.5px solid #9b9b9b59; }
      @media(min-width:768px){ .cabecera-anexos--mod{ border: 0.5px solid #9b9b9b59; } .cabecera-anexos{ border: none; }}
      @media(min-width:992px){.contenedor-imagen{grid-template-columns:1fr 1fr 1fr !important;}}
      @media(min-width:1200px){.contenedor-imagen{grid-template-columns:1fr 1fr 1fr 1fr !important;}}
      @media(min-width:768px){.grid-anexos{display:grid; grid-template-columns: 1fr 1fr; gap: 15px;}}
      @media(min-width:992px){.grid-anexos{gap: 30px;}}
      @media(min-width:1200px){.grid-anexos{grid-template-columns:1fr 1fr 1fr;}}
      .imagen-ajustada {
        width: 100%;
        height: 400px;
        object-fit: contain;
      }
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
          <p class="m-0 p-0 text-secondary"><?php echo $isAuthorized ? $_SESSION['CliNombre'] : 'UNKNOWN'; ?></p>
          <input type="text" class="d-none" id="txtIdInforme" value="<?php echo $ID; ?>" readonly/>
          <p class="m-0 p-0 text-center text-secondary"><?php echo $isAuthorized ? $Nombre : 'UNKNOWN'; ?></p>
        </div>
      </div>

      <?php if ($isAuthorized): ?>
        <div class="row">
          <div class="col-12">
            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
              <ol class="breadcrumb">                        
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInforme.php?id=<?php echo ($ID) ?>" class="text-decoration-none">INFORME</a></li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeEquipo.php?id=<?php echo ($ID) ?>" class="text-decoration-none">EQUIPO</a></li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeResumen.php?id=<?php echo ($ID) ?>" class="text-decoration-none">RESUMEN</a></li>
                <li class="breadcrumb-item fw-bold"><a href="/informes/EditarInformeActividad.php?id=<?php echo ($ID) ?>" class="text-decoration-none">ACTIVIDAD</a></li>
                <li class="breadcrumb-item active fw-bold" aria-current="page">ANEXOS</li>
              </ol>
            </nav>
          </div>
        </div>
        <!-- BOTON AGREGAR ANEXO -->
        <div class="row mb-3 border-bottom">
          <div class="col-12 col-md-3 mb-2">
            <button id="descripcion" class="btn btn-primary form-control fw-bold col-12" onclick="FnModalAgregarArchivo()"><i class="fas fa-plus"></i> ANEXOS</button>
          </div>
        </div>
        <?php $contador=0; ?>
        <div class="mt-4 grid-anexos">
          <input type="hidden" id="txtArchivoId" value="0">
          <?php foreach($archivos as $archivo): ?>
            <?php if ($archivo['tabla'] ==='INFA'): ?>
              <?php $contador+=1; ?>
              <div class="d-flex flex-column">
                <div class="contenedor-anexos mb-2 cabecera-anexos--mod">
                  <div class="bg-light d-flex align-items-center p-1 cabecera-anexos">
                    <div class="text-secondary">ANEXO</div></span><span>&nbsp;- </span><span class="text-secondary" style="font-size:17px !important"><?php echo $contador ?></div>
                    <div class="grid-icono input-grop-icons d-flex p-0">
                      <!--EDITAR-->
                      <span class="bg-light input-group-text border border-0 text-secondary" style="cursor:pointer;" onclick="FnModalModificarArchivo(<?php echo ($archivo['id']); ?>)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 59 64">
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
                      <!--ELIMINAR-->
                      <span class="input-group-text bg-light border border-0 text-secondary" style="cursor:pointer;" onclick="FnEliminarArchivo(<?php echo ($archivo['id'])?>, <?php echo ($archivo['refid']) ?>)">      
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="28" viewBox="0 0 300 343">
                          <title>Eliminar</title>
                          <g fill="none" stroke="#6B6C6E" stroke-width="7">
                            <path d="M86 2.6c-6.2 2.2-11.7 6.4-15.7 12-4.2 6-5.3 10-5.3 19.5 0 4.9-.4 8-1.3 8.7-.7.6-9.3 1.2-21.2 1.4-20 .3-20 .3-25.3 3.2C10.7 51 6.5 55.6 3.1 63 .6 68.2.5 69.3.5 85.5c0 15.7.2 17.3 2.2 21.2 2.9 5.3 8.1 9.3 14.1 10.8l4.7 1.2.5 95.9c.5 104.8.2 98.4 6.3 108 3.2 5.2 11.4 12.7 16.7 15.4 9.5 4.9 11.5 5 104.6 5 58.8 0 89-.4 93-1.1 16.4-3 29.7-15.2 34-31.3 1.1-4.1 1.4-22.6 1.4-98.9V118h2.3c3.4-.1 10.4-3.6 13.3-6.7 4.7-5.1 5.4-8.3 5.4-25.3 0-20.4-1.1-24.2-9.5-32.6-8.5-8.6-11.3-9.3-35-9.7l-19-.2-.6-10c-.4-8.1-1-11-3.1-15-3.1-6-8.6-11.4-14.8-14.6l-4.5-2.4-61-.2c-53.4-.2-61.6 0-65.5 1.3zM211.6 25.4c3.4 3.4 3.5 3.6 3.2 10.8l-.3 7.3-63.7.2L86 44v-7.8c0-8.1 1.1-10.6 5.5-13 1.4-.8 19.4-1.1 58.8-1.2h56.9l3.4 3.4zM257 210.4c0 101 .2 97.6-5.8 103.9-1.5 1.6-4.5 3.9-6.7 5l-4 2.2H59.4l-4.9-2.6c-3.5-1.8-5.9-3.9-8-7.4l-3-4.8-.3-94.4L43 118h214v92.4z"/>
                            <path d="M67.9 142.9l-2.9 2.9v26.4c-.1 95 .1 121.1 1 122.9 2.3 4.2 6.3 5.1 21.1 4.7 13.6-.3 13.9-.3 16.6-3.1l2.8-2.7.3-73.6.2-73.6-3.4-3.4-3.4-3.4H70.8l-2.9 2.9zM96 220v70.1l-10.2-.3-10.3-.3-.3-69.8L75 150h21v70zm35.9-77.1l-2.9 2.9v73.9c.1 56.5.3 74.2 1.3 75.3 3.5 4.5 5.7 5 20 5h13.9l3.4-3.4 3.4-3.4v-72.6c0-78.9.1-76.8-5.5-79.4-1.6-.8-7.9-1.2-16.6-1.2h-14.1l-2.9 2.9zm28.6 77.1v69.5h-21l-.3-69.8-.2-69.8 10.7.3 10.8.3V220zm37.6-78.6c-5.2 2.9-5 .9-5.1 79v72.8l3.4 3.4 3.4 3.4h28.4l3.4-3.4 3.4-3.4v-73.3c0-68-.2-73.6-1.8-75.5-.9-1.2-2.7-2.7-3.9-3.3-3.2-1.6-28.1-1.4-31.2.3zm26.4 78.6v69.5h-21l-.3-68.5c-.1-37.7 0-69.1.3-69.8.3-.9 3.1-1.2 10.7-1l10.3.3V220z"/>
                          </g>
                        </svg>
                      </span>
                    </div>
                  </div>
                  <div class="card p-0 mb-3" style="margin-top:-5px;" id="<?php echo ($archivo['id']); ?>">
                    <div class="card-header bg-transparent text-secondary"><?php echo ($archivo['titulo']); ?></div>
                    <img src="/mycloud/gesman/files/<?php echo ($archivo['nombre']); ?>" class="imagen-ajustada" alt="">
                    <div class="card-footer bg-transparent text-secondary"><?php echo ($archivo['descripcion']); ?></div>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    <!-- AGREGAR ARCHIVO ANEXO -->
    <div class="modal fade" id="modalAgregarAnexo">
      <div class="modal-dialog modal-dialog-scrollable ">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="modalAgregarImagenLabel">AGREGAR ANEXO </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body pb-1">
          <input type="hidden" id="cabeceraIdInput">
            <div class="row">
              <div class="col-12 mb-2">
                <label class="form-label mb-0">Título</label>
                <input type="text" class="form-control text-secondary" id="txtTitulo1">
              </div>
              <div class="col-12 mb-2">
                <label class="form-label mb-0">Descripción</label>
                <input type="text" class="form-control text-secondary" id="txtDescripcion1">
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
          <div id="msjAgregarImagen" class="modal-body pt-1"></div>
          <div class="col-12 modal-footer">
            <button type="button" class="btn btn-primary fw-bold pt-2 pb-2 col-12 w-100" onclick="FnAgregarArchivo();" <?php echo !$isAuthorized ? 'disabled' : ''; ?>><i class="fas fa-save"></i>  GUADAR</button>
          </div>
        </div>
      </div>
    </div>

        <!-- MODIFICAR ARCHIVO ANEXO -->
    <div class="modal fade" id="modalModificarAnexo">
      <div class="modal-dialog modal-dialog-scrollable ">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fs-5 fw-bold" id="modalAgregarImagenLabel">MODICAR ANEXO </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body pb-1">
          <input type="hidden" id="cabeceraIdInput">
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
                <label for="adjuntarImagenInput" class="form-label mb-0">Imagen</label>
                <input id="fileImagen2" type="file" accept="image/*,.pdf" class="form-control mb-2"/>
              </div>
              <div class="col-12 m-0">
                  <div class="col-md-12 text-center" id="divImagen2"><i class="fas fa-images fs-2"></i></div>
              </div>
            </div>
          </div>
          <div id="msjAgregarImagen" class="modal-body pt-1"></div>
          <div class="col-12 modal-footer">
            <button type="button" class="btn btn-primary fw-bold pt-2 pb-2 col-12 w-100" onclick="FnModificarArchivo();" <?php echo !$isAuthorized ? 'disabled' : ''; ?>><i class="fas fa-save"></i>  GUADAR</button>
          </div>
        </div>
      </div>
    </div>

    <div class="container-loader-full">
      <div class="loader-full"></div>
    </div>

    <script src="/informes/js/EditarInformeAnexo.js"></script>
    <script src="/mycloud/library/SweetAlert2/js/sweetalert2.all.min.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
  </body>
</html>
